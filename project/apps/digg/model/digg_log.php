<?php
class model_digg_log extends model
{
	public $setting;
	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'digg_log';
		$this->_primary = 'contentid';
		$this->_fields = array('contentid','flag','userid','username','ip','datetime');
		
		$this->_readonly = array('contentid');
		$this->_create_autofill = array('userid'=>$this->_userid,'username'=>$this->_username,'ip'=>IP,'datetime'=>TIME);
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();
	}

	function is_done($contentid)
	{
		$where = null;
		if($contentid) $where[] = "contentid = $contentid";
		$where[] = "datetime>".(TIME-$this->setting['refresh']);
		if($this->_userid)
		{
			$where[] = "userid = $this->_userid";
		}
		else
		{
			$where[] = "ip = '".IP."'";
		}
		
		if($where) $where = implode(' AND ', $where);
		return $this->get($where, 'flag');
	}

	function add($data)
	{
		return $this->insert($data);
	}

}