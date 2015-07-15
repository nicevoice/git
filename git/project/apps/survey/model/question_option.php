<?php

class model_question_option extends model 
{	
	function __construct()
	{
        parent::__construct();
		$this->_table = $this->db->options['prefix'].'survey_question_option';
		$this->_primary = 'optionid';
		$this->_fields = array('optionid', 'questionid', 'name', 'image', 'sort', 'votes');
		$this->_readonly = array('optionid', 'questionid');
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();
	}
}