<?php
class model_data extends model
{
	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'mood_data';
		$this->_primary = 'contentid';
		$this->_fields = array('contentid', 'moodid', 'total');
		
		$this->_readonly = array('contentid');
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();
	}

	function add($data)
	{
		$contentid = $data['contentid'];
		$voteid = $data['voteid'];
		
		if ($contentid < 0 || $voteid < 0)
			return false;
		
		$field = 'm'.$voteid;
		$r = $this->get($contentid);
		if (!$r['contentid'])
		{
			$this->insert(array('contentid'=>$contentid,'updatetime' => TIME));
		}
		$this->db->query("UPDATE $this->_table SET total=total+1,$field=$field+1,updatetime=".TIME." WHERE contentid=$contentid");
		$r['total'] = $r['total'] + 1;
		$r[$field] = $r[$field] + 1;
		return $r;
	}
}
