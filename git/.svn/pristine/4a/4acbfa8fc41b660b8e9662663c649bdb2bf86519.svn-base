<?php

class model_contribution_log extends model
{
	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'contribution_log';
		$this->_primary = 'logid';
		$this->_fields = array('logid','contributionid', 'status', 'action','created','createdby','note');
		$this->_readonly = array('logid');
		$this->_create_autofill = array('created' => TIME,'createdby' => $this->_userid);
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();
	}
}