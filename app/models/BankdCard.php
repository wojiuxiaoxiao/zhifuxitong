<?php 

/**
* 
*/
class BankdCard extends Eloquent
{
	protected $table = 'xyk_userbinddcard';

	function addUserCard($user_id, $card_data)
	{
		$bank = DB::table('xyk_bankcard')->where('BankCode', $card_data['bankId'])->first();
		
		if (!$bank) {
			throw new Exception("系统不支持该银行卡，请换卡重试", 8998);
		}

		$user_bank_card = $this->where('UserId', $user_id)->first();

		if ($user_bank_card) {
			$isDefault = 0;
		} else {
			$isDefault = 1;
		}

		$is_has = $this->where('CreditNumber', $card_data['bank_number'])->first();
		if ($is_has) {
			$this->where('Id', $is_has->Id)->delete();
	
		}
		

		$this->insert(array(
			'CreditId' => $card_data['bindId'],
			'UserId' => $user_id,
			'CreditName' => $bank->BankName,
			'CreditNumber' => $card_data['bank_number'],
			'status' => 0, // 0 正常 1 冻结  2解绑
			'isDefault' => $isDefault,
			'AddTime' => time(),
			'CVN' => $card_data['cvv2'], // 安全码
			'Quota' => $card_data['quota'],
			'AccountDate' => $card_data['account_date'], // 还款日
			'RepaymentDate' => $card_data['repayment_date'], // 结算日
			'Type' => $card_data['type'], // 1 借记卡  2 贷记卡
			'YN' => $card_data['yn'], // 月年
			'CVV2' => $card_data['cvv2'],
		));

	}
}