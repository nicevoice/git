<?php
class model_admin_place_data extends model
{
    function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'place_data';
		$this->_primary = 'dataid';
		$this->_fields = array('dataid', 'placeid', 'contentid', 'title', 'color', 'url', 'thumb', 'description', 'time', 'sort', 'created', 'createdby', 'status');
		$this->_readonly = array();
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
	}
}