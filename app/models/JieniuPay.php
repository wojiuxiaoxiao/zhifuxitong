<?php

/**
 * Created by PhpStorm.
 * User: XUN
 * Date: 2018/1/15
 * Time: 16:39
 */
class JieniuPay
{
    protected $privateKey = '1234567890123456';
    protected $KEY = '76c7c997b61ea571100513d970ce4cde';  //商户蜜钥(KEY)
    protected $merNo = '311801191881850 ';  //接入商户编号
    protected $action = '';  //传的方法
    protected $url = '';  //接口的url
    protected $orgNo = '1801191881670998';  //接入机构编号

    //进件之后生成的
    protected $userCode = '311801191881878';  //接入商户编号
    protected $userKey = '9dfd6fbb4c28f070373d57a8418e1080';  //接入商户秘钥

    protected $private_key = '-----BEGIN RSA PRIVATE KEY-----
MIICdQIBADANBgkqhkiG9w0BAQEFAASCAl8wggJbAgEAAoGBAIWXOPtJK+bl
l/RZ0rGm10ZESrYIsPLbrrA3PTpo+hflNMBv9n74zspdJ7ynAezLgo9rHMGU
q0opVc5bAVGoKqHxp3Tgpu7uufdHG3uHCV9l/jwTVGDu8qHgrBxE8YJNNSRy
UXeWGI334Ivsw1K3VfAo13IjAnGrYCmCqCsQ6R9RAgMBAAECgYAF3t9iK5UC
UJSc6FWZ+Pr824Ns/HTKN1014TePmY1j/26SBlmOuuBbhDx6zdwHy+mw48Qf
vajJWYerydQFAp7c7mFEgE6YafrTWsmPL/6NLasKjlSiWXbqVYJm+HDViwjV
5xr6hYChc4+yGyOUlSDW9q+yR+8437ur8wA1Q8glAQJBAOUEZ/ZudZZ2KLP+
786GGHQKRpLI+nP7H+ez1KeFPT4uaFBVmGwyIgIycKfVvji2m42lfY60mTFm
Ns+duOsLaAcCQQCVVJJ2vs9Wjf/afzb13oz4VVx9xaamO2/NK8rq849hqMjw
ILR0SVvxT7xwgvAqOrM55Fu5JfOXcGBUF+7y5XfnAkA8aLAfob1kpeBQslOB
L3/tw/QfreHajAg7bwUN9yhTaZxcbGebSpIL8FAlBU162jgn4do/tUWfcS3O
D/WCEm0rAkAlK5T9e8duWxA5mfrbPpdGZTENmXo+3IKaxnDPBOtGutWxd+KT
I4OAUIGuW9leGZhtK5ttPbYhlvZTWFXBHj83AkAOSudnCJ4uHEC+10GXL8G7
u31qPtSVv3NTcc3RmDheMQNUsKw17ts9QZbC1hc/De2ZW2VUd4fuCc0CPqwZ
XqXC
-----END RSA PRIVATE KEY-----';

    //示例
    //$JieniuPay = new JieniuPay('SdkUserStoreQuery');
    //$data = $JieniuPay->postUrl(\Illuminate\Support\Facades\Input::get());
    //dd($data);
    public function __construct($action)
    {
        $this->privateKey = '1234567890123456';
        $this->action = $action;
        $this->url = 'http://api.life.hzjieniu.com/sdk/action';
    }
    /**
     * onStart
     * 数据AES加密
     * @author by kexun
     * @param array $input 用户传值
     * @access public
     * @return array
     * @since 1.0
     */
    public function onStart()
    {
        $result = $this->merchantCheck();
        $result = json_decode($result);
        if($result->code != '000000'){
            $result = $this->merchantInit();
            $result = $this->merchantCheck(json_decode($result)->userCode);
            $result = json_decode($result);
        }
        $result = $this->changeRate($result);
        return $result;
    }
    /**
     * postUrl
     * 数据AES加密
     * @author by kexun
     * @param array $input 用户传值
     * @access public
     * @return array 请求结果
     * @since 1.0
     */
    public function postUrl($input)
    {
        unset($input['token']);
        if(!isset($input['action'])) {
            $this->merNo = $this->userCode;
            $this->KEY = $this->userKey;
        }
        $input['orgNo'] = $this->orgNo;
        $input['merNo'] = $this->merNo;
        $data = $this->returnData($input);
        $sgin = $this->returnSign($input, $data);
        $encrypted = $this->returnEncrypted();
        $input['data'] = $data;
        $input['sign'] = $sgin;
        $input['encryptkey'] = $encrypted;
        $input['action'] = isset($input['action']) ? $input['action'] : $this->action;
        $result = $this->postData($this->url, $input);
        $data = json_decode($result)->data;
        $encryptkey = json_decode($result)->encryptkey;
        require_once(app_path() . '/' . 'HLBPay/RsaUtils.php');
        $RsaUtils = new \RsaUtils();
        $key = $RsaUtils->decrypt($encryptkey, $this->private_key);
        require_once(app_path() . '/' . 'HLBPay/AesUtils.php');
        $AesUtils = new \AesUtils();
        $result = $AesUtils->decrypt($data, $key);
        return $result;
    }

    /**
     * returnData
     * 数据AES加密
     * @author by kexun
     * @param array $input 用户传值
     * @access public
     * @return string 加密后的字符串
     * @since 1.0
     */
    public  function returnData($input)
    {
        $input['action'] = isset($input['action']) ? $input['action'] : $this->action;
        $data = json_encode($input);
        require_once(app_path() . '/' . 'HLBPay/AesUtils.php');
        $AesUtils = new \AesUtils();
        $data = $AesUtils->encrypt($data,$this->privateKey);
        return $data;
    }

    /**
     * returnSign
     * md5加密签名   //md5($orgNo.$merNo.$action.$data.$KEY);
     * @author by kexun
     * @param array  $input 用户传值
     * @param array  $string 加密后的字符串
     * @access public
     * @return string 加密后的字符串
     * @since 1.0
     */
    public  function returnSign($input,$string)
    {
//        $data = '';
//        foreach($input as $v){
//            $data.=$v;
//        }
//        $data.=$this->action;
//        $data.=$string;
//        $data.=$this->KEY;
//        return md5($data);
        $data = '';
        $data.=$this->orgNo;
        $data.=$this->merNo;
        $data.=isset($input['action']) ? $input['action'] : $this->action;
        $data.=$string;
        $data.=$this->KEY;
        return md5($data);
    }

    /**
     * returnEncrypted
     * AES加密
     * @author by kexun
     * @access public
     * @return string 加密后的字符串
     * @since 1.0
     */
    public function returnEncrypted()
    {
        require_once(app_path() . '/' . 'HLBPay/RsaUtils.php');
        $RsaUtils = new \RsaUtils();
        return $RsaUtils->encrypt($this->privateKey,$this->private_key);
    }
    /**
     * postData
     * curl请求
     * @author by kexun
     * @access public
     * @return string 返回的参数
     * @since 1.0
     */
    function postData($url,$param=array()){
        $o="";
        foreach ($param as $k=>$v)
        {
            $o.= "$k=".urlencode($v)."&";
        }
        $get_data=substr($o,0,-1);
        if(strstr($url,"?")){
            $url.='&'.$get_data;
        }
        else {
            $url.='?'.$get_data;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1 );
        curl_setopt($ch,CURLOPT_TIMEOUT,2);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $get_data);
        $result = curl_exec($ch);
        if ($result === false){
            file_put_contents('./output.log',date('Y-m-d H:i:s').': Curl error: '.curl_errno($ch).PHP_EOL,FILE_APPEND);
        }else{
            return $result;
        }
    }

    /**
     * 进件商户信息查询
     */
    function merchantCheck($input = ''){
        $data['userCode']= $input == '' ? $this->userCode : $input;
        $data['action']='SdkUserStoreQuery';
        $result =  $this->postUrl($data);
        if(json_decode($result)->code == '000000'){
            $this->userCode = json_decode($result)->userCode;
            $this->userKey = json_decode($result)->userKey;
        }
        return $result;
    }
    /**
     * 进件商户信息提交
     */
    function merchantInit(){

        $data['userId']='311801191881850';
        $data['userName']='浙江小口袋投资管理有限公司';
        $data['userNick']='小口袋';
        $data['userPhone']='13958651881';
        $data['userAccount']='方建军';
        $data['userCert']='33108119811224087X';
        $data['userEmail']='116779511@qq.com';
        $data['userAddress']='浙江省温岭市城东街道万昌北路806号青商大厦6楼602室';
        $data['userMemo']='国家法律、法规和政策允许的投资业务：接受金融机构委托从事金融信息技术外包等';
        $data['settleBankNo']='6217920301589409';
        $data['settleBankPhone']='13958651881';
        $data['settleBankCnaps']='310345400015';
        $data['action']='SdkUserStoreBind';

        $result =  $this->postUrl($data);
        return $result;
    }

    /*
     * 商户汇率变更
     * SdkUserStoreRate
     * */
    public function changeRate($user)
    {
        $data['userCode'] = $user->userCode;    //商户编号
        $data['payType'] = 1;    //交易类型
        $data['orderRateT0'] = 1;    //交易费率 0.36（费率0.36/100）
        $data['orderRateT0Min'] = 0;    //交易费率最低金额 单位：元（0）
        $data['orderRateT0Max'] = 99999;    //交易费率最高金额 单位：元（99999）
        $data['settleChargeT0'] = 2;    //提现附加费用 单位：元（2）
        $data['orderRateT1'] = 1;    //交易费率 0.33（费率0.33/100）
        $data['orderRateT1Min'] = 0;    //交易费率最低金额  单位：元（0）
        $data['orderRateT1Max'] = 50000;    //交易费率最高金额  单位：元（99999）
        $data['settleChargeT1'] = 1;    //提现附加费用  单位：元（1）
        $data['action'] = 'SdkUserStoreRate';    //方法
        $result =  $this->postUrl($data);
        return $result;
    }
    public function postUrljin($input,$action='SdkUserStoreBind')
    {
        unset($input['token']);
        $input['orgNo'] = $this->orgNo;
        $input['merNo'] = $this->merNo;
        $data = $this->returnData($input);
        $sgin = $this->returnSign($input,$data);
        $encrypted = $this->returnEncrypted();
        $input['data'] = $data;
        $input['sign'] = $sgin;
        $input['encryptkey'] = $encrypted;
        $input['action'] = $action;
        $result = $this->postData($this->url,$input);
        dd($result);
        $data = json_decode($result)->data;
        $encryptkey = json_decode($result)->encryptkey;
        require_once(app_path() . '/' . 'HLBPay/RsaUtils.php');
        $RsaUtils = new \RsaUtils();
        $key = $RsaUtils->decrypt($encryptkey,$this->private_key);
        require_once(app_path() . '/' . 'HLBPay/AesUtils.php');
        $AesUtils = new \AesUtils();
        $result = $AesUtils->decrypt($data,$key);
        return $result;
    }

    /**
     * 商户费率设置
     */
}