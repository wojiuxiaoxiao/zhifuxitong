<?php 

/**
* 
*/
class PayController extends BaseController
{	

	function postBankcode()
	{	
	
		try {
			
			if (!$this->user) {
				throw new Exception("请先登录", '1000');
			}

			if (!$this->IdCard) {
				throw new Exception("请先实名认证", '1010');
			}

			if (!isset($this->data['bank_number'])) {
				throw new Exception("银行卡号必填", '8020');
			}

			$params = array(
				'user_id' => $this->user->UserId,
				'bank_number'=> $this->data['bank_number'],
				'user_phone' => $this->user->Mobile,
			);

			// $params = array(
			// 	'user_id' => '82',
			// 	'bank_number'=> '6225768758046880',
			// 	'user_phone'=> '18329042977',
			// );

			$pay = new Pay('HLBPay');
			$pay->getBankValideteCode(); //  绑卡短信
			$pay->setParams($params);

			$pay->sendRequest();
			
			$result = $pay->getResult();
		
			if ($result['action'] != 1) { throw new Exception($result['msg'], $result['code']);}

			return $this->cbc_encode(json_encode(array('code'=> '200', 'msg'=> '发送成功!')));

		} catch (Exception $e) {
			
			return $this->cbc_encode(json_encode(array('code'=> $e->getCode(), 'msg'=> '失败！错误代码：'.$e->getCode().','.$e->getMessage())));
		}
		


	}
	// 绑卡
	function postBankbind()
	{	


		/*
		需要参数

			bank_number
			bank_year 选填 信用卡必填
			bank_month 选填 信用卡必填
			cvv2 选填 信用卡必填
			user_phone
			validateCode

			quota 选填
			account_date 选填
			repayment_date 选填
		*/

		try {

			if (!$this->user) {
				throw new Exception("请先登录", '1000');
			}

			if (!$this->IdCard) {
				throw new Exception("请先实名认证", '1010');
			}

			if (!isset($this->data['bank_number'])) {
				throw new Exception("银行卡号必填", '8020');
			}

			if (!isset($this->data['account_date'])) {
				throw new Exception("账单日必填", '8020');
			}

			if (!isset($this->data['repayment_date'])) {
				throw new Exception("还款日必填", '8020');
			}

			$params = array(
				'user_id' => $this->user->UserId,
				'user_name' => $this->user->Username,
				'id_card_number' => $this->IdCard,
				'user_phone' => $this->user->Mobile,
				'bank_number' => $this->data['bank_number'],
				'validateCode' => $this->data['validateCode'],
				'account_date' => $this->data['account_date'],
				'repayment_date' => $this->data['repayment_date'],
				'bank_year' => '',
				'bank_month' => '',
				'cvv2' => '',
				'quota' => 0,
			);

			// 测试数据
			// $params = array(
			// 	'user_id' => '82',
			// 	'user_name' => '陈文越',
			// 	'id_card_number' => '330327199312022158',
			// 	'bank_number' => '6225768758046880',
			// 	'user_phone'=> '18329042977',
			// 	'validateCode' => '838306',
			// 	'account_date' => '2017-11-28 10:00:00',
			// 	'repayment_date' => '2017-11-29 23:59:59',
			// );
			// $this->data['bank_year'] = '20';
			// $this->data['bank_month'] = '11';
			// $this->data['cvv2'] = '449';
			// $this->data['quota'] = '15000';


			isset($this->data['bank_year']) && $params['bank_year'] = $this->data['bank_year'];
			isset($this->data['bank_month']) && $params['bank_month'] = $this->data['bank_month'];
			isset($this->data['cvv2']) && $params['cvv2'] = $this->data['cvv2'];
			isset($this->data['quota']) && $params['quota'] = $this->data['quota'];

			if (isset($this->data['bank_year'])) {
				// 贷记卡 信用卡
				$type = 2;
			} else {
				// 借记卡 银行卡
				$type = 1;
			}

			if ($type == 2) {

				if (!isset($this->data['account_date']) || !isset($this->data['repayment_date'])) {
					throw new Exception("绑定信用卡必须填还款日 账单日", 8991);
				}

			}
			$pay = new Pay('HLBPay');
			$pay->bankBind(); // 绑卡
			$pay->setParams($params);
			$pay->sendRequest();
			
			$result = $pay->getResult();

			$data=json_encode($result, JSON_UNESCAPED_UNICODE);
			$up_dir=dirname(dirname(__FILE__)).'/log/bk/';
			$filename=$this->user->UserId.'-'.time().'-'.round(10,99).'.txt';
			$file=$up_dir.$filename;
			
			if(!is_readable($up_dir))
			{	
				is_dir($up_dir) or mkdir($up_dir,0700);
			}
			file_put_contents($file,$data,FILE_APPEND);


			if ($result['action'] != 1) { throw new Exception($result['msg'], $result['code']);}

			if ($result['result']['rt7_bindStatus'] == 'FAIL') {
				throw new Exception('绑卡失败，请重试', 8999);
			}

			if ($result['result']['rt7_bindStatus'] == 'SUCCESS') {
				try {
					

					$bank_card_m = new BankdCard();

					$card_data = array(
						'bindId' => $result['result']['rt10_bindId'],
						'bank_number' => $params['bank_number'],
						'cvv2' => $params['cvv2'],
						'quota' => $params['quota'],
						'type' => $type,
						'bankId' => $result['result']['rt8_bankId'],
						'yn' => $params['bank_year'].'/'.$params['bank_month'],
						'cvv2' => $params['cvv2'],
					);

					isset($this->data['account_date']) && $card_data['account_date'] = $params['account_date'];
					isset($this->data['repayment_date']) && $card_data['repayment_date'] = $params['repayment_date'];
					$bank_card_m->addUserCard($params['user_id'], $card_data);

					return $this->cbc_encode(json_encode(array('code'=> 200, 'msg'=> '添加成功')));

				} catch (Exception $e) {
					throw new Exception('数据库错误:'.$e->getMessage(), 8997);
				}
			}	

		} catch (Exception $e) {
			return $this->cbc_encode(json_encode(array('code'=> $e->getCode(), 'msg'=> '失败：错误代码：'.$e->getCode().','.$e->getMessage())));
		}
		
	}
	// 结算卡绑定
	function postSettlebind()
	{
		try {
			
			if (!$this->user) {
				throw new Exception("请先登录", '1000');
			}

			if (!$this->IdCard) {
				throw new Exception("请先实名认证", '1010');
			}

			$params = array(
				'user_id' => $this->user->UserId,
				'user_name' => $this->user->Username,
				'id_card_number' => $this->IdCard,
				'bank_number' => $this->data['bank_number'],
				'user_phone' => $this->user->Mobile,
				'operate' => 'ADD',
			);



			// $params = array(
			// 	'user_id' => '82',
			// 	'user_name' => '陈文越',
			// 	'id_card_number' => '330327199312022158',
			// 	'bank_number' => '6217710804856110',
			// 	'user_phone' => '18329042977',
			// );
			
			$user_bank_card = BankcCard::where('UserId', $params['user_id'])->first();
			if ($user_bank_card) {
				$params['operate'] = "UPDATE";
			}

			$pay = new Pay('HLBPay');

			$pay->settleBind();
			$pay->setParams($params);
			$pay->sendRequest();

			$result = $pay->getResult();
		
			if ($result['action'] != 1) { throw new Exception($result['msg'], $result['code']);}
			$card_data = $result['result'];
			$bank = DB::table('xyk_bankcard')->where('BankCode', $card_data['rt8_bankId'])->first();

			// 如果不存在 
			if (!$bank) {
				$bank = new stdClass();
				$bank->BankName = '';
			}
			
			if ($user_bank_card) {
				BankcCard::where('Id', $user_bank_card->Id)->delete();
			}
			$is_has = BankcCard::where('BankNumber', $params['bank_number'])->first();
			if ($is_has) {
				BankcCard::where('Id', $is_has->Id)->delete();
			}

			$card = array(
				'BankId' => $card_data['rt10_bindId'],
				'UserId' => $card_data['rt5_userId'],
				'BankName' => $bank->BankName,
				'BankNumber' => $params['bank_number'],
				'status' => 0,
				'AddTime' => time(),
			);

			BankcCard::insert($card);
			return $this->cbc_encode(json_encode(array('code'=> '200', 'msg'=> '绑卡成功')));
		} catch (Exception $e) {
			return $this->cbc_encode(json_encode(array('code'=> $e->getCode(), 'msg'=> '错误代码：'.$e->getCode().','.$e->getMessage())));
		}
	}

	// 结算卡提现 账单不含手续
	function postSettle()
	{
		try {

			if (!isset($this->data['pay_password']) || $this->data['pay_password'] == '') {
				throw new Exception("支付密码 必填", 0);	
			}
			$pay_password = $this->data['pay_password'];
			$pay_password = md5($pay_password);
			if ($pay_password != $this->user->PayPassword) {
				throw new Exception("支付密码 错误", 0);	
			}
			$bank_card = BankcCard::where('UserId', $this->user->UserId)->where('Id', $this->data['bank_id'])->first();

			$fee = DB::table('xyk_fee')->first();
			if (!$fee) {
				throw new Exception("商家未设置费率", 3001);
			}
			if (!$bank_card) {
				throw new Exception("没有找到该卡", 8996);
			}

			if ($bank_card->status != 0) {
				throw new Exception("该卡被系统冻结", 8896);
			}

			$money = (float)$this->data['money'];
			$y_money = (float)$this->data['money'];
			$user = User::where('UserId', $this->user->UserId)->first();

			if ($user->Account < $y_money) {
				throw new Exception('余额不足，请先去"个人"进行"充值余额"', 1003);
			}

			if ($money < 1) {
				throw new Exception("提现金额不能小于1", 1004);
			}
			
			// 费率扣除
			//$settle_fee = $money * $fee->SettleFee / 100;
			//$settle_fee = round($settle_fee, 2);
			$settle_fee = $fee->SettleFee;

			$money = round($money - $settle_fee,2);
			$params = array(
				'user_id' => $this->user->UserId,
				'money' => $money,
				'feeType' => 'PAYER', // PAYER 商户  RECEIVER 用户
				'remark' => '',
				'hlb_bindId' => $bank_card->BankId,
			);

			// $params = array(
			// 	'user_id' => '82',
			// 	'money' => '1.00',
			// 	'feeType' => 'RECEIVER',
			// 	'remark' => '',
			// 	'hlb_bindId' => '8a6019b556ad4cf7a79a61d388989a68',
			// );

			$pay = new Pay('HLBPay');
			// 提现 到借记卡
			$pay->settle();
			$pay->setParams($params);

			
			// 生成账单 余额 到 结算卡
			$bill_id = Bill::createBill(array(
				'BankId' => $bank_card->Id,
				'UserId' => $params['user_id'],
				'money' => $y_money, // 不含手续废
				'Type' => 2, // 提现
				'bank_number' => $bank_card->BankNumber,
				'OrderNum' => $pay->getOrderId(),
				'feeType' => $params['feeType'],
				'SysFee' => $settle_fee,
				'From' => '余额',
				'To' => '结算卡',
				'ApiFee' => $fee->ApiFee,
			));

			// 先扣款
			User::where('UserId', $this->user->UserId)->decrement('Account', (float)$y_money);
			$pay->sendRequest();
			$result = $pay->getResult();
			
			$data=json_encode($result, JSON_UNESCAPED_UNICODE);
			$up_dir=dirname(dirname(__FILE__)).'/log/settle/';
			$filename=$this->user->UserId.'-'.time().'-'.round(10,99).'.txt';
			$file=$up_dir.$filename;
			if(!is_readable($up_dir))
			{	
				is_dir($up_dir) or mkdir($up_dir,0700);
			}
			file_put_contents($file,$data,FILE_APPEND);	
			
			
			//print_R($result);exit;
			DB::beginTransaction();
			if ($result['action'] != 1) {
				Bill::billUpdate($bill_id, 'FAIL');
				// 失败 加回余额
				User::where('UserId', $this->user->UserId)->increment('Account', (float)$y_money);
				DB::commit();
				throw new Exception('错误代码：'.$result['code'].','.$result['msg'], $result['code']);
			}

			if ($result['code'] == '0001') {
				// 带查询
				Bill::billUpdate($bill_id, 'DOING');
				DB::commit();
				throw new Exception("提现处理中", 0);				
			} else {
				// 成功
				Bill::billUpdate($bill_id, 'SUCCESS');
			}
			DB::commit();
			return $this->cbc_encode(json_encode(array('code'=> '200', 'msg'=> '提现成功!')));
		} catch (Exception $e) {
			DB::rollback();
			return $this->cbc_encode(json_encode(array('code'=> (string)$e->getCode(), 'msg'=> $e->getMessage())));
		}
	}

	// 支付  通过银行卡还款 账单不含手续
	function postPay()
	{
		try {

			// 测试数据
			// $this->data['bank_id'] = 1;
			// $this->data['money'] = '1.00';
			$this->data['goods_name'] = '充值';
			$this->data['goods_desc'] = '充值';
			// $this->data['validateCode'] = '184216';
			// $user = new stdClass();
			// $user->UserId = 82;
			// $this->user = $user;
			
			if (!isset($this->data['pay_password']) || $this->data['pay_password'] == '') {
				throw new Exception("支付密码 必填", 0);
			}
			$pay_password = $this->data['pay_password'];
			$pay_password = md5($pay_password);
			if ($pay_password != $this->user->PayPassword) {
				throw new Exception("支付密码 错误", 0);	
			}

			$money = (float)$this->data['money'];

			$bank_card = BankdCard::where('UserId', $this->user->UserId)->where('Id', $this->data['bank_id'])->first();
			$sys = DB::table('xyk_sys')->first();
			$fee = DB::table('xyk_fee')->first();
			if (!$fee) {
				throw new Exception("商家未设置费率", 3001);
			}
			if (!$sys) {
				throw new Exception("商户未配置mac地址", 3003);
			}

			if (!$bank_card) {
				throw new Exception("没有找到该卡", 8996);
			}

			if ($bank_card->status != 0) {
				throw new Exception("该卡被系统冻结", 8896);
			}

			if (!is_numeric($money)) {
				throw new Exception("金额必须为数字", 8995);
			}

			$pay_fee = $money * $fee->PayFee / 100;
			$pay_fee = round($pay_fee, 2);

			if ($money < 1) {
				throw new Exception("金额过低，最小金额1", 8995);
			}
			
			$params = array(
				'hlb_bindId' => $bank_card->CreditId,
				'user_id' => $this->user->UserId,
				'money' => $money,
				'goods_name' => $this->data['goods_name'],
				'goods_desc' => $this->data['goods_desc'],
				'server_mac' => $sys->mac,
				'server_ip'	 => $sys->ip,
				'callback_url' => '', // backurl
				// 'validateCode' => $this->data['validateCode'],
			);

			$pay = new Pay('HLBPay');
			$pay->pay(); // 支付
			$pay->setParams($params);

			// 生成账单 交易卡 到 余额
			$bill_id = Bill::createBill(array(
				'CreditId' => $bank_card->Id,
				'UserId' => $params['user_id'],
				'money' => $params['money'],
				'Type' => 1, //  充值
				'bank_number' => $bank_card->CreditNumber,
				'OrderNum' => $pay->getOrderId(),
				'feeType' => '', // 绑卡支付没有手续费
				'SysFee' => $pay_fee,
				'From' => '交易卡',
				'To' => '余额',
			));

			$pay->sendRequest();
			
			$result = $pay->getResult();
			
			$data=json_encode($result, JSON_UNESCAPED_UNICODE);
			$up_dir=dirname(dirname(__FILE__)).'/log/pay/';
			$filename=$this->user->UserId.'-'.time().'-'.round(10,99).'.txt';
			$file=$up_dir.$filename;
			if(!is_readable($up_dir))
			{	
				is_dir($up_dir) or mkdir($up_dir,0700);
			}
			file_put_contents($file,$data,FILE_APPEND);			
			
			
			// print_r($result);exit;
			// 测试
			// $result_str = '{"rt10_bindId":"48cfb204ba8b4a3f870ea4c567399272","sign":"7cb7efb4a36ffb9468da7699b56c299f","rt1_bizType":"QuickPayBindPay","rt9_orderStatus":"SUCCESS","rt6_serialNumber":"QUICKPAY171207123745PPFQ","rt14_userId":"82","rt2_retCode":"0000","rt12_onlineCardType":"CREDIT","rt11_bankId":"CMBCHINA","rt13_cardAfterFour":"6880","rt5_orderId":"20171207123745976700","rt4_customerNumber":"C1800001108","rt8_orderAmount":"1.00","rt3_retMsg":"成功","rt7_completeDate":"2017-12-07 12:37:49"}';
			// $result = json_decode($result_str, 1);
			// $res = $pay->getResult($result);
			// print_r($res);exit;

			// if ($result['action'] != 1) { throw new Exception($result['msg'], $result['code']);}
			// 验签不通过
			if ($result['code'] == '8000') {
				throw new Exception($result['msg'], $result['code']);
			}

			
			if ($result['result']['rt9_orderStatus'] == 'DOING' || $result['result']['rt9_orderStatus'] == 'INIT') {
				Bill::billUpdate($bill_id, 'DOING'); //  账单修改为处理中
				throw new Exception($result['msg'], $result['code']);
			} else if ($result['result']['rt9_orderStatus'] == 'SUCCESS') {
				Bill::billUpdate($bill_id, 'SUCCESS'); // 成功 
				// 添加余额 扣除手续废
				$d_money = $money - $pay_fee;
				Profit::doProfit($this->user->UserId, $money);
				User::where('UserId', $this->user->UserId)->increment('Account', (float)$d_money);
			} else {
				Bill::billUpdate($bill_id, 'FAIL');
				throw new Exception($result['msg'], $result['code']);
			}

			
			// 成功数据测试
			// $result = '{"rt10_bindId":"48cfb204ba8b4a3f870ea4c567399272","sign":"e50f22d892b279a4cd1e9137cc08def0","rt1_bizType":"QuickPayBindPay","rt9_orderStatus":"SUCCESS","rt6_serialNumber":"QUICKPAY1712041013326IF6","rt14_userId":"82","rt2_retCode":"0000","rt12_onlineCardType":"CREDIT","rt11_bankId":"CMBCHINA","rt13_cardAfterFour":"6880","rt5_orderId":"20171204101332499782","rt4_customerNumber":"C1800001108","rt8_orderAmount":"1.00","rt3_retMsg":"成功","rt7_completeDate":"2017-12-04 10:13:36"}';
			// $result = json_decode($result, 1);
			

			return $this->cbc_encode(json_encode(array('code'=> '200', 'msg'=> '支付请求已发送')));

		} catch (Exception $e) {
			return $this->cbc_encode(json_encode(array('code'=> 0, 'msg'=> '错误代码：'.$e->getCode().','.$e->getMessage())));
		}
		
	}

	// 废弃
	// 验证支付成功 普通处理方式
	function getDopay()
	{
		try {
			DB::beginTransaction();
			$rt_result = Input::all();
			$pay = new Pay('HLBPay');
			$result = $pay->getResult($rt_result);

			if ($result['action'] != 1) { throw new Exception($result['msg'], $result['code']);}

			// 获取账单 id
			$bill_detail = BillDetail::where('OrderNum', $result['result']['rt5_orderId'])->first();
			if (!$bill_detail) {
				throw new Exception("没有找到账单", 2001);
			}
			$bill = Bill::where('Id', $bill_detail->BillId)->first();

			if ($bill->status != 2) {
				throw new Exception("账单已完成", 2002);
			}

			Bill::where('Id', $bill->Id)->update(array('status'=> 1));

			// 添加余额
			User::where('UserId', $bill->UserId)->increment('Account', (float)$result['result']['rt8_orderAmount']);


			DB::commit();
			echo  'success';exit;
		} catch (Exception $e) {
			DB::rallback();
			echo $e->getMessage().PHP_EOL;
		}
	}
	// 支付短信  接口文档完成
	function getPaycode()
	{
		try {

			// $this->data['bank_id'] = 1;
			// $this->data['money'] = '1.00';
			// $user = new stdClass();
			// $user->UserId = 82;
			// $user->Moblie = '18329042977';
			// $this->user = $user;

			$bank_card = BankdCard::where('UserId', $this->user->UserId)->where('Id', $this->data['bank_id'])->first();

			if (!$bank_card) {
				throw new Exception("没有找到该卡", 8996);
			}

			$money = (float)$this->data['money'];

			if ($money < 1) {
				throw new Exception("金额过低，最小金额1", 8995);
			}

			
			$params = array(
				'hlb_bindId' => $bank_card->CreditId,
				'user_id'	=> $this->user->UserId,
				'money'	=> $money,
				'user_phone' => $this->user->Mobile,
			);


			$pay = new Pay('HLBPay');
			$pay->payCode(); // 支付短信
			$pay->setParams($params);
			$pay->sendRequest();
			
			$result = $pay->getResult();

			if ($result['action'] != 1) { throw new Exception($result['msg'], $result['code']);}
		
			return $this->cbc_encode(json_encode(array('code'=> '200', 'msg'=> '发送成功!')));
		} catch (Exception $e) {
			return $this->cbc_encode(json_encode(array('code'=> $e->getCode(), 'msg'=> '错误代码：'.$e->getCode().','.$e->getMessage())));
		}
		
	}

	// 信用卡还款 账单不含手续
	function postRepay()
	{
		try {
			if (!isset($this->data['pay_password']) || $this->data['pay_password'] == '') {
				throw new Exception("支付密码 必填", 0);	
			}
			$pay_password = $this->data['pay_password'];
			$pay_password = md5($pay_password);
			if ($pay_password != $this->user->PayPassword) {
				throw new Exception("支付密码 错误", 0);	
			}

			$bank_card = BankdCard::where('UserId', $this->user->UserId)->where('Id', $this->data['bank_id'])->first();
			$fee = DB::table('xyk_fee')->first();
			if (!$fee) {
				throw new Exception("商家未设置费率", 3001);
			}

			if (!$bank_card) {
				throw new Exception("没有找到该卡", 8996);
			}

			if ($bank_card->Type != 2) {
				throw new Exception("还款必须是信用卡", 8970);	
			}
			
			
			$money = (float)$this->data['money'];
			$y_money = (float)$this->data['money'];


			
			$repay_fee = $fee->ApiFee;
			
			$money = round($money - $repay_fee,2);
			$params = array(
				'user_id' => $this->user->UserId,
				'hlb_bindId' => $bank_card->CreditId,
				'money' => $money,
				'feeType' => 'PAYER', // RECEIVER 收款方 用户      PAYER 付款方  商户
				'remark' => '',
			);
			$pay = new Pay('HLBPay');
			$pay->repay();
			$pay->setParams($params);

			// 先扣除余额
			User::where('UserId', $this->user->UserId)->decrement('Account', $y_money);
			// 生成账单 余额 到 交易卡
			$bill_id = Bill::createBill(array(
				'CreditId' => $bank_card->Id,
				'UserId' => $params['user_id'],
				'money' => $y_money, // 不含手续废
				'Type' => 3, // 还款
				'bank_number' => $bank_card->CreditNumber,
				'OrderNum' => $pay->getOrderId(),
				'feeType' => $params['feeType'],
				'SysFee' => $repay_fee,
				'From' => '余额',
				'To' => '交易卡',
				'ApiFee' => $fee->ApiFee,
			));
			DB::beginTransaction();
			$pay->sendRequest();

			$result = $pay->getResult();
			
			$data=json_encode($result, JSON_UNESCAPED_UNICODE);
			$up_dir=dirname(dirname(__FILE__)).'/log/repay/';
			$filename=$this->user->UserId.'-'.time().'-'.round(10,99).'.txt';
			$file=$up_dir.$filename;
			if(!is_readable($up_dir))
			{	
				is_dir($up_dir) or mkdir($up_dir,0700);
			}
			file_put_contents($file,$data,FILE_APPEND);	
			

			if ($result['action'] != 1) {
				Bill::billUpdate($bill_id, 'FAIL');
				// 还回余额
				User::where('UserId', $this->user->UserId)->increment('Account', $y_money);
				DB::commit();
				throw new Exception($result['msg'], $result['code']);
			}

			// 还款成功	
			if ($result['code'] == '0001') {
				// 待查询
				Bill::billUpdate($bill_id, 'DOING');
				DB::commit();
				throw new Exception("还款处理中", 0);			
			} else {
				// 成功
				Bill::billUpdate($bill_id, 'SUCCESS');
			}

			// 还款成功 
			DB::commit();
			return $this->cbc_encode(json_encode(array('code'=> '200', 'msg'=> '还款成功!')));

		} catch (Exception $e) {
			DB::rollback();
			return $this->cbc_encode(json_encode(array('code'=> $e->getCode(), 'msg'=> '错误代码：'.$e->getCode().','.$e->getMessage())));		
		}
		

	}

	function getBandlist()
	{

		try {
			$params = array(
				'user_id' => $this->user->UserId,
				'hlb_bindId' => '' # 选填 不填为空
			);

			$pay = new Pay('HLBPay');
			$pay->bankList();
			$pay->setParams($params);
			$pay->sendRequest();

			$result = $pay->getResult();

			if ($result['action'] != 1) { throw new Exception($result['msg'], $result['code']);}

			print_r($result);

		} catch (Exception $e) {
			return json_encode(array('code'=> $e->getCode(), 'msg'=> '错误代码：'.$e->getCode().','.$e->getMessage()));
		}
		

	}

	function postDeletebank()
	{

		try {
			$bank_card = BankdCard::where('UserId', $this->user->UserId)->where('CreditId', $this->data['bindId'])->first();

			if (!$bank_card) {
				throw new Exception("没有找到该卡", 8996);
			}
			
			$params = array(
				'user_id' => $this->user->UserId,
				'hlb_bindId' => $this->data['bindId'],
			);

			$pay = new Pay('HLBPay');
			$pay->unBindBank();
			$pay->setParams($params);
			$pay->sendRequest();

			$result = $pay->getResult();

			if ($result['action'] != 1) { throw new Exception($result['msg'], $result['code']);}

			BankdCard::where('Id', $bank_card->Id)->delete();

			return $this->cbc_encode(json_encode(array('code'=> '200', 'msg'=> '解绑成功')));

		} catch (Exception $e) {
			return $this->cbc_encode(json_encode(array('code'=> $e->getCode(), 'msg'=> '错误代码：'.$e->getCode().','.$e->getMessage())));
		}

	}

	function postBanks()
	{
		$banks = DB::table('xyk_bankcard')->get();
		return $this->cbc_encode(json_encode(array('code'=> '200', 'msg'=>'成功', 'banks'=> $banks)));
	}

	function getQuery()
	{
		$pay = new Pay('HLBPay');
		$pay->repayQuery();
		// echo $this->user->UserId;exit;
		$params = array(
			'out_order_id' => $this->data['out_id'],
		);
		$pay->setParams($params);
		$pay->sendRequest();
		$result = $pay->getResult();
		print_r($result);
	}

	function postCeli(){
		$jn_model = new JieniuPay('SdkUserStoreQuery');
		$jn_model->onStart();
		$data = $jn_model->postUrl(\Illuminate\Support\Facades\Input::get());
		dd($data);
	}
}