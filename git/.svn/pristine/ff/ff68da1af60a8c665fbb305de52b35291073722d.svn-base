<?php
class plugin_ucenter extends object
{
	private $m, $enabled = false;
	
	public function __construct(& $model)
	{
		if (!defined('UCENTER') || UCENTER != 'ucenter')
		{
			return;
		}
		$this->enabled = true;
		$this->m = $model;
		$this->ucenter = loader::model('ucenter', 'member');
		$this->member_detail = loader::model('member_detail', 'member');
	}
	
	public function before_login()
	{
		if (! $this->enabled) return;
		$this->login();
	}

	public function after_login()
	{
		if (! $this->enabled) return;
		$this->m->m['ucsynlogin'] = $this->ucenter->sysnlogin($this->m->m['userid']);
	}

	public function before_check_email()
	{
		if (! $this->enabled) return;
		$email = $this->m->email;
		$rs = $this->ucenter->validate($email, 'email');
		if(!$rs['state']) 
		{
			$this->m->error = $rs['message'];
		}
	}

	public function before_check_username()
	{
		if (! $this->enabled) return;
		$username = $this->m->username;
		$rs = $this->ucenter->validate($username, 'username');
		if(!$rs['state']) $this->m->error = $rs['message'];
	}

	public function before_get_photo()
	{
		if (! $this->enabled) return;
		$userid = $this->m->userid;
		$size = $this->m->size;
		$this->m->photo = $this->ucenter->get_photo($userid, $size);
	}

	public function after_password()
	{
		if (! $this->enabled) return;
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

	public function after_email()
	{
		if (! $this->enabled) return;
		$username = $this->m->username;
		$password = $this->m->password;
		$email = $this->m->email;
		$return = $this->ucenter->edit($username,$password,'',$email);
		if($return !== true)
		{
			$this->error = $return['message'];
			return false;
		}
	}

	public function after_getProfile()
	{
		if (! $this->enabled) return;
		$this->m->d['uc_avatar_upload'] = uc_avatar($this->m->_userid);
	}
	
	public function after_logout()
	{
		if (! $this->enabled) return;
		$this->m->synclogout = $this->ucenter->logout();
	}

	public function before_register()
	{
		if (! $this->enabled) return;
		$this->register();
	}

	private function register()
	{
		if (! $this->enabled) return;
		$data = $this->m->data;
		$user = $this->ucenter->register($data['username'], $data['password'], $data['email']);
		if(isset($user['state']) && !$user['state'])
		{
			$this->m->error = $user['message'];
			return false;
		}
		$user['password'] = md5($user['password']);
		$this->m->insert($user);
		$this->member_detail->add($user);
		$this->m->userid = $user['userid'];
	}

	private function login()
	{
		if (! $this->enabled) return;
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
            $ctuser['groupid'] = table('member', $ctuser['userid'], 'groupid');
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
}