<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class JnPayRepay extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'JnPayRepay';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = '捷牛还款计划执行任务';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$way_res = DB::table('xyk_paytemplate')->first();
		$way = $way_res->TemplateId;
		if($way){
			$this->jnfire();exit;
		}

		$now_date = date('Y-m-d H:i:s');
		$pay = new Pay('HLBPay');

		$plan_sys = DB::table('xyk_plan_sys')->first();

		if (!$plan_sys) {
			$this->info("plan_sys no");exit();
		}

		$page = 1;
		while (true) {
			$take = 100;
			$limit = ($page - 1) * $take;
			
			$plans = Plan::where('StartDate', '<=', $now_date)->where('EndDate', '>=', $now_date)->where('status', 1)->skip($limit)->take($take)->get();

			if ($plans->count() == 0) {
				$this->info('empty data');
				exit();
			}

			$page ++ ;
			foreach ($plans as $plan)
		    {	
		    	// 读取计划详情
		    	$plan_details = PlanDetail::where('PlanId', $plan->Id)->orderBy('sort', 'asc')->get();
		    	$bank_card = BankdCard::where('UserId', $plan->UserId)->where('Id', $plan->BankId)->where('status', 0)->first();
		    	if (!$bank_card) {
		    		Plan::where('Id', $plan->Id)->update(array('status'=> 5, 'res'=> '没有找到银行卡'));
		    		continue;
		    	}
		    	$continue_batch = 0;
		    	foreach ($plan_details as $key => $value) {
		  
		    		if ($value->status == 1) {
		    			continue; # 已处理的跳过
		    		}

		    		// 时间未到 不做处理
		    		if (strtotime($value->PayTime) > time()) {
		    			continue;
		    		}

		    		if ($value->Type == 1) {

		    			if ($value->status == 2) {
		    				$continue_batch = $value->Batch;
			    			continue; # 处理中状态
			    		}

		    			// 还款
		    			try {
		    				$repay_params = array(
					    		'user_id' => $plan->UserId,
								'hlb_bindId' => $bank_card->CreditId,
								'money' => (float)$value->Money,
								'feeType' => 'PAYER', // RECEIVER 收款方 自己      PAYER 付款方  用户
								'remark' => '',
					    	);

					    	$pay->repay();
					    	$pay->setParams($repay_params);
					    	// 生成账单 默认失败 商户到交易卡
					    	$bill_id = Bill::createBill(array(
								'CreditId' => $bank_card->Id,
								'UserId' => $repay_params['user_id'],
								'money' => $repay_params['money'], // 不含手续
								'Type' => 7, // 计划还款
								'bank_number' => $bank_card->CreditNumber,
								'OrderNum' => $pay->getOrderId(),
								'feeType' => $repay_params['feeType'],
								'TableId' => $plan->Id,
								'SysFee' => $value->SysFee,
								'From' => '商户',
								'To' => '交易卡',
							));

					    	// 修改计划为带查询
					    	PlanDetail::where('Id', $value->Id)->update(array(
					    		'OrderNum' => $pay->getOrderId(),
					    		'status' => 2,
					    	));

					    	$pay->sendRequest();

					    	$result = $pay->getResult();

							if ($result['action'] != 1) { throw new Exception($result['msg'], $result['code']);}

							if ($result['code'] == '0001') {
								// 待查询  默认计划详情是待查询
								Bill::billUpdate($bill_id, 'DOING');
								$this->info('planID:'. $plan->Id.', batch：'.$value->Batch.', hk doing');
							} else {
								// 成功
								// 还款成功	
								Bill::billUpdate($bill_id, 'SUCCESS');
								PlanDetail::where('Id', $value->Id)->update(array('status'=> 1));
								$this->info('planID:'. $plan->Id.', batch：'.$value->Batch.', hk success');
							}

							
							
							$continue_batch = 0; # 用来做判断 批次号跟跳过批次号相同的 不处理
		    			} catch (Exception $e) {
		    				PlanDetail::where('Id', $value->Id)->update(array('status'=> 0));
		    				$this->info('planID:'. $plan->Id.', batch：'.$value->Batch.', code: '.$e->getCode().' hk faile，'.$e->getMessage());
		    				$continue_batch = $value->Batch;
		    			}
		    		} else if ($value->Type == 2) {
		    			if ($value->status == 2) {
			    			continue; # 处理中状态
			    		}
		    			// 消费 如果还款为成功 一直跳过
		    			if ($continue_batch == $value->Batch) {
		    				$this->info('planID:'. $plan->Id.', batch：'.$value->Batch.', hkfail continue');
		    				continue;
		    			}
		    			try {
		    				$sys = DB::table('xyk_sys')->first();

							if (!$sys) {
								throw new Exception("mac null", 3003);
							}
							// 消费
			    			$pay_params = array(
			    				'hlb_bindId' => $bank_card->CreditId,
								'user_id' => $plan->UserId,
								'money' => (float)$value->Money,
								'goods_name' => '消费',
								'goods_desc' => '消费',
								'server_mac' => $sys->mac,
								'server_ip'	 => $sys->ip,
								'callback_url' => '', // backurl
								// 'validateCode' => $this->data['validateCode'],
			    			);
			    			$pay->pay();
							$pay->setParams($pay_params);
							// 生成账单 默认失败  交易卡 到 商户
					    	$bill_id = Bill::createBill(array(
								'CreditId' => $bank_card->Id,
								'UserId' => $pay_params['user_id'],
								'money' => $pay_params['money'], // 不含手续废
								'Type' => 4, // 还款消费
								'bank_number' => $bank_card->CreditNumber,
								'OrderNum' => $pay->getOrderId(),
								'feeType' => '',
								'TableId' => $plan->Id,
								'SysFee' => $value->SysFee,
								'From' => '交易卡',
								'To' => '商户',
							));

					    	// 修改计划
					    	PlanDetail::where('Id', $value->Id)->update(array(
					    		'OrderNum' => $pay->getOrderId(),
					    		'status' => 2, // 执行中
					    	));

					    	$pay->sendRequest();
							$result = $pay->getResult();
							// 测试
					    	// $result = array(
					    	// 	'action' => 1,
					    	// 	'result' => array(
					    	// 		'rt9_orderStatus' => 'SUCCESS',
					    	// 	),
					    	// );
							// if ($result['action'] != 1) { throw new Exception($result['msg'], $result['code']);}
							if ($result['code'] == '8000') {
								Bill::billUpdate($bill_id, 'DOING'); //  账单修改为处理中
								// 计划修改为处理中
								PlanDetail::where('Id', $value->Id)->update(array('status'=> 2));
								$this->info('planID:'. $plan->Id. ', sign error hkpay');
							}

							if ($result['result']['rt9_orderStatus'] == 'DOING' || $result['result']['rt9_orderStatus'] == 'INIT') {
								Bill::billUpdate($bill_id, 'DOING'); //  账单修改为处理中
								// 计划修改为处理中
								PlanDetail::where('Id', $value->Id)->update(array('status'=> 2));
								$this->info('planID:'. $plan->Id.', batch：'.$value->Batch.', hkpay doing');
							} else {
								Bill::billUpdate($bill_id, 'SUCCESS'); // 成功
								// 分销 判断
								if ($plan_sys->OpenPlanProfit) {
									Profit::doProfit($plan->UserId, $value->Money);
								}
								
								PlanDetail::where('Id', $value->Id)->update(array('status'=> 1));
								$this->info('planID:'. $plan->Id.', batch：'.$value->Batch.', hkpay success');
							}

		    			} catch (Exception $e) {
		    				PlanDetail::where('Id', $value->Id)->update(array('status'=> 0));
		    				$this->info('planID:'. $plan->Id.', batch :'.$value->Batch.', hkpay fail,'.$e->getMessage());
		    			}
		    		}


		    	}

		    	// 计划详情执行完  检查是否全部是 1 是 修改计划为完成
		    	$pld = PlanDetail::where('status', '<>', 1)->where('PlanId', $plan->Id)->first();

		    	if (!$pld) {
		    		Plan::where('Id', $plan->Id)->update(array('status'=> 2));
		    	}

		    }

		}
	    

	

		$this->info('end');
	}


	/**
	 * 捷牛支付
	 */
	public function jnfire(){
		$now_date = date('Y-m-d H:i:s');

		$plan_sys = DB::table('xyk_plan_sys')->first();

		if (!$plan_sys) {
			$this->info("plan_sys no");exit();
		}

		$page = 1;
		while (true) {
			$take = 100;
			$limit = ($page - 1) * $take;

			$plans = Plan::where('StartDate', '<=', $now_date)->where('EndDate', '>=', $now_date)->where('status', 1)->skip($limit)->take($take)->get();

			if ($plans->count() == 0) {
				$this->info('empty data');
				exit();
			}

			$page ++ ;
			foreach ($plans as $plan)
			{
				// 读取计划详情
				$plan_details = PlanDetail::where('PlanId', $plan->Id)->orderBy('sort', 'asc')->get();
				$bank_card = BankdCard::where('UserId', $plan->UserId)->where('Id', $plan->BankId)->where('status', 0)->first();
				$user_contact = DB::table('xyk_usercontact')->where('UserId', $plan->UserId)->first();

				if (!$bank_card) {
					Plan::where('Id', $plan->Id)->update(array('status'=> 5, 'res'=> '没有找到银行卡'));
					continue;
				}
				$continue_batch = 0;
				foreach ($plan_details as $key => $value) {

					if ($value->status == 1) {
						continue; # 已处理的跳过
					}

					// 时间未到 不做处理
					if (strtotime($value->PayTime) > time()) {
						continue;
					}

					if ($value->Type == 1) {

						if ($value->status == 2) {
							$continue_batch = $value->Batch;
							continue; # 处理中状态
						}

						// 还款
						try {
							$repay_params = array(
								'user_id' => $plan->UserId,
								'hlb_bindId' => $bank_card->CreditId,
								'money' => (float)$value->Money,
								'feeType' => 'PAYER', // RECEIVER 收款方 自己      PAYER 付款方  用户
								'remark' => '',
							);

							// 生成账单 默认失败 商户到交易卡
							Bill::createBill(array(
								'CreditId' => $bank_card->Id,
								'UserId' => $repay_params['user_id'],
								'money' => $repay_params['money'], // 不含手续
								'Type' => 7, // 计划还款
								'bank_number' => $bank_card->CreditNumber,
								'OrderNum' => date("YmdHis") . rand(1000, 9999),
								'feeType' => $repay_params['feeType'],
								'TableId' => $plan->Id,
								'SysFee' => $value->SysFee,
								'From' => '商户',
								'To' => '交易卡',
							));

							// 修改计划为带查询
							PlanDetail::where('Id', $value->Id)->update(array(
								'OrderNum' => date("YmdHis") . rand(1000, 9999),
								'status' => 2,
							));


							$data['linkId'] = date("YmdHis") . rand(1000, 9999);
							$data['payType'] = 1;  //支付类型
							$data['orderType'] = 10;  //结算类型
							$data['amount'] = $repay_params['money'];  //结算到账金额
							$data['bankNo'] = $bank_card->CreditNumber;  //结算银行卡号
							$data['bankAccount'] = $bank_card->CreditNumber;  //结算银行账户
							$data['bankPhone'] = $this->user->Mobile;  //绑定手机号码
							$data['bankCert'] = $user_contact->CertNo;  //身份证号
							$data['bankName'] = $bank_card->CreditName;  //银行名称
							$data['bankCode'] = '';  //银行支行联行号
							$data['notifyUrl'] = 'http://api.wlxkd.com/pay/plancb';  //支付结果回调地址

							$JieniuPay = new JieniuPay('SdkSettleMcg');
							$JieniuPay->merchantInit();
							$JieniuPay->postUrl($data);

							$continue_batch = 0; # 用来做判断 批次号跟跳过批次号相同的 不处理
						} catch (Exception $e) {
							PlanDetail::where('Id', $value->Id)->update(array('status'=> 0));
							$this->info('planID:'. $plan->Id.', batch：'.$value->Batch.', code: '.$e->getCode().' hk faile，'.$e->getMessage());
							$continue_batch = $value->Batch;
						}
					}

				}

				// 计划详情执行完  检查是否全部是 1 是 修改计划为完成
				$pld = PlanDetail::where('status', '<>', 1)->where('PlanId', $plan->Id)->first();

				if (!$pld) {
					Plan::where('Id', $plan->Id)->update(array('status'=> 2));
				}

			}

		}

		$this->info('end');
	}


	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			// array('example', InputArgument::REQUIRED, 'An example argument.'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			// array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
	}

}
