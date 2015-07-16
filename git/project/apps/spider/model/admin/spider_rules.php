<?php
/**
 * cmstop_spider_rules
 *   ruleid
 *   siteid
 *   name
 *   charset
 *   enter_rule
 *   list_rule
 *   content_rule
 *   created
 *   createdby
 *   updated
 *   updatedby
 */
class model_admin_spider_rules extends model
{
	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'spider_rules';
		$this->_primary = 'ruleid';
		$this->_fields = array('ruleid','guid','siteid','name','author','version','charset','enter_rule','list_rule','content_rule','description','created','createdby','updated','updatedby');
		
		$this->_readonly = array('ruleid','created','createdby');
		$this->_create_autofill = array('created'=>TIME,'createdby'=>$this->_userid);
		$this->_update_autofill = array('updated'=>TIME,'updatedby'=>$this->_userid);
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array(
			'siteid'=>array(
				'not_empty'=>array('请选择所属网站')
			),
			'name'=>array(
				'not_empty'=>array('规则名称不能为空')
			)
		);
	}
}