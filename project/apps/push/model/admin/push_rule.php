<?php
/**
 * cmstop_push_rule
 *   ruleid
 *   name
 *   dsnid
 *   maintable
 *   jointable
 *   primary
 *   linkrule
 *   fields
 *   defaults
 *   condition
 *   plugin
 *   description
 *   created
 *   createdby
 *   updated
 *   updatedby
 */
class model_admin_push_rule extends model
{
	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'push_rule';
		$this->_primary = 'ruleid';
		$this->_fields = array('ruleid','name','dsnid','maintable','jointable','primary','linkrule','fields','defaults','condition','plugin','description','created','createdby','updated','updatedby');
		
		$this->_readonly = array('ruleid','created','createdby');
		$this->_create_autofill = array('created'=>TIME,'createdby'=>$this->_userid);
		$this->_update_autofill = array('updated'=>TIME,'updatedby'=>$this->_userid);
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array(
			'name'=>array(
				'not_empty'=>array('规则名称不能为空')
			)
		);
	}
}