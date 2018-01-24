<?php

class BaseController extends Controller {

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	
	protected $data = '';
    protected $key = 'xinyongka';
    protected $user;
    protected $IdCard;

    protected $privateKey = "1234567812345678";  
	protected $iv = "1234567812345678"; 

	function __construct()
	{

		header('Content-type:text/html;charset=utf-8');
		// $data = array('token'=> '2sdw2123ddqw', 'params1'=> 'add');

		// echo $this->cbc_encode(json_encode($data));exit;
		
    	$url_ary = array("user/login", "user/checkin", "user/verify", "user/img", "user/password","pay/jncb","pay/jnrecb","pay/plancb");

		// try {
			// $mcrypt_str = Input::get('data');

			// $data_json = $this->cbc_decode($mcrypt_str);
			
			// if ($data_json == '') {
			// 	echo $this->cbc_encode(json_encode(array('code'=> '500', 'msg'=> '解密出错')));
			// 	exit();
			// }
			// $data_json = trim($data_json);
			
			// urldecode
			// $data_json = urldecode($data_json);
			// $data = json_decode($data_json, 1);

			// if (!$data) {
			// 	echo $this->cbc_encode(json_encode(array('code'=> '500', 'msg'=> 'data必传')));
			// 	exit();
			// }


			$path = Request::path();
			$data = Input::all();
			$this->data = $data;

			if (!in_array($path, $url_ary) && !isset($this->data['token'])) {
				echo $this->cbc_encode(json_encode(array('code'=> '500', 'msg'=> 'token必传')));
				exit();
			}
			$token = isset($this->data['token']) ? $this->data['token'] : '';
        	$this->user = User::where('token', $token)->first();

        	if (!in_array($path, $url_ary)) {
        		if ($this->user) {
	        		$this->IdCard = UserContact::where("UserId", $this->user->UserId)
	        			->where("CertType", 1)
	        			->where("Isvalid", 1)
	        			->where("IsActivated", 1)
	        			->pluck('CertNo');
	        	} else {
	        		echo $this->cbc_encode(json_encode(array('code'=> '1001', 'msg'=> '用户未登录')));
	        		exit();
	        	}
        	}
        	

		// 	if (!$data) {
		// 		throw new Exception("data参数必传", 9999);
		// 	}

		// 	$data = $this->cbc_decode($data);

		// 	$data = json_decode($data, 1);

		// 	if (isset($data['token'])) {
		// 		session_start($data['token']);
		// 	} else {
		// 		session_start();
		// 	}
			
			
		// 	$this->data = $data;

		// } catch (Exception $e) {
		// 	return json_encode(array('code'=> $e->getCode(), 'msg'=> $e->getMessage()));
		// }

	}

	protected function cbc_encode($data)
	{
		return $data;
		//加密  
		$encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $this->privateKey, $data, MCRYPT_MODE_CBC, $this->iv);  
		return base64_encode($encrypted);  
	}

	protected function cbc_decode($str)
	{
		return $str;
		//解密  
		$encryptedData = base64_decode($str);  
		$decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->privateKey, $encryptedData, MCRYPT_MODE_CBC, $this->iv);  
		return $decrypted; 
	}

	protected function  switch_pay(){
		$way = DB::table('xyk_paytemplate')->first();
		return $way->TemplateId;
	}

}
