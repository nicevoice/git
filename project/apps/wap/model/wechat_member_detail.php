<?php

class model_wechat_member_detail extends model
{
	public $content, $setting;
	
	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'wechat_member_detail';
		$this->_primary = 'openid';
		$this->_fields = array('openid', 'name', 'mobile', 'created');
		$this->_readonly = array('openid');
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();
	}
}