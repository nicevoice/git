<?php
/*
*	UC API FOR cmstop
*	@xu
*	14:31 2009-9-29
*/
define('CMSTOP_START_TIME', microtime(true));

define('UC_CLIENT_VERSION', '1.5.0');
define('UC_CLIENT_RELEASE', '20090121');

define('API_DELETEUSER', 1);			//note 用户删除 API 接口开关
define('API_RENAMEUSER', 1);			//note 用户改名 API 接口开关
define('API_GETTAG', 1);				//note 获取标签 API 接口开关
define('API_SYNLOGIN', 1);				//note 同步登录 API 接口开关
define('API_SYNLOGOUT', 1);				//note 同步登出 API 接口开关
define('API_UPDATEPW', 1);				//note 更改用户密码 开关
define('API_UPDATEBADWORDS', 1);		//note 更新关键字列表 开关
define('API_UPDATEHOSTS', 1);			//note 更新域名解析缓存 开关
define('API_UPDATEAPPS', 1);			//note 更新应用列表 开关
define('API_UPDATECLIENT', 1);			//note 更新客户端缓存 开关
define('API_UPDATECREDIT', 1);			//note 更新用户积分 开关
define('API_GETCREDITSETTINGS', 1);		//note 向 UCenter 提供积分设置 开关
define('API_GETCREDIT', 1);				//note 获取用户的某项积分 开关
define('API_UPDATECREDITSETTINGS', 1);	//note 更新应用积分设置 开关

define('API_RETURN_SUCCEED', '1');		//note 代码段运行后 返回 API_RETURN_SUCCEED
define('API_RETURN_FAILED', '-1');		//note 接口运行失败则返回 API_RETURN_FAILED
define('API_RETURN_FORBIDDEN', '-2');	//note 返回 API_RETURN_FORBIDDEN

//define('API_ROOT', substr(dirname(__FILE__), 0, -3));
define('API_ROOT', dirname(__FILE__).'/');

/* CMSTOP Setting */
define('RUN_CMSTOP', true);
require '../../../cmstop.php';
//define config
$set= setting('member');
$set['uc_dbtablepre'] = '`'.$set['uc_dbname'].'`.'.$set['uc_dbtablepre'];
$set = array_change_key_case($set, CASE_UPPER);
foreach($set as $k => $v) {
	if(preg_match('/^UC_/',$k)) define($k,$v);
}
define('UC_PPP',20);
$charset_cmstop = strtoupper(config('config','charset'));
$charset_ucenter = ($set['UC_DBCHARSET'] == 'utf8')?'UTF-8':strtoupper($set['UC_DBCHARSET']);

if(!defined('IN_UC')) {

	error_reporting(0);
	if (get_magic_quotes_runtime())
    {
        set_magic_quotes_runtime(false);
    }

	defined('MAGIC_QUOTES_GPC') || define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());

	$_DCACHE = $get = $post = array();

	$code = @$_GET['code'];
	parse_str(_authcode($code, 'DECODE', UC_KEY), $get);
	if(MAGIC_QUOTES_GPC) {
		$get = _stripslashes($get);
	}
	
	$timestamp = time();
	if(empty($get)) {
		exit('Invalid Request');
	} elseif($timestamp - $get['time'] > 3600) {
		exit('Authracation has expiried');
	}
	$action = $get['action'];

	require_once API_ROOT.'./uc_client/lib/xml.class.php';
	$post = xml_unserialize(file_get_contents('php://input'));

	if(in_array($get['action'], array('test', 'deleteuser', 'renameuser', 'gettag', 'synlogin', 'synlogout', 'updatepw', 'updatebadwords', 'updatehosts', 'updateapps', 'updateclient', 'updatecredit', 'getcredit', 'getcreditsettings', 'updatecreditsettings'))) {
		$GLOBALS['db'] = & factory::db();
		$GLOBALS['tablepre'] = $GLOBALS['db']->prefix();
		$uc_note = new uc_note();
		if($charset_cmstop != $charset_ucenter)
		{
			$get = str_charset($charset_ucenter, $charset_cmstop, $get);
			$post = str_charset($charset_ucenter, $charset_cmstop, $post);
		}
		exit($uc_note->$get['action']($get, $post));
	} else {
		exit(API_RETURN_FAILED);
	}

} else {
	//Ucenter通过include的方式调用API
	define('API_ROOT', $app['extra']['apppath']);
	$GLOBALS['db'] = & factory::db();
	$GLOBALS['tablepre'] = $GLOBALS['db']->prefix();
}

class uc_note {

	var $db = '';
	var $tablepre = '';
	var $appdir = '';

	function _serialize($arr, $htmlon = 0) {
		if(!function_exists('xml_serialize')) {
			include_once API_ROOT.'./uc_client/lib/xml.class.php';
		}
		return xml_serialize($arr, $htmlon);
	}
	
	function __construct()
	{
		$this->uc_note();
	}
	
	function uc_note() {
		$this->appdir = API_ROOT;
		$this->db = $GLOBALS['db'];
		$this->tablepre = $GLOBALS['tablepre'];
	}
	
	function test($get, $post) {
		return API_RETURN_SUCCEED;
	}

	function deleteuser($get, $post) {
		$uids = $get['ids'];
		!API_DELETEUSER && exit(API_RETURN_FORBIDDEN);
		
		$this->db->query("DELETE FROM #table_member WHERE userid IN ($uids)");

		return API_RETURN_SUCCEED;
	}

	function renameuser($get, $post) {
		$uid = $get['uid'];
		$usernameold = $get['oldusername'];
		$usernamenew = $get['newusername'];
		if(!API_RENAMEUSER) {
			return API_RETURN_FORBIDDEN;
		}

		$this->db->query("UPDATE #table_member SET `username`='$usernamenew' WHERE userid='$uid'");
		return API_RETURN_SUCCEED;
	}

	function gettag($get, $post) {
		$name = $get['id'];
		if(!API_GETTAG) {
			return API_RETURN_FORBIDDEN;
		}

		$name = trim($name);
		if(empty($name) || !preg_match('/^([\x7f-\xff_-]|\w|\s)+$/', $name) || strlen($name) > 20) {
			return API_RETURN_FAILED;
		}
		
		$return = array($name, $threadlist);
		return $this->_serialize($return, 1);
	}

	function synlogin($get, $post) {
		$uid = $get['uid'];
		$username = $get['username'];
		
		if(!API_SYNLOGIN) {
			return API_RETURN_FORBIDDEN;
		}
		
		ob_clean();
		header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
		$uid = intval($uid);
		$m = $this->db->get("SELECT userid, username, password, groupid FROM #table_member WHERE userid='$uid'");
		$cookie = & factory::cookie();
		if($m) {
			$this->db->query("UPDATE #table_member SET `lastloginip`='".IP."',`lastlogintime`='".TIME."',logintimes=logintimes+1 WHERE `userid`='".$m['userid']."'");
			$cookie->set('auth', str_encode($m['userid']."\t".$m['username']."\t".$m['groupid'], config('config', 'authkey')));
			$cookie->set('userid', $m['userid']);
			$cookie->set('username', $m['username']);
			$cookie->set('rememberusername', $m['username']);
		} else {
			$cookie->set('rememberusername', $username, TIME + 2592000);
		}
		return API_RETURN_SUCCEED;
	}

	function synlogout($get, $post) {
		
		if(!API_SYNLOGOUT) {
			return API_RETURN_FORBIDDEN;
		}
		
		header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
		
		$cookie = & factory::cookie();
		$cookie->set('auth');
		$cookie->set('userid');
		$cookie->set('username');
		return API_RETURN_SUCCEED;
	}

	function updatepw($get, $post) {
		if(!API_UPDATEPW) {
			return API_RETURN_FORBIDDEN;
		}
		$username = $get['username'];
		$password = $get['password'];

		$newpw = md5($password);
		$this->db->query("UPDATE #table_member SET password='$newpw' WHERE username='$username'");
		return API_RETURN_SUCCEED;
	}

	function updatebadwords($get, $post) {
		if(!API_UPDATEBADWORDS) {
			return API_RETURN_FORBIDDEN;
		}
		$cachefile = $this->appdir.'./uc_client/data/cache/badwords.php';
		$fp = fopen($cachefile, 'w');
		$data = array();
		if(is_array($post)) {
			foreach($post as $k => $v) {
				$data['findpattern'][$k] = $v['findpattern'];
				$data['replace'][$k] = $v['replacement'];
			}
		}
		$s = "<?php\r\n";
		$s .= '$_CACHE[\'badwords\'] = '.var_export($data, TRUE).";\r\n";
		fwrite($fp, $s);
		fclose($fp);
		return API_RETURN_SUCCEED;
	}

	function updatehosts($get, $post) {
		if(!API_UPDATEHOSTS) {
			return API_RETURN_FORBIDDEN;
		}
		$cachefile = $this->appdir.'./uc_client/data/cache/hosts.php';
		$fp = fopen($cachefile, 'w');
		$s = "<?php\r\n";
		$s .= '$_CACHE[\'hosts\'] = '.var_export($post, TRUE).";\r\n";
		fwrite($fp, $s);
		fclose($fp);
		return API_RETURN_SUCCEED;
	}

	function updateapps($get, $post) {
		
		if(!API_UPDATEAPPS) {
			return API_RETURN_FORBIDDEN;
		}
		
		$cachefile = $this->appdir.'./uc_client/data/cache/hosts.php';
		$fp = fopen($cachefile, 'w');
		$s = "<?php\r\n";
		$s .= '$_CACHE[\'hosts\'] = '.var_export($post, TRUE).";\r\n";
		fwrite($fp, $s);
		fclose($fp);
		
		return API_RETURN_SUCCEED;
	}

	function updateclient($get, $post) {
		if(!API_UPDATECLIENT) {
			return API_RETURN_FORBIDDEN;
		}
		$cachefile = $this->appdir.'./uc_client/data/cache/settings.php';
		$fp = fopen($cachefile, 'w');
		$s = "<?php\r\n";
		$s .= '$_CACHE[\'settings\'] = '.var_export($post, TRUE).";\r\n";
		fwrite($fp, $s);
		fclose($fp);
		return API_RETURN_SUCCEED;
	}

	function updatecredit($get, $post) {
		if(!API_UPDATECREDIT) {
			return API_RETURN_FORBIDDEN;
		}
		
		$credit = intval($get['credit']);
		$amount = intval($get['amount']);
		$uid = intval($get['uid']);
		
		//更新积分
		$this->db->query("UPDATE #table_member SET credits=credits+'$amount' WHERE userid='$uid'");

		return API_RETURN_SUCCEED;
	}

	function getcredit($get, $post) {
		if(!API_GETCREDIT) {
			return API_RETURN_FORBIDDEN;
		}
		
		$uid = intval($get['uid']);
		$credit = intval($get['credit']);
		
		$q = $this->db->get("SELECT credits FROM #table_member WHERE userid='$uid'");
		return $credit >= 1 && $credit <= 8 ? $q['credits'] : 0;
	}

	function getcreditsettings($get, $post) {
		if(!API_GETCREDITSETTINGS) {
			return API_RETURN_FORBIDDEN;
		}
		$credits = array();
		$credits[1] = array('title', 'unit');
	
		return $this->_serialize($credits);
	}

	function updatecreditsettings($get, $post) {
		if(!API_UPDATECREDITSETTINGS) {
			return API_RETURN_FORBIDDEN;
		}
		
		$outextcredits = array();
	
		foreach($get['credit'] as $appid => $credititems) {
			if($appid == UC_APPID) {
				foreach($credititems as $value) {
					$outextcredits[$value['appiddesc'].'|'.$value['creditdesc']] = array(
						'creditsrc' => $value['creditsrc'],
						'title' => $value['title'],
						'unit' => $value['unit'],
						'ratio' => $value['ratio']
					);
				}
			}
		}
	
		$cachefile = $this->appdir.'./uc_client/data/cache/creditsettings.php';
		$fp = fopen($cachefile, 'w');
		$s = "<?php\r\n";
		$s .= '$_CACHE[\'creditsettings\'] = '.var_export($outextcredits, TRUE).";\r\n";
		fwrite($fp, $s);
		fclose($fp);
		
		return API_RETURN_SUCCEED;

	}
}


function _authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
	$ckey_length = 4;

	$key = md5($key ? $key : UC_KEY);
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);

	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);

	$result = '';
	$box = range(0, 255);

	$rndkey = array();
	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}

	if($operation == 'DECODE') {
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
			return substr($result, 26);
		} else {
				return '';
			}
	} else {
		return $keyc.str_replace('=', '', base64_encode($result));
	}

}

function _stripslashes($string) {
	if(is_array($string)) {
		foreach($string as $key => $val) {
			$string[$key] = _stripslashes($val);
		}
	} else {
		$string = stripslashes($string);
	}
	return $string;
}