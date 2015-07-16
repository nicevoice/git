<?php

!defined('RUN_CMSTOP') && exit('Forbidden');
//api mode 6
//cmstopÄ¾ÓÐ»ý·Ö°¡
class Credit {
	
	var $base;
	var $db;

	function Credit($base) {
		$this->base = $base;
		$this->db = $base->db;
	}

	function get() {
		$arr = array();
		return new ApiResponse($arr);
	}

	function syncredit($arr) {
		if (is_array($arr)) {
			
		}
		return new ApiResponse(1);
	}

	function getvalue($uid) {
		$retv = array();
		return new ApiResponse($retv);
	}
}
