<?php
class model_chat extends model
{
	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'interview_chat';
		$this->_primary = 'chatid';
		$this->_fields = array('chatid', 'contentid', 'guestid', 'content', 'created', 'createdby', 'ip');
		$this->_readonly = array('chatid');
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();
	}
	
	function ls($contentid)
	{
		$contentid = intval($contentid);
		return $this->select("`contentid`=$contentid", '*', '`chatid` ASC');
	}

	function _after_select(& $data, $multiple)
	{
		if (!$data) return ;
		if ($multiple)
		{
			foreach ($data as $k=>$r)
			{
				$r['created'] = date('Y-m-d H:i', $r['created']);
				$data[$k] = $r;
			}
		}
		else 
		{
			$data['created'] = date('Y-m-d H:i', $data['created']);
		}
	}
}