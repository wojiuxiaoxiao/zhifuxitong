<?php 

class Pay
{
	private $pay_obj;

	function __construct($class_name)
	{
		$reflection = new ReflectionClass($class_name);
		$obj = $reflection->newInstance();
		if (!$obj) {
			throw new Exception("no class");
		}

		$this->pay_obj = $obj;
	}

	function getOrderId()
	{
		return $this->pay_obj->out_order_id;
	}

	function setParams($params)
	{
		$this->pay_obj->setParams($params);
	}

	function sendRequest()
	{
		$this->pay_obj->sendRequest();
	}

	function getResult($result = null)
	{
		return $this->pay_obj->getResult($result);
	}

	// 绑卡
	function bankBind()
	{
		$this->pay_obj->setType('bankBind');
	}

	// 绑卡短信
	function getBankValideteCode()
	{
		$this->pay_obj->setType('bankBindCode');
	}

	// 支付
	function pay()
	{
		$this->pay_obj->setType('pay');
	}

	// 支付查询
	function payQuery()
	{
		$this->pay_obj->setType('payQuery');
	}

	// 还款 提现查询
	function repayQuery()
	{
		$this->pay_obj->setType('repayQuery');
	}
	// 支付短信
	function payCode()
	{
		$this->pay_obj->setType('payCode');
	}

	// 还款
	function repay()
	{
		$this->pay_obj->setType('repay');
	}

	// 解绑
	function unBindBank()
	{
		$this->pay_obj->setType('bankUnbind');
	}

	// 绑卡列表
	function bankList()
	{
		$this->pay_obj->setType('bankList');
	}

	// 绑定结算卡
	function settleBind()
	{
		$this->pay_obj->setType('settleBind');
	}

	// 结算卡提现
	function settle()
	{
		$this->pay_obj->setType('settle');
	}
}