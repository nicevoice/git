<?php

class model_vote extends model implements SplSubject
{
	public $content, $category, $modelid, $data;
	private $observers = array();
	
	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'vote';
		$this->_primary = 'contentid';
		$this->_fields = array('contentid', 'type', 'description', 'starttime', 'endtime', 'maxoptions', 'maxvotes', 'mininterval', 'total');
		$this->_readonly = array('contentid');
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();

		$this->content = loader::model('admin/content', 'system');
		$this->category = loader::model('category', 'system');
		
		$this->modelid = modelid('vote');
	}
	
	function get($contentid)
	{
		$this->contentid = intval($contentid);
		
		$this->event = 'before_get';
		$this->notify();
		
		$this->data = $this->db->get("SELECT * FROM `#table_content`, `#table_vote` WHERE `#table_content`.`contentid`=`#table_vote`.`contentid` AND `#table_content`.`contentid`=$this->contentid");
		
		$this->content->output($this->data);
		$this->data['description'] = htmlspecialchars($this->data['description']);
		if (!$this->data)
		{
			$this->error = '投票不存在';
			return false;
		}
		
		$this->event = 'after_get';
		$this->notify();
		
		return $this->data;
	}
	
	function vote($contentid, $optionid)
	{
		if(!$this->check($contentid, $optionid)) return false;
		
		$this->contentid = intval($contentid);
		$this->optionid = $optionid;
		$this->votes = 0;
		
		$this->event = 'before_vote';
		$this->notify();
		
		if (!$this->votes)
		{
			$this->error = '没有选择投票选项';
			return false;
		}
		$result = $this->set_inc('total', $this->contentid, $this->votes);
		if ($result)
		{
			$this->event = 'after_vote';
			$this->notify();
		}
		return $result;
	}
	
	function check($contentid, $optionid)
	{
		$vote = $this->get($contentid);
		if(!$vote || $vote['status'] != 6)
		{
			$this->error = '投票不存在';
			return false;
		}
		elseif(($vote['type'] == 'radio' && !is_numeric($optionid)) || ($vote['type'] == 'checkbox' && !is_array($optionid)))
		{
			$this->error = '参数错误';
			return false;
		}
		elseif($vote['type'] == 'checkbox' && $vote['maxoptions'] && count($optionid) > $vote['maxoptions'])
		{
			$this->error = '所选择的投票选项不得超过 '.$vote['maxoptions'].' 个';
			return false;
		}
		elseif($vote['starttime'] && TIME < $vote['starttime'])
		{
			$this->error = '投票未开始';
			return false;
		}
		elseif ($vote['endtime'] && TIME > $vote['endtime'])
		{
			$this->error = '投票已结束';
			return false;
		}
		elseif($vote['mininterval'])
		{
			$log = loader::model('vote_log');
			if($log->get("contentid=$contentid AND ip='".IP."' AND created>=".(TIME - $vote['mininterval']*3600)))
			{
				$this->error = '同IP'.$vote['mininterval'].'小时内不得重复投票';
				return false;
			}
		}
		return true;
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