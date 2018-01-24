<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class PlanPay extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'PlanPay';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = '接收保证金';

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
		$now_date = date('Y-m-d H:i:s');

		$page = 1;
		$pay = new Pay('HLBPay');
		$pay->pay();

		$sys = DB::table('xyk_sys')->first();
		$fee = DB::table('xyk_fee')->first();
		$plan_sys = DB::table('xyk_plan_sys')->first();

		if (!$plan_sys) {
			$this->info("plan_sys no");exit();
		}
		if (!$fee) {
			$this->info('no fee');
			exit();
		}

		if (!$sys) {
			$this->info('no mac ip');
			exit();
		}
		while (true) {
			try {
				$limit = ($page - 1) * 100;
				$take = 100;
				$plans = Plan::where('status', 0)->where('StartDate', '<=', $now_date)->where('EndDate', '>=', $now_date)->skip($limit)->take(100)->get(); 
				$page++;
				if (!$plans->count()) {
					$this->info('data empty');
					exit();
				}

				foreach ($plans as $plan) {
					// 保证金卡收取 收取公式 保证金 加 计划手续费 加 系统手续费
					if ($plan->PayBankId) {
						try {
							DB::beginTransaction();
							$bank_card = BankdCard::where('Id', $plan->PayBankId)->first();
							if (!$bank_card) {
								throw new Exception("没有找到支付卡");
							}
							
							// 保证金 加 计划手续费 加 系统手续费
							$money = $plan->CashDeposit + $plan->SysFee + $plan->fee;

							$pay_params = array(
								'hlb_bindId' => $bank_card->CreditId,
								'user_id' => $plan->UserId,
								'money' => $money,
								'goods_name' => '保证金',
								'goods_desc' => '保证金',
								'server_mac' => $sys->mac,
								'server_ip'  => $sys->ip,
								'callback_url' => '',
							);
							$pay->setParams($pay_params);
							// 生成账单 默认失败 交易卡带商户
					    	$bill_id = Bill::createBill(array(
								'CreditId' => $bank_card->Id,
								'UserId' => $pay_params['user_id'],
								'money' => $plan->CashDeposit, // 不含手续
								'Type' => 5, // 保证金收取
								'bank_number' => $bank_card->CreditNumber,
								'OrderNum' => $pay->getOrderId(),
								'feeType' => '',
								'TableId' => $plan->Id,
								'SysFee' => $plan->fee + $plan->SysFee,
								'From' => '交易卡',
								'To' => '商户',
							));

					    	// $pay->sendRequest();
					    	// $result = $pay->getResult();

					    	// 测试
					    	$result = array(
					    		'result' => array(
					    			'rt9_orderStatus' => 'DOING',
 					    		),
					    	);
					    	if (empty($result['result'])) {
					    		continue;
					    	}

					    	if ($result['result']['rt9_orderStatus'] == 'SUCCESS') {
					    		// 保证金收取完成 准备开始
					    		Plan::where('Id', $plan->Id)->update(array('status'=> 1));
					    		Bill::billUpdate($bill_id, 'SUCCESS');
					    	} elseif ($result['result']['rt9_orderStatus'] == 'DOING' || $result['result']['rt9_orderStatus'] == 'INIT') {
					    		// 保证金收取中
					    		Bill::billUpdate($bill_id, 'DOING');
					    		// 判断 分销
					    		if ($plan_sys->OpenPlanProfit) {
									Profit::doProfit($plan->UserId, $plan->CashDeposit);
								}
					    		Plan::where('Id', $plan->Id)->update(array('status'=> 4));	
					    	} else {
					    		$this->info('pay fail planid:'. $plan->Id);
					    	}

					    	DB::commit();
						} catch (Exception $e) {
							DB::rollback();
							$this->info('pay fail planid:'. $plan->Id. 'Exception:'. $e->getMessage());
							Plan::where('Id', $plan->Id)->update(array('status'=> 5, 'res'=> $e->getMessage()));
						}
						

					} else {
						// 取余额
						try {
							DB::beginTransaction();
							$user = User::where('UserId', $plan->UserId)->get();
							
							// 收取费用 保证金 加 计划手续费 加 系统手续费
							$money = $plan->CashDeposit + $plan->fee + $plan->SysFee;
							// 余额不足
							if ($user->Account < $money) {
								Plan::where('Id', $plan->Id)->update(array('status'=> 5, 'res'=> '用户余额不足，不够扣除保证金，计划执行失败'));
								DB::commit();
								continue;
							}

							// 生成账单 默认失败  余额到商户
					    	$bill_id = Bill::createBill(array(
								'UserId' => $plan->UserId,
								'money' => $plan->CashDeposit, // 不含手续
								'Type' => 5, // 保证金收取
								'bank_number' => $bank_card->CreditNumber,
								'OrderNum' => '',
								'feeType' => '',
								'TableId' => $plan->Id,
								'SysFee' => $plan->fee + $plan->SysFee,
								'From' => '余额',
								'To' => '商户',
							));
							// 用余额 直接成功
					    	Bill::billUpdate($bill_id, 'SUCCESS');
							//扣除余额 ， 修改计划准备中
							User::where('UserId', $plan->UserId)->decrement('Account', (float)$money);

							// 判断 分销
				    		if ($plan_sys->OpenPlanProfit) {
								Profit::doProfit($plan->UserId, $money);
							}
							Plan::where('Id', $plan->Id)->update(array('status'=> 1));

							DB::commit();
						} catch (Exception $e) {
							DB::rollback();
							Plan::where('Id', $plan->Id)->update(array('status'=> 5, 'res'=> $e->getMessage()));
						}	
					}

					

				}

			} catch (Exception $e) {
				
			}
		}
		
		

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
