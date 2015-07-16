<?php

class plugin_phpwind extends object
{
	private $m, $member_detail, $enabled = false;
	
	public function __construct(& $model)
	{
		if (!defined('UCENTER') || UCENTER != 'phpwind')
		{
			return;
		}
		$this->enabled = true;
		$this->m = $model;
		$this->member_detail = $model->member_detail;
		include_once PUBLIC_PATH.'app/api/pw_client/uc_client.php';
	}
	
	public function before_login()
	{
		if (! $this->enabled) return;
		$username = $this->m->username;
		$password = $this->m->password;
		$user = uc_user_login($username,md5($password),0);
		$status = $user['status'];
		if($status < 0)
		{
			if($status == -1) {
				$this->m->error = '用户名不存在';
			} else if($status == -2) {
				$this->m->error = '用户名不正确';
			} else if($status == -3) {
				$this->m->error = '存在重复的E-mail';
			}
			return false;
		}
		
		//如果cmstop中无此用户，则复制到cmstop中
		if(!$this->m->m)
		{
			$ctuser = array(
				'userid'		=>	$user['uid'],
				'username'		=>	$user['username'],
				'name'			=>	$user['username'],
				'password'		=>	$this->m->make_password($password),
				'email'			=>	$user['email'],
				'regtime'		=>	$user['regdate']
			);
			$this->m->insert($ctuser);
			$this->member_detail->insert($ctuser);
			$this->m->m = $this->m->get_by('userid', $user['uid'], 'userid,username,password,groupid,salt');
		}
		else
		{
			//UC密码正确 CT不正确
			if(!$this->m->check_password($this->m->m['password'], $password, $this->m->m['salt']))
			{
				$new_password = $this->m->make_password($password);
				$this->m->set_field('password', $new_password, "`userid`={$user['uid']}");
				$this->m->m['password'] = $new_password;
			}
		}

		$this->m->ucsynlogin = $user['synlogin'];
	}

	public function after_login()
	{
		if (! $this->enabled) return;
		$this->m->m['ucsynlogin'] = $this->m->ucsynlogin;
	}
	
	public function before_check_email()
	{
		if (! $this->enabled) return;
		$email = $this->m->email;
		$r = uc_user_checkemail($email);
		if($r < 0 )
		{
			if($r == -3) {
				$this->m->error = 'E-mail格式不正确';
			} else if($r == -4) {
				$this->m->error = 'E-mail已经存在';
			} else {
				$this->m->error = '未知错误';
			}
			
		}
	}
	
	public function before_check_username()
	{
		if (! $this->enabled) return;
		$username = $this->m->username;
		$r = uc_user_checkname($username);
		if($r < 0 )
		{
			if($r == -1) {
				$this->m->error = '用户名不合法';
			} else if($r == -2) {
				$this->m->error = '用户名已经存在';
			} else {
				$this->m->error = '未知错误';
			}
		}
	}

	public function before_get_photo()
	{
		if (! $this->enabled) return;
		$userid = $this->m->userid;
		$size = $this->m->size;
		//取头像
	}
	
	public function after_password()
	{
		if (! $this->enabled) return;
		$userid = $this->m->userid;
		$username = $this->m->username;
		$password = $this->m->password;
		$return = uc_user_edit($userid, $username, '', md5($password), '');
		if($return < 0)
		{
			$this->m->error = '修改密码出现错误';
			return false;
		}
	}
	
	public function after_email()
	{
		if (! $this->enabled) return;
		$userid = $this->m->userid;
		$username = $this->m->username;
		$password = $this->m->password;
		$email = $this->m->email;
		$return = uc_user_edit($userid, $username, '', '', $email);
		if($return < 0)
		{
			$this->m->error = '修改E-mail出现错误';
			return false;
		}
	}

	public function after_getProfile()
	{
		//取上传头像的地方
		//$this->m->d['uc_avatar_upload'] = uc_avatar($this->m->_userid);
	}
	
	public function after_logout()
	{
		if (! $this->enabled) return;
		$this->m->synclogout = uc_user_synlogout();
	}
	
	public function before_register()
	{
		if (! $this->enabled) return;
		$data = $this->m->data;
		$uid = uc_user_register($data['username'],md5($data['password']),$data['email']);

		if($uid >0)
		{
			$data['password'] =  $this->m->make_password($data['password']);
			$data['userid'] = $uid;
			$this->m->insert($data);
			$this->member_detail->add($data);
			$this->m->userid = $data['userid'];
		}
		else
		{
			if($uid == -1) {
				$this->m->error = '注册用户名非法';
			} else if($uid == -2) {
				$this->m->error = '已经存在相同的用户名';
			} else if($uid == -3) {
				$this->m->error = 'E-mail格式不正确';
			} else if($uid == -4) {
				$this->m->error = 'E-mail已经存在';
			}
			return false;
		}
		
	}
}