<?php

class model_dms_model extends model
{
    public $data = array();

    public $event, $where, $fields, $order, $page, $pagesize, $total;

	private $observers = array();

	function __construct()
	{
		parent::__construct();

		$this->_table = $this->db->options['prefix'].'dms_model';
		$this->_primary = 'modelid';
		$this->_fields = array('modelid', 'name', 'alias', 'mainindex', 'deltaindex');
		$this->_readonly = array('modelid');
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();
	}

	function get_modules()
	{
		$result = array();
		$data = $this->select();
		foreach($data as $row)
		{
			$result[$row['modelid']] = $row;
			$result[$row['alias']] = $row;
		}
		return $result;
	}

}