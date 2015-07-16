<?php

class model_wechat_member extends model
{
	public $content, $setting;
	
	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'wechat_member';
		$this->_primary = 'openid';
		$this->_fields = array('openid', 'unionid', 'appid', 'nickname', 'created', 'intro');
		$this->_readonly = array('openid');
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();
		$this->setting = setting('wap');
	}
}