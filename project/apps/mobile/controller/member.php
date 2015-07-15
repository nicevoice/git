<?php

class controller_member extends mobile_controller_abstract
{
	private $member;
	
	function __construct(& $app)
	{
		parent::__construct($app);
		if (!$this->setting['open']) $this->showmessage($this->setting['webname'].'的mobile服务已关闭', 'index.php');
		$session = & factory::session();
		$session->start();
	}

	function index()
	{
		exit;
	}
	
	function seccode()
	{
		import('helper.seccode');
		$this->seccode = new seccode();
		$this->seccode->image();
	}
	
	function seccode_valid()
	{
		import('helper.seccode');
		$result = $this->seccode->valid(true) ? array('state'=>'success') : array('state'=>'fail', 'message'=>'验证码不正确');
		echo $this->json->encode($result);
	}
	
	function login()
	{
		$this->member = loader::model('member_front', 'member');
		if($this->_userid)
		{
			$return = array('state'=>true, 'userid' =>$this->_userid, 'username' =>$this->_username, 'message'=>'您已经登录了');
			$data = $this->json->encode($return);
			$data = (isset($_GET['jsoncallback']))? $_GET['jsoncallback']."($data);": $data;
			exit($data);
		}

		if(!isset($_POST['username'])) $_POST['username'] = $_GET['username'];
		if(!isset($_POST['password'])) $_POST['password'] = $_GET['password'];
		$username = $_POST['username'];
		$password = $_POST['password'];
		$_SESSION['needseccode'] = 1;
		if(!$this->_check_login())
		{
            $return = array('state'=>false, 'error'=>$this->error);
			$data = $this->json->encode(array($return));
			$data = (isset($_GET['jsoncallback']))? $_GET['jsoncallback']."($data);": $data;
			exit($data);
		}

		$cookietime = value($_POST, 'cookietime', 86400*30);

        if ($m = $this->member->login($username, $password, $cookietime)) {
            $return = array('state' => true, 'userid'=>$m['userid'], 'username' =>$m['username'], 'message'=>'登录成功'.$m['ucsynlogin']);
        } else {
            $_SESSION['needseccode'] = 1;
            $return = array('state' => false, 'error' => $this->member->error());
        }

		$data = $this->json->encode(array($return));
		$data = (isset($_GET['jsoncallback']))? $_GET['jsoncallback']."($data);": $data;
		echo $data;
	}

	function logout()
	{
		$this->member = loader::model('member_front', 'member');
		if(!$this->_userid)
		{
			$return = array('state'=>false, 'error'=>'您还没有登录');
		}
		else
		{
			$ucsynlogout = '';
			$ucsynlogout = $this->member->logout();
			$return = array('state'=>true, 'message'=>'退出成功'.$ucsynlogout);
		}
		$data = $this->json->encode(array($return));
		$data = (isset($_GET['jsoncallback']))? $_GET['jsoncallback']."($data);": $data;
		echo $data;
	}

	private function _check_login()
	{
		if($_SESSION['needseccode'])
		{
			import('helper.seccode');
			$seccode = new seccode();
			if (!$seccode->valid())
			{
				$this->error = '验证码不正确';
				return false;
			}
		}
		if(empty($_POST['username']))
		{
			$this->error = '用户名不能为空';
			return false;
		}
		if(empty($_POST['password']))
		{
			$this->error = '密码不能为空';
			return false;
		}
		return true;
	}
}