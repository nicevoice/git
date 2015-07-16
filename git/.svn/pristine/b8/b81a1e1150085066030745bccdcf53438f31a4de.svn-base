<?php

!defined('RUN_CMSTOP') && exit('Forbidden');
//api mode 1

define('API_USER_USERNAME_NOT_UNIQUE', 100);

class User {

	var $base;
	var $db;

	function User($base) {
		$this->base = $base;
		$this->db = $base->db;
	}

	function getInfo($uids, $fields = array()) {
		if (!$uids) {
			return new ApiResponse(false);
		}
		
		$users = array();
		return new ApiResponse($users);
	}

	function alterName($uid, $newname) {
		$this->db->query("UPDATE #table_member SET username=$newname WHERE userid=$uid");
		return new ApiResponse(1);
	}

	function deluser($uids) {
		$this->db->query("DELETE FROM #table_member WHERE userid IN (".implode(',',$uids).")");
		return new ApiResponse(1);
	}

	function synlogin($user) {
		//对应的是cmstop的 用户ID，用户名，密码
		list($winduid, $windid, $windpwd) = explode("\t", $this->base->strcode($user, false));
		header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
		$m = $this->db->get("SELECT userid, username, password, groupid FROM #table_member WHERE userid='$winduid'");
		$cookie = & factory::cookie();
		if($m) {
			$this->db->query("UPDATE #table_member SET `lastloginip`='".IP."',`lastlogintime`='".TIME."',logintimes=logintimes+1 WHERE `userid`='".$m['userid']."'");
			$cookie->set('auth', str_encode($m['userid']."\t".$m['username']."\t".$m['groupid'], config('config', 'authkey')));
			$cookie->set('userid', $m['userid']);
			$cookie->set('username', $m['username']);
			$cookie->set('rememberusername', $m['username']);
		} else {
			$cookie->set('rememberusername', $m['username'], TIME + 2592000);
		}
		return 1;
	}

	function synlogout() {
		header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
		$cookie = & factory::cookie();
		$cookie->set('auth');
		$cookie->set('userid');
		$cookie->set('username');
		return '';
	}
}