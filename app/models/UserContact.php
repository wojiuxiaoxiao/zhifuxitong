<?php

class UserContact extends Eloquent {

    protected $table = 'xyk_usercontact';
    public $timestamps = false;

    /**
     * 校验身份证
     * @AuthorHTL
     * @DateTime  2017-11-29T20:21:09+0800
     * Typecho Blog Platform
     * @param     string                   $idNo   身份证号码
     * @param     string                   $idName 真实姓名
     * @return    boolean                  true|false
     */
    public function checkIdCard($idNo, $idName)
    {
        $key = '4d5da73518ef402b93c074ad62b918e3';
        $value = '35c166c283ed47a09453bd0b51f7454b';
        $rcaRequestType = 1;
        
        $data = compact("key", "value", "rcaRequestType", "idNo", "idName");
        $url = 'https://authentic.yunhetong.com/authentic/authenticationPersonal';
        $result = json_decode($this->curlPost($url, $data));

        if (!$result || $result->code != 200) {
            return array('code' => 1106, 'msg' => '请求失败');
        }
        switch ($result->data->status) {
            case 1:
                return array('code' => 200, 'msg' => '认证一致');
                break;
            case 2:
                return array('code' => 1107, 'msg' => '认证不一致');
                break;
            case 3:
                return array('code' => 1108, 'msg' => '认证无结果');
                break;
            default:
                return array('code' => 1106, 'msg' => '请求失败');
                break;
        }
    }

    /**
     * post请求
     * @AuthorHTL
     * @DateTime  2017-11-29T20:23:14+0800
     * Typecho Blog Platform
     * @param     string                   $url   请求路由
     * @param     array                    $param 请求数据
     * @return    [type]                          [description]
     */
    public function curlPost($url, $param)
    {
        $postStr = '';
        foreach($param as $k => $v){
            $postStr .= $k."=".$v."&";
        }
        $postStr=substr($postStr,0,-1);
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);                //设置访问的url地址
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postStr);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);               //设置超时
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);   //用户访问代理 User-Agent
        curl_setopt($ch, CURLOPT_REFERER,$_SERVER['HTTP_HOST']);        //设置 referer
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);          //跟踪301
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);        //返回结果
        $r=curl_exec($ch);
        curl_close($ch);
        return $r;
    }

}