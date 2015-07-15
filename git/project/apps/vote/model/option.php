<?php

class model_option extends model 
{
	function __construct()
	{
        parent::__construct();
		$this->_table = $this->db->options['prefix'].'vote_option';
		$this->_primary = 'optionid';
		$this->_fields = array('optionid', 'contentid', 'name', 'sort', 'votes');
		$this->_readonly = array('optionid', 'contentid');
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();
	}

	function ls($contentid)
	{
		return $this->gets_by('contentid', $contentid, '*', '`sort` ASC, `optionid` ASC');
	}
}