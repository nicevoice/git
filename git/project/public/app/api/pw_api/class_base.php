<?php

!defined('RUN_CMSTOP') && exit('Forbidden');

define('API_CLOSED', 1);
define('API_SIGN_ERROR', 2);
define('API_MODE_NOT_EXISTS', 3);
define('API_METHOD_NOT_EXISTS', 4);

class ApiResponse {

	var $result;
	var $mode;

	function ApiResponse($res, $mode = null) {
		$this->result = $res;
		$this->mode = $mode;
	}

	function getResult() {
		return $this->result;
	}

	function getMode() {
		return $this->mode;
	}
}

class ErrorMsg {

	var $errCode = 0;
	var $errMessage = '';

	function ErrorMsg($errCode, $errMessage) {
		$this->errCode = $errCode;
		$this->errMessage = $errMessage;
	}

	function getErrCode() {
		return $this->errCode;
	}

	function getErrMessage() {
		return $this->errMessage;
	}

	function getResult() {
		return null;
	}
}

class api_client {

	var $type;
	var $apikey;
	var $charset;
	var $db;
	var $classdb;
    var $siteappkey;

	function api_client() {
		$this->apikey	= '';
		$this->type		= '';
		$this->siteappkey ='';
		$this->db		=& $GLOBALS['db'];
		$this->classdb	= array();
		$this->charset	= $GLOBALS['charset'];
	}

	function run($request) {
		$request = $this->strips($request);
		if (isset($request['type']) && $request['type'] == 'uc') {
			$this->type		= 'uc';
			$this->apikey	= $GLOBALS['uc_key'];
		} else {
			$this->type		= 'app';
			$this->apikey	= $GLOBALS['db_siteownerid'];
			$this->siteappkey = $GLOBALS['db_siteappkey'];
		}
		
		ksort($request);
		reset($request);
		$arg = '';
		foreach ($request as $key => $value) {
			if ($value && $key != 'sig') {
				$arg .= "$key=$value&";
			}
		}
		
		if (empty($this->apikey) || md5($arg . $this->apikey) != $request['sig']) {
			return new ErrorMsg(4, 'Error Sign');
		}
		$mode	= $request['mode']; //测试时等于 Site 
		$method	= $request['method'];  //测试时等于 connect 
		$params = isset($request['params']) ? unserialize($request['params']) : array();
		
		if ($params && isset($request['charset'])) {
			//$params = pwConvert($params, $this->charset, $request['charset']);
			$params = str_charset($request['charset'],$this->charset,$params);
		}
		//print_r($params);exit;
		return $this->callback($mode, $method, $params);
	}

	function callback($mode, $method, $params) {
		
		if (!isset($this->classdb[$mode])) {
			if (!file_exists(API_ROOT.'pw_api/class_' . $mode . '.php')) {
				return new ErrorMsg(API_MODE_NOT_EXISTS, "Class($mode) Not Exists");
			}
			require_once (API_ROOT.'pw_api/class_' . $mode . '.php');
			$this->classdb[$mode] = new $mode($this);
		}
		
		if (!method_exists($this->classdb[$mode], $method)) {
			return new ErrorMsg(API_METHOD_NOT_EXISTS, "Method($method of $mode) Not Exists");
		}
		!is_array($params) && $params = array();
		return @call_user_func_array(array(&$this->classdb[$mode], $method), $params);
	}

	function dataFormat($data) {
		$res = array(
			'charset' => $this->charset
		);
		if (strtolower(get_class($data)) == 'apiresponse') {
			$res['result'] = $data->getResult();
		} else {
			$res['errCode'] = $data->getErrCode();
			$res['errMessage'] = $data->getErrMessage();
		}
		return serialize($res);
	}

	function strips($param) {
		if (is_array($param)) {
			foreach ($param as $key => $value) {
				$param[$key] = $this->strips($value);
			}
		} else {
			$param = stripslashes($param);
		}
		return $param;
	}

	function strcode($string, $encode = true) {
		!$encode && $string = base64_decode($string);
		$code = '';
		$key  = substr(md5($_SERVER['HTTP_USER_AGENT'] . $this->apikey),8,18);
		$keylen = strlen($key);
		$strlen = strlen($string);
		for ($i = 0; $i < $strlen; $i++) {
			$k		= $i % $keylen;
			$code  .= $string[$i] ^ $key[$k];
		}
		return ($encode ? base64_encode($code) : $code);
	}
}
