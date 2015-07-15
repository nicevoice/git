<?php

class model_member extends model implements SplSubject
{
	public $userid,
			$username,
			$register_fields = array('username', 'password', 'email');
	public $m, $synclogout;
	private $member_detail;
	private $observers = array();
	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'member';
		$this->_primary = 'userid';
		$this->_fields = array('userid', 'username', 'password', 'email', 'groupid', 'avatar', 'regip', 'regtime', 'lastloginip', 'lastlogintime', 'logintimes', 'posts', 'comments', 'pv', 'credits','salt');
		$this->_readonly = array('userid', 'username', 'regip', 'regtime');
		$this->_create_autofill = array('regip'=>IP, 'regtime'=>TIME, 'groupid'=>setting('member', 'groupid'),'salt' => '');
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array('username'=>array('not_empty'=>array('用户不能为空')), 
									'email'=>array('not_empty'=>array('E-mail不能为空'),
													'email' =>array('E-mail格式不正确')
												)
									);

		$this->member_detail = loader::model('member_detail','member');
		define('UCENTER',setting('member', 'uc'));
	}
	
	function add($data)
	{
		if($this->exists('username',$data['username']))
		{
			$this->error = '用户名已经存在';
			return false;
		}
		if($this->exists('email',$data['email']))
		{
			$this->error = 'E-mail已经存在';
			return false;
		}
		
		return $this->insert($data);
	}
	
	function edit($userid, $data)
	{
		return $this->update($data, array('userid' => $userid));
	}
	
	function password($userid, $password, $last_password, $isMd5 = false)
	{
		//原密码
		$r = $this->get($userid, 'username,password,email,salt');
		if (!$r)
		{
			$this->error = '用户不存在';
			return false;
		}

		if(!$this->check_password($r['password'], $last_password, $r['salt'], $isMd5))
		{
			$this->error = '原密码不正确';
			return false;
		}
		
		$this->userid = $r['userid'];
		$this->username = $r['username'];
		$this->password = $password;
		$this->last_password = $last_password;
		$this->event = 'after_password';
		$this->notify();
		if($this->error)
		{
			return false;
		}
		return $this->set_field('password', self::make_password($password), "userid=$userid");
	}
	
	function force_password($userid, $password)
	{
		$r = $this->get($userid);
		$this->userid = $r['userid'];
		$this->username = $r['username'];
		$this->password = $password;
		$this->event = 'after_force_password';
		$this->notify();
		if($this->error)
		{
			return false;
		}
		return $this->set_field('password', self::make_password($password), array('userid' => $userid));
	}
	
	function update_detail($data, $where)
	{
		return $this->member_detail->update($data,$where);
	}
	
	public function remarks($data, $where)
	{
		if (!$this->member_detail->update(array('remarks' => $data['remarks']),$where))
		{
			$this->error = $this->member_detail->error();
			return false;
		}
		return true;
	}
	
	public function new_add($data)
	{
		if(!$this->validate($data))
		{
			return false;
		}
		
		$this->data = $data;
		$this->event = 'before_new_add';
		$this->notify();
		
		if($this->error)
		{
			return false;
		}
		
		if($this->userid)
		{
			return $this->userid;
		}
		
		$data['password'] = self::make_password($data['password']);
		
		$userid = $this->insert($data);
		if ($userid)
		{
			$data['userid'] = $userid;
			$this->member_detail->add($data);
		}
		return $userid ? $userid : false;
	}
	
	public function new_edit($userid, $data)
	{
		$user = $this->get($userid);
		if($exists = $this->get("`email`='$data[email]' AND `userid` != '$userid'"))
		{
			$this->error = 'E-mail已经存在';
			return false;
		}
		//print_r($exists);exit;
		$this->data = $data;
		$this->user = $user;
		$this->event = 'before_new_edit';
		$this->notify();
		$data['birthday'] = (empty($data['birthday']))? null : implode('-',$data['birthday']);
		if(!($this->edit($userid, $data) && $this->member_detail->update($data, "`userid`='{$userid}'")))
		{
			return false;
		}
		
		return true;
	}
	
	public function del($userid)
	{
		if(empty($userid)) return false;
		$this->userid = $userid;
		$this->event = 'before_delete';
		$this->notify();
		$this->event = 'del_bind';
		$this->notify();
		if($this->error) return false;
		$where = "`userid` IN ({$userid})";
		return $this->delete($where);
	}
	
	public function unlock($userid)
	{
		if(empty($userid)) return false;
		$username = username($userid);
		$this->db->delete("delete from #table_member_login_log where username=? AND succeed=0", array($username));
		$r = $this->db->update("update #table_member set locked=? where username=?", array(0, $username));

		if($this->error) return false;
		return $r;
	}
	
	public function validate($data)
	{
		if($this->exists('username',$data['username']))
		{
			$this->error = '用户名已经存在';
			return false;
		}
		if($this->exists('email',$data['email']))
		{
			$this->error = 'E-mail已经存在';
			return false;
		}
		return true;
	}
	
	function register_for_admin($data)
	{
		
		$this->db->beginTransaction();
		try
		{
			$user = $this->get(array('username'=>$data['username']));
			if ($user) {
				if ($user['groupid']==1)
				{
					throw new Exception("用户'{$data[username]}'已经是管理员");
				}
				else if (! $data['upgrade'])
				{
					throw new Exception("用户名'{$data[username]}'已存在");
				}
				else
				{
					if ($this->update(array('groupid'=>1), $user['userid']))
					{
						$data['userid'] = $user['userid'];
					}
					else
					{
						throw new Exception("提升管理员'{$data[username]}'失败");
					}
				}
			} else {
				$this->error = null;
				if (!($userid = $this->new_add($data)))
				{
					throw new Exception($this->error);
				}
				$data['userid'] = $userid;
			}
		}
		catch (Exception $e)
		{
			$this->db->rollBack();
			$this->error = $e->getMessage();
			if (! $this->error)
			{
				$err = $this->db->errorInfo();
				$this->error = $err[2];
			}
			return false;
		}
		$this->db->commit();
		return $data;
	}
	
	function login($username, $password, $cookietime = 0)
	{
		$this->m = $this->get_by('username', $username, 'userid,username,password,groupid,salt');
		$this->error = null;
		$this->username = $username;
		$this->password = $password;
		$this->event = 'before_login';
		$this->notify();
		if($this->error)
		{
			return false;
		}
		$login_log = loader::model('member_login_log', 'member');

		if (!$login_log->valid($username))
		{
			$this->error = '登录失败次数过多，已被系统锁定';
			$this->db->update("update #table_member set locked=? where username=?", array(TIME, $username));
			return false;
		}
		if (!$this->m)
		{
			$this->error = "$username 不存在";
			return false;
		}

		if(!$this->check_password($this->m['password'], $password, $this->m['salt']))
		{
			$login_log->add(array('username'=>$username, 'succeed'=>0));
			$this->error = '密码错误';
			return false;
		}
		
		$this->update(array('lastloginip'=>IP, 'lastlogintime'=>TIME), "userid=".$this->m['userid']);
		$this->set_inc('logintimes',$this->m['userid']);
		$cookie = & factory::cookie();
		$cookie->set('auth', str_encode($this->m['userid']."\t".$this->m['username']."\t".$this->m['groupid'], config('config', 'authkey')), $cookietime);
		$cookie->set('userid', $this->m['userid'], $cookietime);
		$cookie->set('username', $this->m['username'], $cookietime);
		$cookie->set('rememberusername', $this->m['username'], TIME + 2592000);
		unset($_SESSION['needseccode']);
		$login_log->add(array('username'=>$username, 'succeed'=>1));
		$this->event = 'after_login';
		$this->notify();
		return $this->m;
	}

	function internalLogin()
	{
		$internal = array(config('config', 'internal_userid'), 'internal', 1);
		$cookie = & factory::cookie();
		$cookie->set('auth', str_encode(implode("\t", $internal), config('config', 'authkey')), 0);
		$cookie->set('userid', $internal[0], 0);
		$cookie->set('username', $internal[1], 0);
		$cookie->set('groupid', $internal[2], 0);
		return $internal;
	}
	
	function logout()
	{
		$cookie = & factory::cookie();
		$cookie->set('auth');
		$cookie->set('userid');
		$cookie->set('username');

		$this->event = 'after_logout';
		$this->notify();

		if(is_array($this->synclogout))
		{
			return array('state' => true, 'synclogout' => $this->synclogout);
		}
		return array('state' => true);
	}

	function getmemberlist($sql)
	{
		$data = $this->db->select($sql);
		return $data;
	}
	
	function group_persons()
	{
		$sql = "SELECT groupid, COUNT(*) persons FROM #table_member GROUP BY groupid";
		$query = $this->db->query($sql);
		$return = array();
		foreach ($query as $value)
		{
			$return[$value['groupid']] = $value['persons'];
		}
		return $return;
	}

	static function online()
	{
		$cookie = & factory::cookie();
		$str = $cookie->get('auth');
		return $str ? explode("\t", str_decode($str, config('config', 'authkey'))) : false;
	}

	static function make_password($password)
	{
		return md5($password);
	}

	public function check_password($password, $inputpwd, $salt = '', $isMd5=false)
	{
		$md5pass = $isMd5 ? $inputpwd : md5($inputpwd);
		if($password == $md5pass) return true;
		if($password == md5($md5pass.$salt)) return true;
		return false;
	}
	
	public function getProfile($userid, $detail = true)
	{
		$r = $this->get($userid);
        if (! $r) return false;

		$d = array();
		if($detail)
		{
			$d = $this->member_detail->get($userid);
		}
		return $d && is_array($d) ? array_merge($r, $d) : $r;
	}
	
	function set_photo_path($userid)
	{
		$dir = array();
		$userid = sprintf("%09d", $userid);
		$dir[] = substr($userid, 0, 3);
		$dir[] = substr($userid, 3, 2);
		$dir[] = substr($userid, 5, 2);
		$dir = implode('/',$dir);
		$name = substr($userid, -2);
		$return = array($dir, $name);
		return $return;
	}

	function _after_select(& $data, $multiple)
	{
		if (empty($data)) return $data;
		
		$group = loader::model('member_group', 'member');
		$groups = $group->groups();
		import('helper.iplocation');
		$iplocation = new iplocation();
		if ($multiple) 
		{
			foreach ($data as $key => $value) 
			{
				$data[$key]['group'] = $groups[$value['groupid']];
				$data[$key]['regtime'] = date('Y-m-d H:i', $value['regtime']);
				$data[$key]['lastlogintime'] = empty($value['lastlogintime']) ? '' : date('Y-m-d H:i', $value['lastlogintime']);
				$data[$key]['location'] = $iplocation->get($value['lastloginip']);
				unset($data[$key]['password']);
			}
		}
		else
		{
			$data['group'] = $groups[$data['groupid']];
			$data['regtime'] = date('Y-m-d H:i', $data['regtime']);
			$data['location'] = $iplocation->get($data['lastloginip']);
			$data['lastlogintime'] = empty($data['lastlogintime']) ? '' : date('Y-m-d H:i', $data['lastlogintime']);
		}
	}

	public function attach(SplObserver $observer)
	{
		$this->observers[] = $observer;
	}

	public function detach(SplObserver $observer)
	{
		if($index = array_search($observer, $this->observers, true)) unset($this->observers[$index]);
	}

	public function notify()
	{
		foreach ($this->observers as $observer)
		{
			$observer->update($this);
		}
	}
}