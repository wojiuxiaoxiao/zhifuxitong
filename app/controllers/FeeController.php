<?php 

/**
* 
*/
class FeeController extends BaseController
{
	function postFee()
	{
		try {
			$fee = DB::table('xyk_fee')->first();
			if (!$fee) {
				throw new Exception("商家未设置费率", 0);
			}
			return $this->cbc_encode(json_encode(array('code'=> '200', 'msg'=> '成功', 'fee'=> $fee)));
		} catch (Exception $e) {
			return $this->cbc_encode(json_encode(array('code'=> '0', 'msg'=> $e->getMessage())));
		}
		
	}
}