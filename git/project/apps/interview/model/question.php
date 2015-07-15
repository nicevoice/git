<?php
class model_question extends model
{
	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'interview_question';
		$this->_primary = 'questionid';
		$this->_fields = array('questionid', 'contentid', 'nickname', 'content', 'created', 'createdby', 'ip', 'iplocked', 'state');
		$this->_readonly = array('questionid');
		$this->_create_autofill = array('created'=>TIME, 'createdby'=>$this->_userid, 'ip'=>IP);
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array('content'=>array('not_empty'=>array('内容不能为空'))
		                          );
	}
	
	function add($data)
	{
		$data = $this->filter_array($data, array('contentid', 'nickname', 'content', 'state'));
		return $this->insert($data);
	}
	
	function ls($contentid)
	{
		$contentid = intval($contentid);
		return $this->select("`contentid`=$contentid AND `state`>1", '*', '`questionid` ASC');
	}
	
	function _after_select(& $data, $multiple)
	{
		if (!$data) return ;
		import('helper.iplocation');
		$iplocation = new iplocation();
		if ($multiple)
		{
			foreach ($data as $k=>$r)
			{
				$r['created'] = date('Y-m-d H:i', $r['created']);
				if ($r['createdby']) $r['username'] = username($r['createdby']);
				$r['iparea'] = $iplocation->get($r['ip']);
                $r['content'] = remove_xss($r['content']);
				$data[$k] = $r;
			}
		}
		else 
		{
			$data['created'] = date('Y-m-d H:i', $data['created']);
			if ($data['createdby']) $data['username'] = username($data['createdby']);
			$data['iparea'] = $iplocation->get($data['ip']);
            $data['content'] = remove_xss($data['content']);
		}
	}
}