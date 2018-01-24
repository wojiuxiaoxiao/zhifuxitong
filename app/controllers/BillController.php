<?php 

class BillController extends BaseController
{
    /**
     * 账单列表
     * @return multitype:unknown
     */
	public function postBilllist()
	{
	    $offset = $this->data['offset'] ? $this->data['offset'] : '0';
	    $limit = $this->data['limit'] ? $this->data['limit'] : '20';
	    $startTime = $this->data['startTime'];
	    $endTime = $this->data['endTime'];
	    $status = $this->data['status'];
	    $type = $this->data['type'];
	   
	    $billList = Bill::select()->where('UserId',$this->user['UserId']);
	    //起始时间
	    if($startTime != ''){
	        $billList = $billList->where('created_at','>',$startTime);
	    }
	    //结束时间
	    if($endTime != ''){
	        $billList = $billList->where('created_at','<',$endTime);
	    }
	    //状态
	    if($status != ''){
	        $billList = $billList->where('status',$status);
	    }
	    //类型
	    if($type != ''){
	        $billList = $billList->where('Type',$type);
	    }
	    $billList = $billList->orderBy('created_at', 'desc')->skip($offset)->take($limit)->get();
	    
 	    if(!$billList->isEmpty()){
	        foreach ($billList as $key => &$val){
	            //获取信用卡号
	            $val->CreditName = '';
	            $val->CreditNumber = '';
	            $val->BankName = '';
	            $val->BankNumber = '';
	            $val->PayBankInfo = array();
	            if($val->CreditId != ''){
	                $creaditInfo = BankdCard::where('Id',$val['CreditId'])->first();
	                $val->CreditName = $creaditInfo['CreditName'];
	                $val->CreditNumber = $creaditInfo['CreditNumber'];
	            }
	            //获取银行卡号
	            if($val->BankId != ''){
	                $bankInfo = BankcCard::where('Id',$val['BankId'])->first();
	                $val->BankName = $bankInfo['BankName'];
	                $val->BankNumber = $bankInfo['BankNumber'];
	            }
	            
	            //paybank
	            if($val->PayBankId != ''){
	                $val->PayBankInfo = BankdCard::where('Id',$val['PayBankId'])->first();
	            }
	        }
 	    }
 	    
 	    return json_encode(array('code'=> '200', 'billList'=> $billList));
	}
	
	/**
	 * 账单详情页
	 * @return multitype:unknown
	 */
	public function postBilldetail()
	{
	    $billDetail = BillDetail::where('BillId',$this->data['billId'])->where('UserId', $this->user->UserId)->first();
	    if(!empty($billDetail)){
	        $billDetail['CreditInfo'] = array();
	        $billDetail['BankInfo'] = array();
	        if($billDetail->CreditId != ''){
	            $billDetail['CreditInfo'] = BankdCard::where('Id',$billDetail->CreditId)->first();
	        }
	        //获取银行卡号
	        if($billDetail->BankId != ''){
	            $billDetail['BankInfo'] = BankcCard::where('Id',$billDetail->BankId)->first();
	        }
	    }

	    return json_encode(array('code'=> '200', 'billDetail'=> $billDetail));
	}


   
}