<?php

class model_admin_vote_log extends model implements SplSubject
{
	public $data;
	
	private $observers = array();
	
	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'vote_log';
		$this->_primary = 'logid';
		$this->_fields = array('logid', 'contentid', 'created', 'createdby', 'ip');
		$this->_readonly = array('logid', 'contentid');
		$this->_create_autofill = array('created'=>TIME, 'createdby'=>$this->_userid, 'ip'=>IP);
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array('contentid'=>array('not_empty' =>array('内容ID不能为空'),
                                                      'is_numeric' =>array('内容ID必须是数字'),
                                                      'max_length' =>array(8, '内容ID不得超过8个字节'),
                                                     )
                                  );
	}
	
	function ls($contentid, $page = 1, $pagesize = 20)
	{
		$contentid = intval($contentid);
		$page = intval($page);
		$pagesize = intval($pagesize);
		
		$this->event = 'before_ls';
		$this->notify();
		
		$this->data = $this->page("`contentid`=$contentid", '*', '`logid` DESC', $page, $pagesize);
		
		$this->event = 'after_ls';
		$this->notify();
		
		return $this->data;
	}
	
	function total($contentid)
	{
		$contentid = intval($contentid);
		return $this->count("`contentid`=$contentid");
	}
	
	function _after_select(& $data, $multiple)
	{
		import('helper.iplocation');
		$this->iplocation = new iplocation();
		
		if ($multiple)
		{
			array_map(array($this, 'output'), & $data);
		}
		else 
		{
			$this->output($data);
		}
	}

	private function output(& $r)
	{
		if (!$r) return;

		$r['createdbyname'] = $r['createdby'] ? username($r['createdby']) : '';
		$r['created'] = date('Y-m-d H:i', $r['created']);
		$r['area'] = $this->iplocation->get($r['ip']);
	}
	
	public function attach(SplObserver $observer)
	{
		$this->observers[] = $observer;
	}

	public function detach(SplObserver $observer)
	{
		if($index = array_search($observer, $this->observers, true)) unset($this->observers[$index]);
	}

	public function notify()
	{
		foreach ($this->observers as $observer)
		{
			$observer->update($this);
		}
	}
}