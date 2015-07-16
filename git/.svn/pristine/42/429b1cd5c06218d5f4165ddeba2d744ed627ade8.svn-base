<?php
class model_member_detail extends model 
{
	function __construct()
	{
		parent::__construct();
		
		$this->_table = $this->db->options['prefix'].'member_detail';
		$this->_primary = 'userid';
		$this->_fields = array('userid', 'name', 'sex', 'birthday', 'telephone', 'mobile', 'job', 'address', 'zipcode', 'qq', 'msn', 'authstr','remarks');
		
		$this->_readonly = array('userid');
		$this->_create_autofill = array('telephone'=>'','mobile'=>'','job'=>'','address'=>'','zipcode'=>'','qq'=>'','msn'=>'','authstr'=>'','remarks'=>'');
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array(
			'userid'=>array('not_empty'=>array('用户ID不能为空'),'integer' => array('用户ID必须为整数')),
			'telephone'=>array('telephone'=>array('电话号码格式不正确')),
			'mobile' =>array('mobile'=>array('手机格式不正确')),
			'qq'=>array('qq'=>array('QQ格式不正确')),
			'msn'=>array('email'=>array('MSN格式不正确'))
		);
	}
	
	function add($data)
	{
		return $this->insert($data);
	}
}