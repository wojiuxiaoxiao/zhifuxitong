<?php 

class BindCardController extends BaseController
{
    /**
     * 交易卡列表
     * @return multitype:unknown
     */
	public function postBinddcardlist()
	{
	    $offset = $this->data['offset'] ? $this->data['offset'] : '0';
	    $limit = $this->data['limit'] ? $this->data['limit'] : '20';
	   	
	   	$select = BankdCard::skip($offset)->take($limit)->where('UserId', $this->user->UserId);

	   	if (isset($this->data['type'])) {
	   		$select->where('type', $this->data['type']); // 1 银行  2 信用卡
	   	}

	    $binddCards = $select->get();
	    
	    return json_encode(array('code'=> '200', 'binddCards'=> $binddCards));
	}
	
	
	/**
	 * 结算卡列表
	 * @return multitype:unknown
	 */
	public function postBindccardlist()
	{
	    $offset = $this->data['offset'] ? $this->data['offset'] : '0';
	    $limit = $this->data['limit'] ? $this->data['limit'] : '20';
	
	    $bindcCards = BankcCard::skip($offset)->take($limit)->where('UserId', $this->user->UserId)->get();
	     
	    return json_encode(array('code'=> '200', 'bindcCards'=> $bindcCards));
	}


   
	// 修改 交易卡
	public function postBindcardupdate()
	{
		try {


			$update_arr = array();
			
			if (!$this->data['bank_id']) {
				throw new Exception("bank_id必传", 0);
			}

			if (isset($this->data['AccountDate'])) {
				$update_arr['AccountDate'] = $this->data['AccountDate'];
			}

			if (isset($this->data['RepaymentDate'])) {
				$update_arr['RepaymentDate'] = $this->data['RepaymentDate'];
			}
		
			if (isset($this->data['Quota'])) {
				$update_arr['Quota'] = $this->data['Quota'];
			}

			if (empty($update_arr)) {
				throw new Exception("至少修改一项", 0);
			}
	
			$bank_card = BankdCard::where('UserId', $this->user->UserId)->where('Id', $this->data['bank_id'])->first();
			if (!$bank_card) {
				throw new Exception("找不到该卡", 3001);
			}

			BankdCard::where('Id', $bank_card->Id)->update($update_arr);

			return $this->cbc_encode(json_encode(array('code'=> '200', 'msg'=> '修改成功')));
		} catch (Exception $e) {
			return $this->cbc_encode(json_encode(array('code'=> $e->getCode(), 'msg'=> $e->getMessage())));
		}

	}

}