<?php
class plugin_ucenter extends object
{
	private $m;
	
	public function __construct(& $model)
	{
		$this->m = $model;
		$this->ucenter = loader::model('ucenter', 'member');
		$this->member_detail = loader::model('member_detail', 'member');
	}

	public function after_password()
	{
		$username = $this->m->username;
		$password = $this->m->password;
		$last_password = $this->m->last_password;
		$return = $this->ucenter->edit($username,$last_password,$password,'',1);
		if($return !== true)
		{
			$this->error = $return['message'];
			return false;
		}
	}

	public function after_force_password()
	{
		$username = $this->m->username;
		$password = $this->m->password;
		$return = $this->ucenter->edit($username, '', $password, '', 1);
		if ($return !== true)
		{
			$this->error = $return['message'];
			return false;
		}
	}

	public function before_new_add()
	{
		$data = $this->m->data;
		$r = $this->ucenter->register($data['username'],$data['password'],$data['email']);

		if(isset($r['state']) && !$r['state'])
		{
			$this->error = $r['message'];
		}
		else
		{
			$r['groupid'] = $data['groupid'];

			$r['password'] = $this->m->make_password($r['password']);
			$this->m->insert($r);
			$this->member_detail->add($r);
			$this->m->userid = $r['userid'];
		}
	}

	public function before_new_edit()
	{
		$user = $this->m->user;
		$data = $this->m->data;
		$this->ucenter->user_edit($user['username'], $data['password'], '', $data['email'],1);
	}
	
	public function before_delete()
	{
		return uc_user_delete($this->m->userid);
	}
	
	public function before_login()
	{
		$this->login();
	}

	private function login()
	{
		$this->m->error = null;
		$username = $this->m->username;
		$password = $this->m->password;
		$user = $this->ucenter->login($username, $password);
		if($user['userid'] < 0)
		{
			$this->m->error = $user['error'];
			return;
		}
		//如果cmstop中无此用户，则复制到cmstop中
		if(!$this->m->m)
		{
			$this->ucenter->get_ucdb();
			$ucdb = & $this->ucenter->ucdb;
			$ucuser = $ucdb->get("SELECT * FROM #table_members WHERE uid = ".$user['userid']);
			$ctuser = array(
				'userid'		=>	$ucuser['uid'],
				'username'		=>	$ucuser['username'],
				'name'			=>	$ucuser['username'],
				'password'		=>	$ucuser['password'],
				'salt'			=>	$ucuser['salt'],
				'email'			=>	$ucuser['email'],
				'regtime'		=>	$ucuser['regdate'],
				'lastloginip'	=>	$ucuser['lastloginip'],
				'lastlogintime'	=>	$ucuser['lastlogintime'],
			);
			$this->m->insert($ctuser);
			$this->member_detail->insert($ctuser);
			$this->m->m = $ctuser;
		}
		else
		{
			//UC密码正确 CT不正确
			if(!$this->m->check_password($this->m->m['password'], $password, $this->m->m['salt']))
			{
				$new_password = $this->m->make_password($password);
				$this->m->set_field('password', $new_password, "`userid`={$user['userid']}");
				$this->m->m['password'] = $new_password;
			}
		}
	}

	//同步登录：由于后台登录采用ajax方式，所以在这里不使用js方式通知其他应用，而直接用get方法
	public function after_login()
	{
		$js = $this->ucenter->sysnlogin($this->m->m['userid']);
		preg_match_all('#src="(.*?)"#', $js, $temp);
		$this->m->m['ucsynlogin'] = $temp[1];	//js地址数组
	}

	public function after_logout()
	{
		$js = $this->ucenter->logout();
		preg_match_all('#src="(.*?)"#', $js, $temp);
		$this->m->synclogout = $temp[1];	//js地址数组
	}
}