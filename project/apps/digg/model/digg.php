<?php
class model_digg extends model
{
	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'digg';
		$this->_primary = 'contentid';
		$this->_fields = array('contentid','supports','againsts');
		
		$this->_readonly = array('contentid');
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();
	}

	function add($data)
	{
		return $this->insert($data);
	}
}