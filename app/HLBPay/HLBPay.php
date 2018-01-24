<?php 

class HLBPay
{
	private $http_client;
	private $crypt_rsa;

	// 测试环境
	// private $pay_gateway = 'http://test.trx.helipay.com/trx/';
	// private $huan_gateway = 'http://test.trx.helipay.com/trx/';
	//生产环境
	private $pay_gateway = 'http://quickpay.trx.helipay.com/trx/';
	private $huan_gateway = 'http://transfer.trx.helipay.com/trx/';

	// 私钥 测试
	private $signkey = '9mzXWZCoBs0yzXLlphrcuOpbp5EYKRYz'; 
	private $rt_signkey = 'nM1l91NJHqAm04lLgNsgD0zzoOqNYedg';
	// rsa 私钥
	private $rsa_signkey = 'MIICeAIBADANBgkqhkiG9w0BAQEFAASCAmIwggJeAgEAAoGBALCgzmIu2FT5TSU8blvi1cfvX9lVg3ZbA9NFXnXrkj6bQ0Ze+NZGMKrARCsN87QaT/WnciqxVlWkOeZQCl+wH+cK9VLLHbNZuMaLTqPOvHCpEnxe2Ny8XOJtoC0ONAQo1Bs5kiSrUYgs1TGsO37XyXhTqf8dLyhyXAEnJVPNzmXBAgMBAAECgYBwp5/673X7fKa/wTOCV8OSqhKwQ+J9cr+V2QDOpVm5pL3b/GcqA8q2nbrc2yE9Fis5u3sNz94I8Z4cT6DONz+gxgfvmzRwk0tBl+Ly5c25j67u1Od36htCwEHcuFUmW0t9paAhZlAUsOoaHlocpAJ1+I3pc6+rYTp2sJRXH8IxIQJBANiNetDbnJJrjteUBQ73hC2NuthCwmueq/AUXFDploQyHkDZWEEBAyPsS+KSn3F9Njm5htlAQmuNHy1ZkVpVte8CQQDQzYXwunJzQFnVHavgDaL04G9i4RW/y7SuOBngNn7J6CS/tH4t8X/iW6A2NQeWUZYonTbx6IjuILSJ3097sc9PAkEAxFmsFXo7AmwyDXgyCfsVxzQuSW5m2Kv7XGkpt1fFWUIUOlqX8gDX9xeHSv4FQiL1Kuv0wEHKt7gyn60J5W230wJBAKEDK1sD24fGQr+VftlqipO8kgg0u9nHks+Z0VJVk5XM3rG51GdHSC9cKoJCiFRBG8K74QfQIe9G5xE+U4N2DP8CQQCw1EZ57Kv4g22K+dpfjG1EdPmbzvyZeNs3Qzfa8A05fLNjM3GGO6jcm0HSHGjNYwlu1tUSUxs832+YMfvYcagG';

	
	// 测试
	private $customer_number = 'C1800169895';
	private $credit_number = 'C1800169895';

	private $type; // 业务类型
	public $send_url; // 发送接口
	public $send_data; // 发送的参数
	private $sign_str;
	private $sign; // 签名
	protected $out_order_id; // 生成单号

	public $response; // 返回
	public $result; // 返回结果

	public $rt_sign;

	function __get($name)
	{
		if ($name == 'out_order_id') {
			return $this->out_order_id;
		}
	}

	function setType($type)
	{
		$this->type = $type;
		$url_t = '';
		// TransferQuery  CreditCardRepayment 需要RSA
		if ($type == 'repay' || $type == 'repayQuery' || $type == 'settle') {
			$this->setRSA(new Crypt_RSA);
			$this->send_url = $this->huan_gateway . 'transfer/interface.action';
		} else {
			$this->send_url = $this->pay_gateway . 'quickPayApi/interface.action';
		}
	}

	function setParams($params)
	{

		$type_list = array(
			'bankBind' 			=> 'QuickPayBindCard', // 绑卡
			'bankBindCode'  	=> 'QuickPayBindCardValidateCode', // 绑卡短信
			'bankUnbind'		=> 'BankCardUnbind', // 解绑银行卡
			'bankList'			=> 'BankCardbindList', // 用户绑定银行卡信息查询（仅限于交易卡）

			'pay'				=> 'QuickPayBindPay', // 绑卡支付
			'payCode'			=> 'QuickPayBindPayValidateCode', // 绑卡支付短信

			'payQuery'			=> 'QuickPayQuery', // 订单查询
			
			'repay'				=> 'CreditCardRepayment', // 信用卡还款	
			'repayQuery'		=> 'TransferQuery', // 信用卡还款查询

			'account'			=> 'AccountQuery', // 用户余额查询


			'settleBind'		=> 'SettlementCardBind', // 绑定结算卡
			'settle'			=> 'SettlementCardWithdraw', // 结算 提现 
		);

		$params['P1_bizType'] = $type_list[$this->type];

		call_user_func(array($this, $type_list[$this->type]), $params);
	}

	function sendRequest()
	{
		if ($this->type == 'repayQuery' || $this->type == 'repay' || $this->type == 'settle') {
			$this->ras_sign();
		} else {
			$this->md5_sign();
		}

		$pageContents = HttpClient::quickPost($this->send_url, $this->send_data);
		// echo $pageContents;exit;
		$this->response = $pageContents;
		$result = json_decode($pageContents, 1);
		$this->result = $result;
	}

	function getResult($result = null)
	{
		if (!is_null($result)) {
			$this->result = $result;
		}

		// 验证签名
		$rt_sign = $this->result['sign'];
		call_user_func(array($this, 'rt'.$this->result['rt1_bizType']), $this->result);

		if ($this->rt_sign != $rt_sign) {
			return array('action'=> 0, 'code'=> '8000', 'msg'=> '返回数据签名失败，请注意您所在的网络环境是否安全', 'result'=> array());
		}

		// 待查询状态
		if ($this->type == 'pay') {
			if ($this->result['rt9_orderStatus'] == 'DOING') {
				return array('action'=> 1, 'code'=> '0000', 'msg'=> '待查询', 'result'=> $this->result);
			}
		} else if ($this->type == 'settle' || $this->type == 'repay') {
			if ($this->result['rt2_retCode'] == '0001') {
				return array('action'=> 1, 'code'=> '0001', 'msg'=> '待查询', 'result'=> $this->result);
			}
		}

		if ($this->result['rt2_retCode'] == '0000') {

			return array('action'=> 1, 'code'=> '0000', 'msg'=> '成功', 'result'=> $this->result);

		} else {
			switch ($this->result['rt2_retCode']) {
				case '8000':
					$msg = '失败';
					break;
				case '8001': // 输入参数错误
					$msg = $this->result['rt3_retMsg'];
					break;

				case '8002': // 订单号不唯一
					$msg = '订单号不唯一';
					break;

				case '8003': // 订单金额不正确
					$msg = '订单金额不正确';
					break;

				case '8004': // 订单不存在
					$msg = '订单不存在';
					break;

				case '8005': // 订单状态异常
					$msg = '订单状态异常';
					break;

				case '8006': // 订单对应的渠道未在系统中配置
					$msg = '订单对应的渠道未在系统中配置';
					break;

				case '8007': // 退款金额超过了订单实付金额
					$msg = '退款金额超过了订单实付金额';
					break;

				case '8008': // 渠道请求交互验签错误
					$msg = '渠道请求交互验签错误';
					break;

				case '8009': // 订单已过期
					$msg = '订单已过期';
					break;

				case '8010': // 订单已存在,请更换订单号重新下单
					$msg = '订单已存在,请更换订单号重新下单';
					break;

				case '8011': // 商户未开通此银行
					$msg = '商户未开通此银行';
					break;

				case '8012': // 绑定号不存在
					$msg = '绑定号不存在';
					break;

				case '8013': // 银行卡绑卡信息不存在
					$msg = '银行卡绑卡信息不存在';
					break;

				case '8014': // 商户不存在
					$msg = '商户不存在';
					break;

				case '8015': // 短信验证码错误或已过期
					$msg = '短信验证码错误或已过期';
					break;

				case '8016': // 手机号码与下单时手机号码不一致
					$msg = '手机号码与下单时手机号码不一致';
					break;

				case '8017': // 当前银行卡不支持
					$msg = '当前银行卡不支持';
					break;

				case '8018': // 卡号与支付卡种不符
					$msg = '卡号与支付卡种不符';
					break;
				case '8019': // 产品未开通或已关闭
					$msg = '产品未开通或已关闭';
					break;

				case '8028': // 手机号码与绑定号对应的手机号码不一致
					$msg = '手机号码与绑定号对应的手机号码不一致';
					break;

				case '8030': // 只支持信用卡还款
					$msg = '只支持信用卡还款';
					break;

				case '8031': // 用户ID已绑定其他身份证号码
					$msg = '用户ID已绑定其他身份证号码';
					break;

				case '8032': // 用户ID和绑定ID已有成功绑定的记录，请核对
					$msg = '用户ID和绑定ID已有成功绑定的记录，请核对';
					break;

				case '8999': // 系统异常，请联系管理员
					$msg = '系统异常，请联系管理员';
					break;
				case '8033':
					$msg = '功能升级中，请使用信用卡充值';
					break;
				case '0002':
					$msg = '接受失败';
					break;
				default :
					$msg = $this->result['rt3_retMsg'];
					break;
			}
			
			return array('action'=> 0, 'code'=> (string)$this->result['rt2_retCode'], 'msg'=> $msg, 'result'=> $this->result);
		}	
	}

	function getOrderId()
	{
		$this->out_order_id = date('YmdHis').mt_rand(100000, 999999);
		return $this->out_order_id;
	}

	function md5_sign()
	{
		$tmp_sign_data = $this->send_data;

		if ($this->type == 'settle') {
			unset($tmp_sign_data['P8_bindId']);
		}

		if ($this->type == 'pay') {
			unset($tmp_sign_data['P17_validateCode'], $tmp_sign_data['P18_isIntegral'], $tmp_sign_data['P19_integralType'], $tmp_sign_data['P20_aptitudeCode']);
		}

		if ($this->type == 'settleBind') {
			unset($tmp_sign_data['P11_operateType']);
		}


		$signkey = $this->signkey;
		
		$sign_str = '&'.implode('&', array_values($tmp_sign_data)).'&'.$signkey;
		$this->sign_str = $sign_str;
		$sign = md5($sign_str);

		$this->send_data['sign'] = $sign;
		$this->sign = $sign;
	}

	// 导入RSA类
	function setRSA($crypt_rsa)
	{
		$this->crypt_rsa = $crypt_rsa;	
	}

	// rsa 签名
	function ras_sign()
	{
		$tmp_sign_data = $this->send_data;

		if ($this->type == 'settle') {
			unset($tmp_sign_data['P8_bindId']);
		}

		$sign_str = '&'.implode('&', array_values($tmp_sign_data));
		$this->sign_str = $sign_str;
		$this->crypt_rsa->setHash('md5'); 
		$this->crypt_rsa->setSignatureMode(CRYPT_RSA_SIGNATURE_PKCS1); # CRYPT_RSA_SIGNATURE_PKCS1
		$this->crypt_rsa->loadKey($this->rsa_signkey);
		$sign = base64_encode($this->crypt_rsa->sign($sign_str));

		$this->send_data['sign'] = $sign;
		$this->sign = $sign;
	}

	/*
	银行卡下单
	必要参数 user_id, user_name, card_number
	*/
	function QuickPayBankCardPay($params)
	{
		$this->send_data = array(
			'P1_bizType' 			=> $params['P1_bizType'],
			'P2_customerNumber' 	=> $this->customer_number,
			'P3_userId' 			=> $params['user_id'],
			'P4_orderId'  			=> $this->getOrderId(),
			'P5_timestamp' 			=> date('YmdHis'),
			'P6_payerName' 			=> $params['user_name'],
			'P7_idCardType'			=> 'IDCARD',
			'P8_idCardNo'			=> $params['id_card_number'],
			'P9_cardNo'				=> $params['bank_number'],
			'P10_year'				=> $params['bank_year'],
			'P11_month'				=> $params['bank_month'],
			'P12_cvv2'				=> $params['cvv2'],
			'P13_phone'				=> $params['user_phone'],
			'P14_currency'			=> 'CNY',
			'P15_orderAmount' 		=> round($params['money'], 2),
			'P16_goodsName'			=> $params['goods_name'],
			'P17_goodsDesc'     	=> '',
			'P18_terminalType'  	=> 'MAC',
			'P19_terminalId'		=> $params['server_mac'], // 手机序列号
			'P20_orderIp'       	=> $params['server_ip'],
			'P21_period'			=> '1',
			'P22_periodUnit'		=> 'Hour', //有效时间一小时
			'P23_serverCallbackUrl'	=> $params['callback_url'],
		);
	}

	// 验签名
	function rtQuickPayBankCardPay($params)
	{
		$rt_sign_arr = array(
			'rt1_bizType'           => $params['rt1_bizType'],
			'rt2_retCode'			=> $params['rt2_retCode'],
			'rt4_customerNumber'	=> $params['rt4_customerNumber'],
			'rt5_orderId'			=> $params['rt5_orderId'],
		);

		$sign_str = '';

		foreach ($rt_sign_arr as $key => $value) {
			$sign_str .= '&'.$value;
		}

		$sign_str .= '&'.$this->signkey;
		$this->rt_sign = md5($sign_str);
	}

	/*
	鉴权绑卡
	params 下标参数必填
	*/
	function QuickPayBindCard($params)
	{
		$this->send_data = array(
			'P1_bizType'		=> $params['P1_bizType'],
			'P2_customerNumber'	=> $this->customer_number,
			'P3_userId'			=> $params['user_id'],
			'P4_orderId'		=> $this->getOrderId(),
			'P5_timestamp'		=> date('YmdHis'),
			'P6_payerName'		=> $params['user_name'],
			'P7_idCardType'		=> 'IDCARD',
			'P8_idCardNo'		=> $params['id_card_number'],
			'P9_cardNo'			=> $params['bank_number'],
			'P10_year'			=> $params['bank_year'],
			'P11_month'			=> $params['bank_month'],
			'P12_cvv2'			=> $params['cvv2'],
			'P13_phone'			=> $params['user_phone'],
			'P14_validateCode'  => $params['validateCode'], // 短信验证码
		);
	}

	// 验签
	function rtQuickPayBindCard($params)
	{
		$rt_sign_arr = array(
			'rt1_bizType'           => $params['rt1_bizType'],
			'rt2_retCode'			=> $params['rt2_retCode'],
			'rt4_customerNumber'	=> $params['rt4_customerNumber'],
			'rt5_userId'			=> $params['rt5_userId'],
			'rt6_orderId'			=> $params['rt6_orderId'],
			'rt7_bindStatus'		=> $params['rt7_bindStatus'],
			'rt8_bankId'			=> $params['rt8_bankId'],
			'rt9_cardAfterFour'		=> $params['rt9_cardAfterFour'],
			'rt10_bindId'			=> $params['rt10_bindId'],
			'rt11_serialNumber'		=> $params['rt11_serialNumber'],
		);

		$sign_str = '';

		foreach ($rt_sign_arr as $key => $value) {
			$sign_str .= '&'.$value;
		}

		$sign_str .= '&'.$this->signkey;
		$this->rt_sign = md5($sign_str);
	}

	/*
	鉴权绑卡短信
	*/
	function QuickPayBindCardValidateCode($params)
	{
		$this->send_data = array(
			'P1_bizType' 		=> $params['P1_bizType'],
			'P2_customerNumber' => $this->customer_number,
			'P3_userId' 		=> $params['user_id'],
			'P4_orderId'		=> $this->getOrderId(),
			'P5_timestamp'		=> date('YmdHis'),
			'P6_cardNo'			=> $params['bank_number'],
			'P7_phone'  		=> $params['user_phone'],
		);
	}

	function rtQuickPayBindCardValidateCode($params)
	{
		$rt_sign_arr = array(
			'rt1_bizType'           => $params['rt1_bizType'],
			'rt2_retCode'			=> $params['rt2_retCode'],
			'rt4_customerNumber'	=> $params['rt4_customerNumber'],
			'rt5_orderId'			=> $params['rt5_orderId'],
			'rt6_phone'				=> $params['rt6_phone'],
		);

		$sign_str = '';

		foreach ($rt_sign_arr as $key => $value) {
			$sign_str .= '&'.$value;
		}

		$sign_str .= '&'.$this->signkey;
		$this->rt_sign = md5($sign_str);
	}

	// 验签
	function rtPayBindCardValidateCode($params)
	{
		$rt_sign_arr = array(
			'rt1_bizType'           => $params['rt1_bizType'],
			'rt2_retCode'			=> $params['rt2_retCode'],
			'rt4_customerNumber'	=> $params['rt4_customerNumber'],
			'rt5_userId'			=> $params['rt5_userId'],
			'rt6_orderId'			=> $params['rt6_orderId'],
			'rt7_bindStatus'		=> $params['rt7_bindStatus'],
			'rt8_bankId'			=> $params['rt8_bankId'],
			'rt9_cardAfterFour'		=> $params['rt9_cardAfterFour'],
			'rt10_bindId'			=> $params['rt10_bindId'],
			'rt11_serialNumber'		=> $params['rt11_serialNumber'],
		);

		$sign_str = '';

		foreach ($rt_sign_arr as $key => $value) {
			$sign_str .= '&'.$value;
		}

		$sign_str .= '&'.$this->signkey;
		$this->rt_sign = md5($sign_str);
	}

	/*绑卡支付短信*/
	function QuickPayBindPayValidateCode($params)
	{
		$this->send_data = array(
			'P1_bizType'		=> $params['P1_bizType'],
			'P2_customerNumber' => $this->customer_number,
			'P3_bindId'			=> $params['hlb_bindId'], // 合利宝 绑卡生成唯一ID
			'P4_userId'			=> $params['user_id'],
			'P5_orderId'		=> $this->getOrderId(),
			'P6_timestamp'		=> date('YmdHis'),
			'P7_currency'		=> 'CNY',
			'P8_orderAmount' 	=> round($params['money'], 2),
			'P9_phone'			=> $params['user_phone'],
		);
	}

	function rtQuickPayBindPayValidateCode($params)
	{
		$rt_sign_arr = array(
			'rt1_bizType'           => $params['rt1_bizType'],
			'rt2_retCode'			=> $params['rt2_retCode'],
			'rt4_customerNumber'	=> $params['rt4_customerNumber'],
			'rt5_orderId'			=> $params['rt5_orderId'],
			'rt6_phone'				=> $params['rt6_phone'],
		);

		$sign_str = '';

		foreach ($rt_sign_arr as $key => $value) {
			$sign_str .= '&'.$value;
		}

		$sign_str .= '&'.$this->signkey;
		$this->rt_sign = md5($sign_str);
	}

	/*绑卡支付*/
	function QuickPayBindPay($params)
	{
		$this->send_data = array(
			'P1_bizType' 			=> $params['P1_bizType'],
			'P2_customerNumber' 	=> $this->customer_number,
			'P3_bindId'				=> $params['hlb_bindId'],
			'P4_userId'				=> $params['user_id'],
			'P5_orderId'			=> $this->getOrderId(),
			'P6_timestamp'			=> date('YmdHis'),
			'P7_currency'			=> 'CNY',
			'P8_orderAmount'		=> round($params['money'], 2),
			'P9_goodsName'      	=> $params['goods_name'],
			'P10_goodsDesc'			=> $params['goods_desc'],
			'P11_terminalType'		=> 'MAC',
			'P12_terminalId'		=> $params['server_mac'],
			'P13_orderIp'			=> $params['server_ip'],
			'P14_period'			=> '1',
			'P15_periodUnit'		=> 'Hour',
			'P16_serverCallbackUrl' => $params['callback_url'],
			// 'P17_validateCode'		=> $params['validateCode'],
			'P18_isIntegral'		=> 'TRUE',
			'P19_integralType'		=> 'DISCOUNTS',
			'P20_aptitudeCode'		=> '',
		);
	}

	function rtQuickPayBindPay($params)
	{
		$rt_sign_arr = array(
			'rt1_bizType'           => $params['rt1_bizType'],
			'rt2_retCode'			=> $params['rt2_retCode'],
			'rt3_retMsg'			=> $params['rt3_retMsg'],
			'rt4_customerNumber'	=> $params['rt4_customerNumber'],
			'rt5_orderId'			=> $params['rt5_orderId'],
			'rt6_serialNumber'		=> $params['rt6_serialNumber'],
			'rt7_completeDate'		=> $params['rt7_completeDate'],
			'rt8_orderAmount'		=> $params['rt8_orderAmount'],
			'rt9_orderStatus'		=> $params['rt9_orderStatus'],
			'rt10_bindId'			=> $params['rt10_bindId'],
			'rt11_bankId'			=> $params['rt11_bankId'],
			'rt12_onlineCardType'	=> $params['rt12_onlineCardType'],
			'rt13_cardAfterFour'	=> $params['rt13_cardAfterFour'],
			'rt14_userId'			=> $params['rt14_userId'],
		);

		$sign_str = '';

		foreach ($rt_sign_arr as $key => $value) {
			$sign_str .= '&'.$value;
		}

		$sign_str .= '&'.$this->signkey;
		$this->rt_sign = md5($sign_str);
	}

	/*订单查询*/
	function QuickPayQuery($params)
	{
		$this->send_data = array(
			'P1_bizType'			=> $params['P1_bizType'],
			'P2_orderId'			=> $params['out_order_id'],
			'P3_customerNumber'		=> $this->customer_number,
		);
	}

	function rtQuickPayQuery($params)
	{
		$rt_sign_arr = array(
			'rt1_bizType'           => $params['rt1_bizType'],
			'rt2_retCode'			=> $params['rt2_retCode'],
			'rt3_retMsg'			=> $params['rt3_retMsg'],
			'rt4_customerNumber'	=> $params['rt4_customerNumber'],
			'rt5_orderId'			=> $params['rt5_orderId'],
			'rt6_orderAmount'		=> $params['rt6_orderAmount'],
			'rt7_createDate'		=> $params['rt7_createDate'],
			'rt8_completeDate'		=> $params['rt8_completeDate'],
			'rt9_orderStatus'		=> $params['rt9_orderStatus'],
			'rt10_serialNumber'		=> $params['rt10_serialNumber'],
			'rt11_bankId'			=> $params['rt11_bankId'],
			'rt12_onlineCardType'	=> $params['rt12_onlineCardType'],
			'rt13_cardAfterFour'	=> $params['rt13_cardAfterFour'],
			'rt14_bindId'			=> $params['rt14_bindId'],
			'rt15_userId'			=> $params['rt15_userId'],
		);

		$sign_str = '';

		foreach ($rt_sign_arr as $key => $value) {
			$sign_str .= '&'.$value;
		}

		$sign_str .= '&'.$this->signkey;
		$this->rt_sign = md5($sign_str);
	}

	/*信用卡还款*/
	function CreditCardRepayment($params)
	{
		$this->send_data = array(
			'P1_bizType'			=> $params['P1_bizType'],
			'P2_customerNumber'		=> $this->credit_number,
			'P3_userId'				=> $params['user_id'],
			'P4_bindId'				=> $params['hlb_bindId'],
			'P5_orderId'			=> $this->getOrderId(),
			'P6_timestamp'			=> date('YmdHis'),
			'P7_currency'			=> 'CNY',
			'P8_orderAmount'		=> round($params['money'], 2),
			'P9_feeType'			=> $params['feeType'],
			'P10_summary'			=> $params['remark'],
		);

	}

	/*信用卡还款 验签*/
	function rtCreditCardRepayment($params)
	{
		$rt_sign_arr = array(
			'rt1_bizType'           => $params['rt1_bizType'],
			'rt2_retCode'			=> $params['rt2_retCode'],
			'rt4_customerNumber'	=> $params['rt4_customerNumber'],
			'rt5_userId'			=> $params['rt5_userId'],
			'rt6_orderId'			=> $params['rt6_orderId'],
			'rt7_serialNumber'		=> $params['rt7_serialNumber'],
			'rt8_bindId'			=> $params['rt8_bindId'],
		);

		$sign_str = '';

		foreach ($rt_sign_arr as $key => $value) {
			$sign_str .= '&'.$value;
		}

		$sign_str .= '&'.$this->rt_signkey;
		$this->rt_sign = md5($sign_str);
	}

	/*信用卡还款查询*/
	function TransferQuery($params)
	{
		$this->send_data = array(
			'P1_bizType'			=> $params['P1_bizType'],
			'P2_orderId'			=> $params['out_order_id'],
			'P3_customerNumber'		=> $this->customer_number,
		);
	}

	function rtTransferQuery($params)
	{
		$rt_sign_arr = array(
			'rt1_bizType'           => $params['rt1_bizType'],
			'rt2_retCode'			=> $params['rt2_retCode'],
			'rt4_customerNumber'	=> $params['rt4_customerNumber'],
			'rt5_orderId'			=> $params['rt5_orderId'],
			'rt6_serialNumber'		=> $params['rt6_serialNumber'],
			'rt7_orderStatus'		=> $params['rt7_orderStatus'],
		);

		$sign_str = '';

		foreach ($rt_sign_arr as $key => $value) {
			$sign_str .= '&'.$value;
		}

		$sign_str .= '&'.$this->rt_signkey;
		$this->rt_sign = md5($sign_str);
	}

	/*用户余额查询*/
	function AccountQuery($params)
	{
		$this->send_data = array(
			'P1_bizType'			=> $params['P1_bizType'],
			'P2_customerNumber'		=> $this->customer_number,
			'P3_userId'				=> $params['user_id'],
			'P4_timestamp'			=> date('YmdHis'),
		);
	}

	function rtAccountQuery($params)
	{
		$rt_sign_arr = array(
			'rt1_bizType'           => $params['rt1_bizType'],
			'rt2_retCode'			=> $params['rt2_retCode'],
			'rt4_customerNumber'	=> $params['rt4_customerNumber'],
			'rt5_userId'			=> $params['rt5_userId'],
			'rt6_accountName'		=> $params['rt6_accountName'],
			'rt7_idCardNo'			=> $params['rt7_idCardNo'],
			'rt8_accountStatus'		=> $params['rt8_accountStatus'],
			'rt9_accountBalance'	=> $params['rt9_accountBalance'],
			'rt10_accountFrozenBalance' => $params['rt10_accountFrozenBalance'],
			'rt11_currency'			=> $params['rt11_currency'],
			'rt12_createDate'		=> $params['rt12_createDate'],
			'rt13_desc'				=> $params['rt13_desc'],
		);

		$sign_str = '';

		foreach ($rt_sign_arr as $key => $value) {
			$sign_str .= '&'.$value;
		}

		$sign_str .= '&'.$this->signkey;
		$this->rt_sign = md5($sign_str);
	}

	/*银行卡解绑*/
	function BankCardUnbind($params)
	{
		$this->send_data = array(
			'P1_bizType'			=> $params['P1_bizType'],
			'P2_customerNumber'		=> $this->customer_number,
			'P3_userId'				=> $params['user_id'],
			'P4_bindId'				=> $params['hlb_bindId'],
			'P5_orderId'			=> $this->getOrderId(),
			'P6_timestamp'			=> date('YmdHis'),
		);
	}

	function rtBankCardUnbind($params)
	{
		$rt_sign_arr = array(
			'rt1_bizType'           => $params['rt1_bizType'],
			'rt2_retCode'			=> $params['rt2_retCode'],
			'rt3_retMsg'			=> $params['rt3_retMsg'],
			'rt4_customerNumber'	=> $params['rt4_customerNumber'],
		);

		$sign_str = '';

		foreach ($rt_sign_arr as $key => $value) {
			$sign_str .= '&'.$value;
		}

		$sign_str .= '&'.$this->signkey;
		$this->rt_sign = md5($sign_str);
	}

	function BankCardbindList($params)
	{
		if (isset($params['hlb_bindId'])) {
			$this->send_data = array(
				'P1_bizType'			=> $params['P1_bizType'],
				'P2_customerNumber'		=> $this->customer_number,
				'P3_userId'				=> $params['user_id'],
				'P4_bindId'				=> $params['hlb_bindId'],
				'P5_timestamp'			=> date('YmdHis'),
			);
		} else {
			$this->send_data = array(
				'P1_bizType'			=> $params['P1_bizType'],
				'P2_customerNumber'		=> $this->customer_number,
				'P3_userId'				=> $params['user_id'],
				'P5_timestamp'			=> date('YmdHis'),
			);
		}

		
	}
	// 验签
	function rtBankCardbindList($params)
	{
		$rt_sign_arr = array(
			'rt1_bizType'           => $params['rt1_bizType'],
			'rt2_retCode'			=> $params['rt2_retCode'],
			'rt4_customerNumber'	=> $params['rt4_customerNumber'],
			'rt5_bindCardList'		=> $params['rt5_bindCardList'],
		);

		$sign_str = '';

		foreach ($rt_sign_arr as $key => $value) {
			$sign_str .= '&'.$value;
		}

		$sign_str .= '&'.$this->signkey;
		$this->rt_sign = md5($sign_str);
	}

	/*
	结算卡绑定
	*/
	function SettlementCardBind($params)
	{
		$this->send_data = array(
			'P1_bizType' 			=> $params['P1_bizType'],
			'P2_customerNumber'		=> $this->customer_number,
			'P3_userId'				=> $params['user_id'],
			'P4_orderId'			=> $this->getOrderId(),
			'P5_payerName'			=> $params['user_name'],
			'P6_idCardType'			=> 'IDCARD',
			'P7_idCardNo'			=> $params['id_card_number'],
			'P8_cardNo'				=> $params['bank_number'],
			'P9_phone'				=> $params['user_phone'],
			'P10_bankUnionCode'		=> '',
			'P11_operateType'		=> $params['operate'],
		);

	}

	// 验签
	function rtSettlementCardBind($params)
	{
		$rt_sign_arr = array(
			'rt1_bizType'           => $params['rt1_bizType'],
			'rt2_retCode'			=> $params['rt2_retCode'],
			'rt4_customerNumber'	=> $params['rt4_customerNumber'],
			'rt5_userId'			=> $params['rt5_userId'],
			'rt6_orderId'			=> $params['rt6_orderId'],
			'rt7_bindStatus'		=> $params['rt7_bindStatus'],
			'rt8_bankId'			=> $params['rt8_bankId'],
			'rt9_cardAfterFour'		=> $params['rt9_cardAfterFour'],
			// 'rt10_bindId'			=> $params['rt10_bindId'],
		);

		$sign_str = '';

		foreach ($rt_sign_arr as $key => $value) {
			$sign_str .= '&'.$value;
		}

		$sign_str .= '&'.$this->signkey;
		$this->rt_sign = md5($sign_str);
	}

	/* 结算提现 */
	function SettlementCardWithdraw($params)
	{
		$this->send_data = array(
			'P1_bizType' 			=> $params['P1_bizType'],
			'P2_customerNumber'		=> $this->customer_number,
			'P3_userId'				=> $params['user_id'],
			'P4_orderId'			=> $this->getOrderId(),
			'P5_amount'				=> round($params['money'], 2),
			'P6_feeType'			=> $params['feeType'],
			'P7_summary'			=> $params['remark'],
			'P8_bindId'				=> $params['hlb_bindId'],
		);
	}

	/*结算 验签*/
	function rtSettlementCardWithdraw($params)
	{
		$rt_sign_arr = array(
			'rt1_bizType'           => $params['rt1_bizType'],
			'rt2_retCode'			=> $params['rt2_retCode'],
			'rt4_customerNumber'	=> $params['rt4_customerNumber'],
			'rt5_userId'			=> $params['rt5_userId'],
			'rt6_orderId'			=> $params['rt6_orderId'],
			'rt7_serialNumber'		=> $params['rt7_serialNumber'],
		);
		
		$sign_str = '';

		foreach ($rt_sign_arr as $key => $value) {
			$sign_str .= '&'.$value;
		}

		$sign_str .= '&'.$this->rt_signkey;
		$this->rt_sign = md5($sign_str);
	}

	/*异步通知确认*/

	function rtQuickPayConfirmPay($params)
	{
		$rt_sign_arr = array(
			'rt1_bizType' 			=> $params['rt1_bizType'],
			'rt2_retCode' 			=> $params['rt2_retCode'],
			'rt4_customerNumber'	=> $params['rt4_customerNumber'],
			'rt5_orderId'			=> $params['rt5_orderId'],
			'rt6_serialNumber'		=> $params['rt6_serialNumber'],
			'rt7_completeDate'		=> $params['rt7_completeDate'],
			'rt8_orderAmount'		=> $params['rt8_orderAmount'],
			'rt9_orderStatus' 		=> $params['rt9_orderStatus'],
			'rt10_bindId'			=> $params['rt10_bindId'],
			'rt11_bankId'			=> $params['rt11_bankId'],
			'rt12_onlineCardType'	=> $params['rt12_onlineCardType'],
			'rt13_cardAfterFour'	=> $params['rt13_cardAfterFour'],
			'rt14_userId'			=> $params['rt14_userId'],
		);

		$sign_str = '';

		foreach ($rt_sign_arr as $key => $value) {
			$sign_str .= '&'.$value;
		}

		$sign_str .= '&'.$this->signkey;
		$this->rt_sign = md5($sign_str);
	}
}	