<?php

class model_admin_log_data extends model
{
	private $iplocation;
	
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

		import('helper.iplocation');
		$this->iplocation = new iplocation();
	}
	
	function option($logid)
	{
		$data = $this->gets_by('logid', $logid);
		array_map(array($this, 'output'), & $data);
		return $data;
	}
	
	function ls($optionid, $page = 1, $pagesize = 20)
	{
		$optionid = intval($optionid);
		$page = intval($page);
		$pagesize = intval($pagesize);
		$data = $this->db->page("SELECT * FROM #table_vote_log_data d LEFT JOIN #table_vote_log l ON d.`logid`=l.`logid` WHERE d.`optionid`=$optionid ORDER BY `dataid` DESC", $page, $pagesize);
		array_map(array($this, 'output'), & $data);
		return $data;
	}
	
	function total($optionid)
	{
		$optionid = intval($optionid);
		return $this->count("`optionid`=$optionid");
	}
	
	private function output(& $r)
	{
		if (!$r) return;

		$r['createdbyname'] = $r['createdby'] ? username($r['createdby']) : '';
		$r['created'] = date('Y-m-d H:i', $r['created']);
		$r['area'] = $this->iplocation->get($r['ip']);
	}
}