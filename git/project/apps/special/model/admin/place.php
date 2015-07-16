<?php
class model_admin_place extends model
{
    function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'place';
		$this->_primary = 'placeid';
		$this->_fields = array('placeid', 'pageid', 'name', 'description');
		$this->_readonly = array();
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
	}
}