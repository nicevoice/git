<?php
/**
 * cmstop_push_task
 *   taskid
 *   ruleid
 *   catid
 *   title
 *   extra_condition
 *   created
 *   createdby
 *   updated
 *   updatedby
 */
class model_admin_push_task extends model
{
	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'push_task';
		$this->_primary = 'taskid';
		$this->_fields = array('taskid','ruleid','catid','title','extra_condition','created','createdby','updated','updatedby');
		
		$this->_readonly = array('taskid','created','createdby');
		$this->_create_autofill = array('created'=>TIME,'createdby'=>$this->_userid);
		$this->_update_autofill = array('updated'=>TIME,'updatedby'=>$this->_userid);
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array(
			'title'=>array(
				'not_empty'=>array('任务名称不能为空')
			)
		);
	}
}