<?php
class model_guestbook_type extends model
{
	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'guestbook_type';
		$this->_primary = 'typeid';
		$this->_fields = array('typeid','name','count','sort');
		
		$this->_readonly = array('typeid');
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();
	}

}