<?php
//Ucenter
class model_ucenter extends model
{
	public $charset_cmstop = 'UTF-8';
	public $charset_ucenter = 'UTF-8';
	public $charset_phpwind = 'UTF-8';
	public $charset_match = true;
	public $ucdb;

	public function __construct()
	{
		parent::__construct();
		
		$set = setting('member');
		$set['uc_dbtablepre'] = '`'.$set['uc_dbname'].'`.'.$set['uc_dbtablepre'];
        if ($set['uc_dbport'])
        {
            $set['uc_dbhost'] = $set['uc_dbhost'].':'.$set['uc_dbport'];
            unset($set['uc_dbport']);
        }
		$set = array_change_key_case($set, CASE_UPPER);
		foreach($set as $k => $v)
		{
			if(preg_match('/^UC_/',$k)) define($k,$v);
		}
		$this->charset_cmstop = strtoupper(config('config','charset'));
		$this->charset_ucenter = ($set['UC_DBCHARSET'] == 'utf8')?'UTF-8':strtoupper($set['UC_DBCHARSET']);
		if($this->charset_cmstop != $this->charset_ucenter)
		{
			$this->charset_match = false;
		}
		define('UC_PPP',20);
		include_once PUBLIC_PATH.'app/api/uc_client/client.php';
	}
	
	public function __call($name, $arguments)
	{
		$functionName = 'uc_'.$name;
		if(!function_exists($functionName))
		{
			$this->error = 'method is not exists';
			return false;
		}
		if(!$this->charset_match)
		{
			$arguments = str_charset($this->charset_cmstop, $this->charset_ucenter, $arguments);
		}
		$return  = call_user_func_array($functionName, $arguments);
		if(!$this->charset_match)
		{
			$return = str_charset($this->charset_ucenter, $this->charset_cmstop, $return);
		}
		return $return;
	}
	
	public function validate($value, $type = 'username')
	{
		if(!$this->charset_match)
		{
			$value = str_charset($this->charset_cmstop, $this->charset_ucenter, $value);
		}
		$return = array('state' => false,'message'=>'undefined');
		if($type == 'username')
		{
			$ucresult = uc_user_checkname($value);
			if($ucresult == -1) {
				$return['message'] = '用户名不合法';
			} elseif($ucresult == -2) {
				$return['message'] = '包含禁止关键字';
			} elseif($ucresult == -3) {
				$return['message'] = '用户名已经存在';
			} else {
				$return['state'] = true;
				$return['message'] = '成功';
			}
		}
		else if($type == 'email')
		{
			$ucresult = uc_user_checkemail($value);
			if($ucresult == -4) {
				$return['message'] = 'Email 格式有误';
			} elseif($ucresult == -5) {
				$return['message'] = 'Email 不允许注册';
			} elseif($ucresult == -6) {
				$return['message'] = '该 Email 已经被注册';
			} else {
				$return['state'] = true;
				$return['message'] = '成功';
			}
		}
		
		return $return;
	}
	
	public function register($username, $password, $email)
	{
		if(!$this->charset_match)
		{
			$username = str_charset($this->charset_cmstop, $this->charset_ucenter, $username);
		}
		$userid = uc_user_register($username, $password, $email);
		if($userid <= 0) {
			$return  = array('state'=>false,'message' => '');
			if($userid == -1) {
				$return['message'] = '用户名不合法';
			} elseif($userid == -2) {
				$return['message'] = '包含不允许注册的词语';
			} elseif($userid == -3) {
				$return['message'] = '用户名已经存在';
			} elseif($userid == -4) {
				$return['message'] = 'Email 格式有误';
			} elseif($userid == -5) {
				$return['message'] = 'Email 不允许注册';
			} elseif($userid == -6) {
				$return['message'] = '该 Email 已经被注册';
			} else {
				$return['message'] = '注册错误';
			}
		}
		else
		{
			if(!$this->charset_match)
			{
				$username = str_charset($this->charset_ucenter, $this->charset_cmstop, $username);
			}
			$return = array(
				'userid' 	=> $userid,
				'username' 	=> $username,
				'password' 	=> $password,
				'email' 	=> $email
			);
		}
		return $return;
	}
	
	public function login($username, $password)
	{
		$return = array();
		if(!$this->charset_match)
		{
			$username = str_charset($this->charset_cmstop, $this->charset_ucenter, $username);
		}
		list($uid, $username, $password, $email) = uc_user_login($username, $password);
		if($uid > 0) {
			$return['userid'] = $uid;
			$return['username'] = $username;
			$return['password'] = $password;
			$return['email'] = $email;
			if(!$this->charset_match)
			{
				$return = str_charset($this->charset_ucenter, $this->charset_cmstop, $return);
			}
		} elseif($uid == -1) {
			$return['userid'] = $uid;
			$return['error'] = '用户不存在,或者被删除';
		} elseif($uid == -2) {
			$return['userid'] = $uid;
			$return['error'] = '密码错误';
		} else {
			$return['userid'] = $uid;
			$return['error'] = '未知错误';
		}
		return $return;
	}
	
	public function edit($username, $oldpw, $newpw, $email,$ignoreoldpw = 0)
	{
		if(!$this->charset_match)
		{
			$username = str_charset($this->charset_cmstop, $this->charset_ucenter, $username);
			$oldpw = str_charset($this->charset_cmstop, $this->charset_ucenter, $oldpw);
			$newpw = str_charset($this->charset_cmstop, $this->charset_ucenter, $newpw);
			$email = str_charset($this->charset_cmstop, $this->charset_ucenter, $email);
		}
		$ucresult = uc_user_edit($username , $oldpw , $newpw, $email, $ignoreoldpw);
		if($ucresult < 0) {
			$return  = array('state'=>false,'message' => '');
			if($ucresult == -1) {
				$return['message'] = '旧密码不正确';
			} elseif($ucresult == -4) {
				$return['message'] = 'Email 格式有误';
			} elseif($ucresult == -5) {
				$return['message'] = 'Email 不允许注册';
			} elseif($ucresult == -6) {
				$return['message'] = '该 Email 已经被注册';
			}
			return $return;
		}
		else
		{
			return true;
		}
	}
	
	public function sysnlogin($uid)
	{
		return uc_user_synlogin($uid);
	}
	
	public function logout()
	{
		return uc_user_synlogout();
	}
	
	public function get_photo($userid, $size='small')
	{
		$size = in_array($size, array('big', 'middle', 'small')) ? $size : 'small';
		if(!uc_check_avatar($userid,$size))
		{
			return UC_API.'/images/noavatar_'.$size.'.gif';
		}
		$avatarfile = '';
		$userid = abs(intval($userid));
		$userid = sprintf("%09d", $userid);
		$dir1 = substr($userid, 0, 3);
		$dir2 = substr($userid, 3, 2);
		$dir3 = substr($userid, 5, 2);
		$avatarfile = $dir1.'/'.$dir2.'/'.$dir3.'/'.substr($userid, -2)."_avatar_$size.jpg";
		
		return UC_API.'/data/avatar/'.$avatarfile;
	}

    /**
     * 测试ucenter数据库设置
     * @param array $data
     * @return array
     */
    function uctest($data)
    {
        $dbhost = isset($data['dbport']) && $data['dbport']
            ? $data['dbhost'] . ':' . $data['dbport']
            : $data['dbhost'];
        if(!$link = mysql_connect($dbhost, $data['dbuser'], $data['dbpw'], 1))
        {
            return array('state'=>false,'message'=>'无法连接数据库服务器'.mysql_error());
        }
        if(!@mysql_select_db($data['dbname'], $link))
        {
            return array('state'=>false,'message'=>'无法找到数据库：'.$data['dbname']);
        }
        if(!@mysql_fetch_array(mysql_query("SHOW TABLES LIKE '{$data['dbtablepre']}members'")))
        {
            return array('state'=>false,'message'=>'数据表前缀错误：'.$data['dbtablepre']);
        }
        return array('state'=>true,'message'=>'连接成功');
    }

	/**
	 * 返回连接uc数据库的db类
	 */
	public function get_ucdb()
	{
		if($this->ucdb) return;
		$set = setting('member');
		$this->ucdb = factory::db(array(
			'driver' => $set['uc_connect'],
			'host' => $set['uc_dbhost'],
			'port' => $set['uc_dbport'],
			'username' => $set['uc_dbuser'],
			'password' => $set['uc_dbpw'],
			'dbname' => $set['uc_dbname'],
			'prefix' => $set['uc_dbtablepre'],
			'pconnect' => $set['uc_dbconnect'],
			'charset' => $set['uc_dbcharset'],
		));
	}
	
	public function uc_merge($start = 0,$perpage = '100',$total = 0)
	{
		$this->get_ucdb();
		$cache = & factory::cache();
		$merge_cache = $cache->get('merge_cache');
		if($merge_cache == false) {
			$r = $this->db->get("SHOW TABLE STATUS WHERE Name = '#table_member'");
			$merge_cache['ct_max'] = $r['Auto_increment'];
			$r = $this->ucdb->get("SHOW TABLE STATUS WHERE Name = '#table_members'");
			$merge_cache['uc_max'] = $r['Auto_increment'];
			$cache->set('merge_cache',$merge_cache);
		}
		$start_userid = intval($cache->get('start_userid'));
		$max_userid = intval($merge_cache['ct_max']);
		
		if(!$total)
		{
			$r = $this->db->get("SELECT COUNT(*) as `count` FROM `#table_member`");
			$total = $r['count'];
		}
		unset($r);
		$sql = "SELECT userid,username,email,password,regip,regtime,lastloginip,lastlogintime FROM #table_member WHERE userid > $start_userid AND userid < $max_userid ORDER BY userid LIMIT $perpage";
		
		$success = 0;
		$user_cache = $cache->get('user_cache');
		$is_max = $cache->get('is_max');
		
		$users = $this->db->select($sql);
		foreach($users as $user)
		{
			if(!$this->charset_match)
			{
				$user = str_charset($this->charset_cmstop, $this->charset_ucenter, $user);
			}
			extract($user);
			//处理相同用户名
			$user_exists = null;
			$user_exists = $this->ucdb->get("SELECT * FROM #table_members WHERE username='$username'");
			if($user_exists)
			{
				$user_cache[$userid] = $user_exists['uid'];
				continue;
			}
			
			if(!$user['salt'])
			{
				$salt = mt_rand(100000, 999999);
				$password = md5($password.$salt);
			}
			
			$sql_uc = "INSERT INTO #table_members SET username = '$username', password = '$password', email = '$email', salt = '$salt',regip = '$regip', regdate = '$regtime', lastloginip = '$lastloginip',lastlogintime = '$lastlogintime'";
			if($merge_cache['ct_max'] > $merge_cache['uc_max'] && $is_max == false)
			{
				$sql_uc .= ", uid = $max_userid";  //ct的用户数比较大 则第一条入Uc的是插入uid的。
				$cache->set('is_max', 1);
				$is_max = 1;
			}
			
			$insertid = $this->ucdb->insert($sql_uc);
			$this->ucdb->insert("INSERT INTO #table_memberfields SET uid = $insertid, blacklist = ''");
			$this->repair_userid($insertid,$userid); //修复userid
			$success++;
		}
		$cache->set('user_cache', $user_cache);
		$cache->set('start_userid', $user['userid']);
		
		if(($start+$perpage) > $total)
		{
			//最后处理相同用户名的 完成收尾 CT为主
			$user_cache = $cache->get('user_cache');
			foreach($user_cache as $ct_userid => $uc_uid)
			{
				$user = $this->db->get("SELECT userid,username,email,password,regip,regtime,lastlogintime FROM #table_member WHERE userid='$ct_userid'");
				if(!$this->charset_match)
				{
					$user = str_charset($this->charset_cmstop, $this->charset_ucenter,$user);
				}
				extract($user);
				if(!$user['salt'])
				{
					$salt = mt_rand(100000, 999999);
					$password = md5($password.$salt);
				}
				$set = "SET username = '$username', password = '$password', email = '$email', salt = '$salt',regip = '$regip', regdate = '$regtime', lastlogintime = '$lastlogintime'";
				
				$this->ucdb->exec("UPDATE #table_members $set WHERE uid = $uc_uid");
				$this->repair_userid($uc_uid,$ct_userid);
			}
			
			$this->db->exec('OPTIMIZE TABLE `#table_member`'); //更新表
			$cache->rm('user_cache');
			$cache->rm('is_max');
			$cache->rm('merge_cache');
			import('helper.folder');
			folder::clear(CACHE_PATH.'table'.DS); //清理缓存
			folder::clear(CACHE_PATH.'setting'.DS);
		}
		return $success;
	}
	
	public function get_pwdb()
	{
		if($this->pwdb) return;
		$set = setting('member');
		$this->pwdb = factory::db(array(
			'driver' => $set['pw_connect'],
			'host' => $set['pw_dbhost'],
			'port' => $set['pw_dbport'],
			'username' => $set['pw_dbuser'],
			'password' => $set['pw_dbpw'],
			'dbname' => $set['pw_dbname'],
			'prefix' => $set['pw_dbtablepre'],
			'pconnect' => $set['pw_dbconnect'],
			'charset' => $set['pw_dbcharset'],
		));
		$this->charset_phpwind = ($set['pw_dbcharset'] == 'utf8')?'UTF-8':strtoupper($set['pw_dbcharset']);
		if($this->charset_cmstop != $this->charset_phpwind)
		{
			$this->charset_match = false;
		}
	}
	
	public function pw_merge($start = 0,$perpage = '100',$total = 0)
	{
		$this->get_pwdb();
		$cache = & factory::cache();
		$merge_cache = $cache->get('merge_cache');
		if($merge_cache == false) {
			$r = $this->db->get("SHOW TABLE STATUS WHERE Name = '#table_member'");
			$merge_cache['ct_max'] = $r['Auto_increment'];
			$r = $this->pwdb->get("SHOW TABLE STATUS WHERE Name = '#table_members'");
			$merge_cache['uc_max'] = $r['Auto_increment'];
			$cache->set('merge_cache',$merge_cache);
		}
		$start_userid = intval($cache->get('start_userid'));
		$max_userid = intval($merge_cache['ct_max']);
		
		if(!$total)
		{
			$r = $this->db->get("SELECT COUNT(*) as `count` FROM `#table_member`");
			$total = $r['count'];
		}
		unset($r);

		$sql = "SELECT userid,username,email,password,regip,regtime,lastloginip,lastlogintime FROM #table_member WHERE userid > $start_userid AND userid < $max_userid ORDER BY userid LIMIT $perpage";
		$success = 0;
		$user_cache = $cache->get('user_cache');
		$is_max = $cache->get('is_max');

		$users = $this->db->select($sql);
		foreach($users as $user)
		{
			if(!$this->charset_match)
			{
				$user = str_charset($this->charset_cmstop, $this->charset_phpwind, $user);
			}
			extract($user);
			//处理相同用户名
			$user_exists = null;
			$user_exists = $this->pwdb->get("SELECT * FROM #table_members WHERE username='$username'");
			if($user_exists)
			{
				$user_cache[$userid] = $user_exists['uid'];
				continue;
			}
			
			$sql_uc = "INSERT INTO #table_members SET username = '$username', password = '$password', email = '$email',regdate = '$regtime'";
			if($merge_cache['ct_max'] > $merge_cache['uc_max'] && $is_max == false)
			{
				$sql_uc .= ", uid = {$merge_cache['ct_max']}";  //ct的用户数比较大 则第一条入Uc的是插入uid的。
				$cache->set('is_max', 1);
				$is_max = 1;
			}
			$insertid = $this->pwdb->insert($sql_uc);
			$this->repair_userid($insertid,$userid); //修复userid
			$success++;
		}
		$cache->set('user_cache', $user_cache);
		$cache->set('start_userid', $user['userid']);
		
		if(($start+$perpage) > $total)
		{
			//最后处理相同用户名的 完成收尾
			foreach($user_cache as $ct_userid => $uc_uid)
				{
					$user = $this->db->get("SELECT userid,username,email,password,regtime FROM #table_member WHERE userid='$ct_userid'");
					if(!$this->charset_match)
					{
						$user = str_charset($this->charset_cmstop, $this->charset_phpwind,$user);
					}
					extract($user);
					$set = "SET username = '$username', password = '$password', email = '$email', regdate = '$regtime'";
					$this->pwdb->exec("UPDATE #table_members $set WHERE uid = $uc_uid");
					$this->repair_userid($uc_uid,$ct_userid);
			}
			$this->db->exec('OPTIMIZE TABLE `#table_member`'); //更新表
			$cache->rm('user_cache');
			$cache->rm('is_max');
			$cache->rm('merge_cache');
			import('helper.folder');
			folder::clear(CACHE_PATH.'table'.DS); //清理缓存
			folder::clear(CACHE_PATH.'setting'.DS);
		}
		return $success;
	}
	
	private function repair_userid($newid,$oldid)
	{
		$this->db->exec('SET FOREIGN_KEY_CHECKS=0');
		$updates_fields = @include(loader::_file('member','config'));
		foreach($updates_fields as $v)
		{
			list($table,$field)= explode("|",$v);
			$sql_ct = "UPDATE #table_{$table} SET {$field}={$newid} WHERE {$field}={$oldid}";
			$this->db->exec($sql_ct);
		}
		return $this;
	}

}