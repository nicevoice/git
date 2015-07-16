<?php

class plugin_phpwind extends object
{
	private $m;
	
	public function __construct(& $model)
	{
		$this->m = $model;
		$this->member_detail = loader::model('member_detail', 'member');
		include_once PUBLIC_PATH.'app/api/pw_client/uc_client.php';
	}

	public function after_password()
	{
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

	public function after_force_password()
	{
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

	public function before_new_add()
	{
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

	public function before_new_edit()
	{
		$user = $this->m->user;
		$data = $this->m->data;
		if(isset($data['email']) && $data['email'] != $user['email'])
		{
			$return = uc_user_edit($user['userid'], $user['username'], '', '', $data['email']);
			if($return < 0)
			{
				$this->m->error = '修改资料出现错误';
				return false;
			}
		}
	}
	
	public function before_delete()
	{
		$return = uc_user_delete($this->m->userid);
		if($return <1)
		{
			$this->m->error = '删除用户出现错误';
		}
	}
	
	public function before_login()
	{
		$this->login();
	}

	private function login()
	{
		$username = $this->m->username;
		$password = $this->m->password;
		$user = uc_user_login($username,md5($password),0);
		$status = $user['status'];
		if($status < 0)
		{
			if($status == -1) {
				$this->m->error = '用户名不存在';
			} else if($status == -2) {
				$this->m->error = '密码不正确';
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
			$ctuser['ucsynlogin'] = $uer['synlogin'];
			$this->m->m = $ctuser;
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
	}

	//同步登录：由于后台登录采用ajax方式，所以在这里不使用js方式通知其他应用，而直接用get方法
	public function after_login()
	{
		$js = $this->m->m['ucsynlogin'];
		preg_match_all('#src="(.*?)"#', $js, $temp);
		$this->m->m['ucsynlogin'] = $temp[1];	//js地址数组
	}

	public function after_logout()
	{
		$js = uc_user_synlogout();
		preg_match_all('#src="(.*?)"#', $js, $temp);
		$this->m->synclogout = $temp[1];	//js地址数组
	}
}