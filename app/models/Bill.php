<?php 

/**
* 
*/
class Bill extends Eloquent
{
	protected $table = 'xyk_billlistlog';


	static function createBill($data)
	{
		$time = time();

		if (!isset($data['BankId'])) {
			$data['BankId'] = 0;
		}

		if (!isset($data['CreditId'])) {
			$data['CreditId'] = 0;
		}

		if (!isset($data['TableId'])) {
			$data['TableId'] = 0;
		}

		if (!isset($data['SysFee'])) {
			$data['SysFee'] = 0;
		}

		if (!isset($data['From'])) {
			$data['From'] = '';
		}

		if (!isset($data['To'])) {
			$data['To'] = '';
		}

		if (!isset($data['ApiFee'])) {
			$data['ApiFee'] = 0;
		}

		$bill_id = Bill::insertGetId(array(
			'CreditId' => $data['CreditId'],
			'BankId' => $data['BankId'],
			'status' => 2, // 待查询
			'UserId' => $data['UserId'],
			'Amount' => $data['money'],
			'AddTime' => $time,
			'Type' => $data['Type'],
			'created_at' => date('Y-m-d H:i:s'),
			'feeType' => $data['feeType'],
			'TableId' => $data['TableId'],
			'SysFee' => $data['SysFee'],
			'From' => $data['From'],
			'To' => $data['To'],
			'ApiFee' => $data['ApiFee'],
		));

		BillDetail::insert(array(
			'BillId' => $bill_id,
			'CreditId' => $data['CreditId'],
			'BankId' => $data['BankId'],
			'UserId' => $data['UserId'],
			'OrderNum' => $data['OrderNum'],
			'CardId' => $data['bank_number'],
			'CreateTime' => $time,
			'AddTime' => $time,
			'Amount' => $data['money'] + $data['SysFee'],
			'created_at' => date('Y-m-d H:i:s'),
		));

		return $bill_id;
	}

	static function billUpdate($bill_id, $type = 'SUCCESS')
	{
		if ($type == 'SUCCESS') {
			Bill::where('Id', $bill_id)->update(array(
				'status' => 1,
			));
		} else if ($type == 'DOING') {
			Bill::where('Id', $bill_id)->update(array(
				'status' => 2,
			));
		} else if ($type == 'FAIL') {
			Bill::where('Id', $bill_id)->update(array(
				'status' => 0,
			));
		}

	}
}