<?php
class model_admin_section_history extends model
{
	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'section_history';
		$this->_primary = 'logid';
		$this->_fields = array('logid', 'sectionid', 'data', 'created', 'createdby', 'ip', 'state');
		
		$this->_readonly = array('logid');
		$this->_create_autofill = array('createdby'=>$this->_userid, 'created'=>TIME, 'ip'=>IP, 'state'=>0);
		$this->_update_autofill = array('locked'=>0, 'lockedby'=>0, 'updated'=>time(), 'updatedby'=>$this->_userid);
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();
		
	}
	
	function get_section($sectionid, $extraWhere = null)
	{
		$where = "sectionid = $sectionid";
		if ($extraWhere) $where .= " AND $extraWhere";
		$data = $this->select($where, '*', 'created DESC', 20);
		return $data;
	}

	function add($data)
	{
		$data = $this->filter_array($data, array('sectionid', 'data'));
		return $this->insert($data);
	}

	function edit($sectionid, $data)
	{
        $data = $this->filter_array($data, array('logid', 'sectionid', 'data', 'created', 'createdby', 'ip', 'state'));
		return $this->update($data, "sectionid=$sectionid");
	}
	
	function ls()
	{
		return $this->select();
	}
	
	
}
