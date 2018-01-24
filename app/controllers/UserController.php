<?php 


//----------------------------------
// User部分错误code以10000开始
//  用户注册：
//      验证码错误：1001
//      手机号格式错误：1002
//      验证码发送失败：1003
//      手机号已注册：1004
//      密码格式错误：1005
//      用户注册失败：1006
//  用户登录：
//      手机号格式错误：1101
//      手机号未注册：1102
//      密码错误：1103
//      手机号码未修改：1104
//      手机号码不一致：1105
//      身份认证接口请求失败：1106
//      身份认证不一致：1107
//      身份证认证无结果：1108
//      两次密码错误： 1109
//      邀请人不存在：1110
//      邀请码不正确： 1111
//----------------------------------

class UserController extends BaseController
{
    // 姓名，余额，头像
	public function postIndex()
	{
        $this->user->IdCard = $this->IdCard;
        return $this->cbc_encode(json_encode(array('code'=> 200, 'msg'=> '数据请求成功', 'data'=> $this->user, 'IdCard'=> $this->IdCard)));
	}

    /**
	 * 个人中心
	 * @AuthorHTL
	 * @DateTime  2017-11-23T21:06:12+0800
	 * Typecho Blog Platform
	 * @param string 	mobile
	 * @param string 	password
	 * @return    [type]                   [description]
	 */ 
    public function postLogin()
    {
    	$mobile = $this->data['mobile'];
    	$password = md5($this->data['password']);

    	if(!preg_match("/^1(3|4|5|7|8)\d{9}$/", $mobile)){
    		return $this->cbc_encode(json_encode(array('code'=> 1101, 'msg'=> '手机号格式错误')));
    	}

    	$user = User::where("Mobile", $mobile)->first();

        if($user['Status']==2) {
            return $this->cbc_encode(json_encode(array('code'=> 1102, 'msg'=> '账户已被禁用')));
        }

    	if(!$user) {
    		return $this->cbc_encode(json_encode(array('code'=> 1102, 'msg'=> '手机号未注册')));
    	}

    	if($user['Password'] != $password) {
    		return $this->cbc_encode(json_encode(array('code'=> 1103, 'msg'=> '密码错误')));
    	}

        $code = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $rand = $code[rand(0,25)]
            .strtoupper(dechex(date('m')))
            .date('d').substr(time(),-5)
            .substr(microtime(),2,5)
            .sprintf('%02d',rand(0,99));
        $a = md5( $rand, true );
        $s = '0123456789ABCDEFGHIJKLMNOPQRSTUV';
        $d = '';
        for ($i = 0; $i < 8; $i++) {
            $g = ord( $a[ $i ] );
            $d .= $s[ ( $g ^ ord( $a[ $i + 8 ] ) ) - $g & 0x1F ];
        }
    	$token = time() . $d . $user['UserId'];
    	User::where("UserId", $user['UserId'])
    		->update(compact("token"));
        $user['token'] = $token;

        $IdCard = UserContact::where("UserId", $user->UserId)
            ->where("CertType", 1)
            ->where("Isvalid", 1)
            ->where("IsActivated", 1)
            ->pluck('CertNo');
$user['IDCard']=$IdCard;
    	return $this->cbc_encode(json_encode(array('code'=> 200, 'msg'=> '登录成功', 'data'=> $user, 'IdCard'=> $IdCard)));
    }

    /**
     * 退出账号
     * @AuthorHTL
     * @DateTime  2017-11-23T21:06:34+0800
     * Typecho Blog Platform
     * @copyright [copyright]
     * @license   [license]
     * @version   [version]
     * @return    [type]                   [description]
     */
    public function postLogout()
    {
        User::where('UserId', $this->user->UserId)->update(array('token'=> ''));
        return $this->cbc_encode(json_encode(array('code'=> 200, 'msg'=> '请求成功')));
    }

    /**
     * 注册
     * @AuthorHTL
     * @DateTime  2017-11-23T21:06:37+0800
     * Typecho Blog Platform
     * @copyright [copyright]
     * @license   [license]
     * @version   [version]
     * @return    [type]                   [description]
     */
    public function postCheckin()
    {
        $key = isset($this->data['key']) ? $this->data['key'] : '';
    	$mobile = isset($this->data['mobile']) ? $this->data['mobile'] : '';
    	$code = isset($this->data['code']) ? $this->data['code'] : '';
    	$password = isset($this->data['password']) ? $this->data['password'] : '';
        $invite = isset($this->data['invite']) ? $this->data['invite'] : 0;
        $paypassword = isset($this->data['pay_password']) ? $this->data['pay_password'] : '';

        $invite_info = Invitationcode::where("invcode", 'kb:'.$key)->first();
        if (!$invite_info['Isvalid']) {
            return $this->cbc_encode(json_encode(array('code'=> 1200, 'msg'=> '邀请码无效')));
        }
        if ($invite_info['Isuse']) {
            return $this->cbc_encode(json_encode(array('code'=> 1201, 'msg'=> '邀请码已被使用')));
        }

        if (!$paypassword) {
            return $this->cbc_encode(json_encode(array('code'=> 1311, 'msg'=> '交易密码不能空')));
        }

    	if(!$password) {
    		return $this->cbc_encode(json_encode(array('code'=> 1005, 'msg'=> '密码格式错误')));
    	}

    	if(!preg_match("/^1(3|4|5|7|8)\d{9}$/", $mobile)) {
    		return $this->cbc_encode(json_encode(array('code'=> 1002, 'msg'=> '手机号格式错误')));
    	}

        if($invite && !preg_match("/^1(3|4|5|7|8)\d{9}$/", $invite)) {
            return $this->cbc_encode(json_encode(array('code'=> 1002, 'msg'=> '邀请人手机格式错误')));
        }

    	$ary = Verify::where('mobile', $mobile)
            ->orderBy('time', 'desc')
            ->first();
    	if ($ary['time'] < time() || $ary['code'] != $code) {
    		// return $this->cbc_encode(json_encode(array('code'=> 1001, 'msg'=> '验证码错误')));
    	}

    	$have_user = User::where("Mobile", $mobile)
    		->count();
    	if($have_user) {
    		return $this->cbc_encode(json_encode(array('code'=> 1004, 'msg'=> '手机已被注册')));
    	}

        //edit by deshengli
    	try {
    		$username = '';
    		$userId = User::insertGetId(array(
	    		'Mobile' 	=> $mobile,
	    		'Password'  => md5($password),
	    		'Username'  => $username,
                'PayPassword' => md5($paypassword),
                'Status'    => 1,
                'UserAvatar'    => '',
                'AddTime'   => time(),
                'MerchantId' => $invite_info['MerchantId'],
                'InviterId' => $invite_info['SaleId'],
	    	));

	    	if ($userId) {
                DB::table('xyk_Invitationcode')
                    ->where('invcode', 'kb:'.$key)
                    ->update(array(
                        'UserId' => $userId,
                        'Isvalid'  => 0,
                        'Isuse' => 1,
                        'UpdateTime' => time(),
                    ));

	    		return $this->cbc_encode(json_encode(array('code'=> 200, 'msg'=> '用户注册成功')));
	    	}
    		return $this->cbc_encode(json_encode(array('code'=> 1006, 'msg'=> '用户注册失败')));
    	} catch (Exception $e) {
    		return $this->cbc_encode(json_encode(array('code'=> $e->getCode(), 'msg'=> $e->getMessage())));
    	}
    }

    /**
     * 发送验证码
     * @AuthorHTL
     * @DateTime  2017-11-23T21:06:42+0800
     * Typecho Blog Platform
     * @copyright [copyright]
     * @license   [license]
     * @version   [version]
     * @return    [type]                   [description]
     */
    public function postVerify()
    {
    	$mobile = $this->data['mobile'];
    	if(!preg_match("/^1(3|4|5|7|8)\d{9}$/", $mobile)){
    		return $this->cbc_encode(json_encode(array('code'=> 1002, 'msg'=> '手机号格式错误')));
    	}

    	try {
    		$code = rand(100000,999999);
    		if ($this->sendSMS($mobile, $code)) {
    			return $this->cbc_encode(json_encode(array('code'=> 200, 'msg'=> '验证码发送成功')));
    		}
    		return $this->cbc_encode(json_encode(array('code'=> 1003, 'msg'=> '验证码发送失败')));
    	} catch (Exception $e) {
    		return $this->cbc_encode(json_encode(array('code'=> $e->getCode(), 'msg'=> $e->getMessage())));
    	}
    }

    public function postPaypwverify()
    {
        $mobile = $this->user->Mobile;
        if(!preg_match("/^1(3|4|5|7|8)\d{9}$/", $mobile)){
            return $this->cbc_encode(json_encode(array('code'=> 1002, 'msg'=> '手机号格式错误')));
        }

        try {
            $code = rand(100000,999999);
            if ($this->sendSMS($mobile, $code)) {
                return $this->cbc_encode(json_encode(array('code'=> 200, 'msg'=> '验证码发送成功')));
            }
            return $this->cbc_encode(json_encode(array('code'=> 1003, 'msg'=> '验证码发送失败')));
        } catch (Exception $e) {
            return $this->cbc_encode(json_encode(array('code'=> $e->getCode(), 'msg'=> $e->getMessage())));
        }
    }

    /**
     * 发送验证码
     * @AuthorHTL
     * @DateTime  2017-11-23T21:06:46+0800
     * Typecho Blog Platform
     * @copyright [copyright]
     * @license   [license]
     * @version   [version]
     * @param     [type]                   $mobile [description]
     * @param     [type]                   $code   [description]
     * @return    [type]                           [description]
     */
    public function sendSMS($mobile, $code)
    {

        $account = "878001";
        $text = "【卡邦】您的验证码是{$code}";
        $sign = base64_encode(MD5("878001cd0df95e46e32c00719f156c8ec7c067"));
        $url = "http://202.91.244.252:30001/yqx/v1/sms/single_send";

        $post_data = json_encode(compact("account","text","sign","mobile"));

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_POST, 1);

        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(

            'Content-Type: application/json; charset=utf-8',

            'Content-Length: ' . strlen($post_data))

        );


        ob_start();

        curl_exec($ch);

        $return_content = ob_get_contents();

        ob_end_clean();
        $return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $result = json_decode($return_code, true);

        if (!$result || $result['code'] != 0) {
            return false;
        }
        $time = time() + 300;
        Verify::insert(compact("mobile", "code", "time"));
    	return true;
    }

    /**
     * 修改个人信息
     * @AuthorHTL
     * @DateTime  2017-11-27T21:23:57+0800
     * Typecho Blog Platform
     * @copyright [copyright]
     * @license   [license]
     * @version   [version]
     * @return    [type]                   [description]
     */
    public function postEditinfo()
    {
        $type = $this->data['type'];
        $code = $this->data['code'];
        $id = $this->user->UserId;
        $name = $this->user->Username;
        $mobile = $this->user->Mobile;
        $password = $this->user->Password;
        $pay_password = $this->user->PayPassword;
        $avatar = $this->user->UserAvatar;

        switch ($type) {
            case 'name':
                $name = $this->data['name'];
                break;
            case 'mobile':
                $mobile = $this->data['mobile'];
                if ($mobile != $this->user->Mobile) {
                    if(!preg_match("/^1(3|4|5|7|8)\d{9}$/", $mobile)){
                        return $this->cbc_encode(json_encode(array('code'=> 1002, 'msg'=> '手机号格式错误')));
                    }

                    if (User::where("Mobile", $mobile)->count()) {
                        return $this->cbc_encode(json_encode(array('code'=> 1004, 'msg'=> '手机号已被注册')));
                    }

                    $verify = Verify::where("mobile", $mobile)
                    ->orderBy('time', 'desc')
                    ->first();
                    if ($verify->code != $code || $verify->time < time()) {
                        return $this->cbc_encode(json_encode(array('code'=> 1001, 'msg'=> '验证码错误')));
                    }
                } else {
                    return $this->cbc_encode(json_encode(array('code'=> 1104, 'msg'=> '手机号码未修改')));
                }
                break;
            case 'password':
                $password = $this->data['password'];

                $verify = Verify::where("mobile", $this->user->Mobile)
                    ->orderBy('time', 'desc')
                    ->first();
                if ($verify->code != $code || $verify->time < time()) {
                    return $this->cbc_encode(json_encode(array('code'=> 1001, 'msg'=> '验证码错误')));
                }
                $password = md5($password);
                break;
            case 'pay_password':
                $pay_password = $this->data['pay_password'];

                $verify = Verify::where("mobile", $this->user->Mobile)
                    ->orderBy('time', 'desc')
                    ->first();
                if ($verify->code != $code || $verify->time < time()) {
                    return $this->cbc_encode(json_encode(array('code'=> 1001, 'msg'=> '验证码错误')));
                }
                $pay_password = md5($pay_password);
                break;
            case 'avatar':
                $avator = $this->data['avatar'];
                break;
            default:
                return $this->cbc_encode(json_encode(array('code'=> 200, 'msg'=> '信息修改成功')));
                break;
        }

        User::where("UserId", $id)->update(array(
            'Username'      => $name,
            'Mobile'        => $mobile,
            'Password'      => $password,
            'PayPassword'   => $pay_password,
            'UserAvatar'    => $avatar
        ));
        return $this->cbc_encode(json_encode(array('code'=> 200, 'msg'=> '信息修改成功')));
    }

    /**
     * 身份证绑定
     * @AuthorHTL
     * @DateTime  2017-11-29T20:49:45+0800
     * Typecho Blog Platform
     * @param 
     * @return    [type]                   [description]
     */
    public function postIdcard()
    {
        $name = $this->data['name'];
        $no = $this->data['no'];

        $contact = new UserContact();
        $result = $contact->checkIdCard($no, $name);

        if ($result['code'] != 200) {
            return $this->cbc_encode(json_encode(array('code'=> $result['code'], 'msg'=> $result['msg'])));
        }
        User::where('UserId', $this->user->UserId)->update(array(
            'Username' => $name,
        ));
        $contact->insert(array(
            'UserId'        => $this->user->UserId,
            'Contact'       => $name,
            'CertType'      => 1,
            'CertNo'        => $no,
            'Isvalid'       => 1,
            'IsActivated'   => 1,
            'AddTime'       => time(),
            'UpdateTime'    => time()
        ));

        return $this->cbc_encode(json_encode(array('code'=> 200, 'msg'=> '身份证绑定成功')));
    }

    /**
     * 我的卡包
     * @AuthorHTL
     * @DateTime  2017-11-29T21:01:53+0800
     * Typecho Blog Platform
     * @copyright [copyright]
     * @license   [license]
     * @version   [version]
     * @return    [type]                   [description]
     */
    public function postMycards()
    {
        $type = $this->data['type'];

        try {
            $cards = BankdCard::where("UserId", $this->user->UserId)
                ->where("type", $type)
                ->get();
            return $this->cbc_encode(json_encode(array('code'=> 200, 'msg'=> '请求成功', 'data'=> $cards)));
        } catch (Exception $e) {
            return $this->cbc_encode(json_encode(array('code'=> $e->getCode(), 'msg'=> $e->getMessage())));
        }

        $cards = BinkdCard::where("UserId", $this->user->UserId)->get();
        return $this->cbc_encode(json_encode(array('code'=> 200, 'msg'=> '请求成功', 'data'=> $cards)));
    }

    /**
     * 我的分享
     * @AuthorHTL
     * @DateTime  2017-11-29T21:02:28+0800
     * Typecho Blog Platform
     * @copyright [copyright]
     * @license   [license]
     * @version   [version]
     * @return    [type]                   [description]
     */
    public function postMyshare()
    {
        $money = Profit::where("user_id", $this->user->UserId)
            ->sum('money');

        $level = $this->data['level'];
        $first_ids = $second_ids = $third_ids = array();

        $first_ids = User::where("InviteOne", $this->user->UserId)
            ->lists('UserId');    
        

        $second_ids = User::where("InviteTwo", $this->user->UserId)
            ->lists('UserId');
        
        
        
        $third_ids = User::where("InviteThree", $this->user->UserId)
            ->lists('UserId');
        

        $firsts = $seconds = $thirds = array();
        $firsts_count = $seconds_count = $thirds_count = 0;

        $firsts_count = count($first_ids);
        $seconds_count = count($second_ids);
        $thirds_count = count($third_ids);

        if ($first_ids && $level == 'first') {
            $firsts = User::whereIn("UserId", $first_ids)->orderBy('AddTime', 'desc')
                ->get();
        }
        if ($second_ids && $level == 'second') {
            $seconds = User::whereIn("UserId", $second_ids)->orderBy('AddTime', 'desc')
                ->get();
        }
        if ($third_ids && $level == 'third') {
            $thirds = User::whereIn("UserId", $third_ids)->orderBy('AddTime', 'desc')
                ->get();
        }

        return $this->cbc_encode(json_encode(array('code'=> 200, 'msg'=> '请求成功', 'data'=> compact("firsts", "seconds", "money", "thirds", "firsts_count", "seconds_count", "thirds_count", "money"))));
    }

    public function postProfits()
    {
        $offset = $this->data['offset'];
        $limit = $this->data['limit'];
        $profits = Profit::where("user_id", $this->user->UserId)->orderBy("time", 'desc')->skip($offset)->take($limit)
            ->get();
        foreach ($profits as $k => $v) {
            $profits[$k]['first'] = User::where("UserId", $v['first_user_id'])->get();
            $profits[$k]['second'] = User::where("UserId", $v['second_user_id'])->get();
        }
        return $this->cbc_encode(json_encode(array('code'=> 200, 'msg'=> '请求成功', 'data'=> $profits)));
    }

    public function postPassword()
    {
        $data = isset($this->data) ? $this->data : array();
        $mobile = isset($data['mobile']) ? $data['mobile'] : '';
        $code = isset($data['code']) ? $data['code'] : '';
        $password = isset($data['password']) ? md5($data['password']) : '';
        $repassword = isset($data['repassword']) ? md5($data['repassword']) : '';

        if(!preg_match("/^1(3|4|5|7|8)\d{9}$/", $mobile)){
            return $this->cbc_encode(json_encode(array('code'=> 1002, 'msg'=> '手机号格式错误')));
        }

        if (!User::where("Mobile", $mobile)->count()) {
            return $this->cbc_encode(json_encode(array('code'=> 1102, 'msg'=> '手机号已被注册')));
        }

        $verify = Verify::where("mobile", $mobile)
            ->orderBy('time', 'desc')
            ->first();
        if ($verify->code != $code || $verify->time < time()) {
            return $this->cbc_encode(json_encode(array('code'=> 1001, 'msg'=> '验证码错误')));
        }

        if(!$password || $password != $repassword) {
            return $this->cbc_encode(json_encode(array('code'=> 1109, 'msg'=> '两次密码错误')));
        }

        User::where("Mobile", $mobile)->update(compact("password"));
        return $this->cbc_encode(json_encode(array('code'=> 200, 'msg'=> '密码修改成功')));
    }

    public function postImg()
    {
        $file = Input::file('img');
        $upload_dir = './upload/user';

        try {

            if (!Input::hasFile('img')) {
                throw new Exception("没有上传文件");
            }
            $ext = $file->getClientOriginalExtension();
            $web_dir = ltrim($upload_dir, '.');

            $file_name = date('YmdHis').uniqid().'.'.trim($ext);
            $file->move($upload_dir, $file_name);

            $url = 'http://'.$_SERVER['HTTP_HOST'] . $web_dir . '/' . $file_name;

            User::where('UserId', $this->user->UserId)->update(array(
                'UserAvatar' => $url,
            ));

            return Response::json(array('code'=> 200, 'msg'=> '上传成功', 'data'=> $url));
        } catch (Exception $e) {
            return Response::json(array('code'=> 500, 'message'=> '上传失败:'.$e->getMessage()));
        }
    }
}
