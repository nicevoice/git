<?php

class controller_user extends exam_controller_abstract
{
    private $member_front,$member_detail;
	function __construct(& $app)
	{
		parent::__construct($app);
        include_once app_dir("exam").'lib/validation.php' ;
		$this->member_front = loader::model('member_front', 'member');
		$this->member_detail = loader::model('member_detail', 'member');
        $this->member = loader::model('member_front', 'member');
        load_open();
	}
    
    /**
     * 修改密码  api
     * @param $phone
     */
    function resetpassword(){
        $uid = intval($_GET['uid']);
		$uk = value($_REQUEST, 'uk');
        $password = trim(value($_REQUEST, 'password'));
        $oldPassword = trim(value($_REQUEST, 'oldPassword'));
        if (!ukdecode($uid, $uk)){
			$status = '2';
			$msg = 'cat‘t find member';
			$res = array('status'=>$status,'msg'=>$msg,'data'=>array());
			echo $this->json->encode($res);exit;
		}
        $result = $this->member->password($uid, $password, $oldPassword);
        if($result){
            $status = 1;
            $msg = '密码修改成功';
            $res = array('status'=>$status,'msg'=>$msg);
            echo $this->json->encode($res);exit;
        }else{
            $status = 0;
            $msg = '密码修改失败';
            $res = array('status'=>$status,'msg'=>$msg);
            echo $this->json->encode($res);exit;
        }
    }
    
    /**
     * 验证短信修改密码  api
     * @param $phone
     */
    function codePassword(){
        $phone = is_numeric($_GET['phone']) ? $_GET['phone'] : 0;
        $password = trim(value($_GET, 'password'));
        $uk = $_GET['uk'];
        $userid = $this->member->plugin_open_member('mobile', $phone);
        //生成uk
        $auth = MD5($userid.$phone);
        if($uk != $auth){
            $status = 0;
            $msg = '用户名称发生改变';
            $res = array('status'=>$status,'msg'=>$msg);
            echo $this->json->encode($res);exit;
        }
        $result = $this->member->forget_update_password($userid, $password);
        if($result){
            $status = 1;
            $msg = '密码修改成功';
            $res = array('status'=>$status,'msg'=>$msg);
            echo $this->json->encode($res);exit;
        }else{
            $status = -1;
            $msg = '密码修改失败';
            $res = array('status'=>$status,'msg'=>$msg);
            echo $this->json->encode($res);exit;
        }
    }
    
    /**
     * 验证短信验证码  api
     * @param $phone
     */
    function code(){
        $phone = is_numeric($_GET['phone']) ? $_GET['phone'] : 0;
        $getCode = is_numeric($_GET['code']) ? $_GET['code'] : 0;
        $userid = $this->member->plugin_open_member('mobile', $phone);
        if(!$userid){
            $status = -1;
            $msg = '无法获取用户信息';
            $res = array('status'=>$status,'msg'=>$msg);
            echo $this->json->encode($res);exit;
        }
        $redis = redis();
        $code = $redis->get($phone);
        if($getCode != $code){
            $status = 0;
            $msg = '验证失败';
            $res = array('status'=>$status,'msg'=>$msg);
            echo $this->json->encode($res);exit;
        }
        //生成uk
        $auth = MD5($userid.$phone);
        $status = 1;
        $msg = '验证成功';
        $_result = array('phone'=>$phone,'uk'=>$auth);
        $res = array('status'=>$status,'msg'=>$msg,'data'=>$_result);
        echo $this->json->encode($res);exit;
    }
	
	/**
     * 题库短信  api
     * @param $phone
     * @param $user_code
     */
	function duanxin(){
		$phone = is_numeric($_GET['phone']) ? $_GET['phone'] : 0;
        $reset = $_GET['reset'] ? $_GET['reset'] : 0;
		$redis = redis();
		$phoneOld = $redis->get($phone);
		if($phoneOld){
			$status = 1;
			$msg = '短信发送成功';
			$res = array('status'=>$status,'msg'=>$msg);
			echo $this->json->encode($res);exit;
		}
        if(!$reset){
            if (uc_user_open_get($this->member_front->openConfig['mobile']['appid'], $phone, 'mobile')) {//判断手机是否注册
                $status = 0;
                $msg = '该号码已经被注册';
                $_result = array('phone'=>$phone);
                $res = array('status'=>$status, 'msg'=>$msg ,'data'=>$_result);
                echo $this->json->encode($res);exit;
            }
            $modelId = 37;
        }else{
            $userid = $this->member->plugin_open_member('mobile', $phone);
            if(!$userid){
                $status = -1;
                $msg = '无法获取用户信息';
                $res = array('status'=>$status,'msg'=>$msg);
                echo $this->json->encode($res);exit;
            }
            $modelId = 55;
        }
		if($phone){
			$code = mt_rand(100000,999999);
			$send[] = array
			(
				'mode' => 'sms',
				'mobile' => $phone,
				'template' => array(
					array(
						'id' => $modelId,
						'data_key' => array('sms_data'),
					)
				),
			);
			$send_data = array(
				'sms_data' => array(
					'code' => $code,
				),
			);
			$notify = load_rpc('notify')->send_mode($send, $send_data);
			if($notify){
				$redis->set($phone, $code, 3600);
				$status = 1;
				$msg = '短信发送成功';
				$_result = array('code'=>$code,'phone'=>$phone);
				$res = array('status'=>$status,'msg'=>$msg,'data'=>$_result);
				echo $this->json->encode($res);exit;
			}else{
				$status = 2;
				$msg = '短信发送失败';
				$_result = array('code'=>$code,'phone'=>$phone);
				$res = array('status'=>$status,'msg'=>$msg,'data'=>$_result);
				echo $this->json->encode($res);exit;
			}
		}else{
			$status = 3;
			$msg = '电话号码错误';
			$res = array('status'=>$status,'msg'=>$msg);
			echo $this->json->encode($res);exit;
		}
	}
    
    
	
	/**
     * 题库注册  api
     * @param $phone
     * @param $user_code
     * @param $nickname
     * @param $password
     * @param $city
     */
	public function register(){
		$phone = $_GET['phone'];
		$user_code = $_GET['user_code']  ? intval($_GET['user_code']) : 0;
		$nickname = $_GET['nickname'];
		$password = $_GET['password'];
		$city = $_GET['city'];//地区
		$redis = redis();
		$code_answer = $redis->get($phone);

		if($code_answer!=$user_code){
			$status = 4;
			$msg = '验证码错误';
			$_result = array('code'=>$code_answer,'phone'=>$phone);
			$res = array('status'=>$status,'msg'=>$msg,'data'=>$_result);
			echo $this->json->encode($res);exit;
		}else{
			$email= $phone."@mr.kuaiji.com";
			$register_data = array('sex'=>2, 'city'=>$city,'username'=>$phone, 'password'=>$password, 'email'=>$email, 'mobile'=>$phone, 'nickname'=>$nickname);

			$userid = $this->member_front->register($register_data);
			if(!$userid){
				$status = 0;
				$msg = $this->member_front->error;
			}else{
				$status = 1;
				$msg = '注册成功';
			}
			$res = array('status'=>$status, 'msg'=>$msg ,'data'=>$userid);
			echo $this->json->encode($res);exit;
		}
	}

    /**
     * APP 用户登录
     *
     * @die json->array();
     */
    public function login(){
        $check_data = $this -> valid_data();
        $user = $this->member_front->login($check_data['uname'], $check_data['passwd'], 0, (strpos($check_data['uname'], '@') !== false ? 2 : (eregi('^[0-9]*$',$check_data['uname']) ? 3:0)));
		$user_id = $user['userid'];
		$name = $user['username'];
		$email = $user['email'];

        //生成uk
        $auth = $this->_set_status(compact('user_id', 'name'));
        $status = 1;
        $msg  = '登录成功';
        $glod = 0;
        if(!$user_id){
            $msg = '登录失败';
            $status = 0;
        }else{
            $rank = loader::model('exam_rank', 'exam');
            $golds = $rank->select("user_id={$user_id}", 'gold');

            foreach ($golds as $v) {
                $glod = $glod + $v['gold'];
            }
        }
		//当用户登录时把用户id记录进report表
		$this->report = loader::model('report', 'exam');
		$report_result = $this->report->get('uid='.$user_id);
		if(!$report_result){
			$report_data = array('uid'=>$user_id);
			$this->report->insert($report_data);
		}
		//当用户登录时把用户id记录进report表end
        $result = array(
            'status'=>$status,
            'msg'=>$msg,
            'data'=>array(
                'uk'=> $auth,
                'uid'=> $user_id,
                'uname'=> $name,
                'email'=> $email,
                'mobile'=> $user['mobile'],
                'sex'=> $user['sex'],
                'city'=> $user['city'],
                'nickname'=> $user['nickname'],
                'intro'=> $user['remarks'],
                'avatar'=> $this->member_front->get_photo($user_id, 320, 320, 'big'),
                'glod'=> $glod,
            )
        );
        die($this->json->encode($result));
    }

    /**
     * APP 修改用户信息
     *
     * @die json->array();
     */
    public function info()
    {
        $uk 		= value($_REQUEST, 'uk');
        $uid        = $_REQUEST['uid'] ? intval($_REQUEST['uid']) : 0;
        $type		= trim(value($_REQUEST, 'type'));
        $val		= urldecode(value($_REQUEST, 'val'));
        $status = '-1';
        if(!$type || !$uid || !$uk || !$val){
            $msg = '关键数据为空!';
            $res = array('status'=>$status,'msg'=>$msg,'data'=>array());
            echo $this->json->encode($res);exit;
        }
        if (!ukdecode($uid, $uk)){
            $msg = 'cat‘t find member';
            $res = array('status'=>2,'msg'=>$msg,'data'=>array());
            echo $this->json->encode($res);exit;
        }
		//由于修改了数据库字段名称，又不想升级APP版本所以暂时强制修改字段名。等APP版本更新时再做处理
		if($type=='intro'){
			$type = 'remarks';
		}
		
		$result = $this->member_detail->update(array($type=>$val), $uid);
        $msg  = 'update '.$type. ' field is failure!';
        if($result){
            $msg  = 'success';
            $status = 1;
        }
        $res = array('status'=>$status,'msg'=>$msg,'data'=>array('type'=>$type, 'val'=>$val));
        echo $this->json->encode($res);
        exit;
    }

    /**
     * 检查请求的数据
     * @return array
     */
    private function valid_data(){
        $check_data 				= array();
        $check_data['uname'] 		= urldecode(value($_REQUEST, 'uname'));
        $check_data['passwd'] 		= urldecode(value($_REQUEST, 'passwd'));
        if(!$check_data['uname'] || !$check_data['passwd']){
            $result = array(
                'status'=>'-1',
                'msg'=>'用户名或密码不能为空!',
                'data'=>array(

                )
            );
            die($this->json->encode($result));
        }

        if(!$check_data['pt']){
            $check_data['pt'] = 'android';
        }

        if(!$check_data['auto_login']){
            $check_data['auto_login'] = 'false';
        }

        return $check_data;
    }

    /**
     * 获取auth
     *
     * @param $data
     * @return string
     */
    private function _set_status($data) {
        if (is_array($data) && $data) {
            $member_id = value($data, 'user_id', 0);
            $name = value($data, 'name', '');
            if ($member_id) return  uc_authcode(md5($member_id . $name . LOGIN_KEY . mt_rand()) . "\t" . $member_id, 'ENCODE', LOGIN_KEY);
        }
    }
}
