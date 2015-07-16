<?php

class model_log_data extends model 
{	
	function __construct()
	{
        parent::__construct();
		$this->_table = $this->db->options['prefix'].'vote_log_data';
		$this->_primary = 'dataid';
		$this->_fields = array('dataid', 'logid', 'optionid');
		$this->_readonly = array('dataid');
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();
	}
}