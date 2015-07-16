<?php

class model_survey extends model implements SplSubject
{
	public $content, $category, $cookie, $modelid;
	private $observers = array();

	function __construct()
	{
        parent::__construct();
		$this->_table = $this->db->options['prefix'].'survey';
		$this->_primary = 'contentid';
		$this->_fields = array('contentid', 'description', 'starttime', 'endtime', 'maxanswers', 'minhours', 'checklogined','mailto', 'questions', 'answers');
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();
        $this->content = loader::model('content', 'system');
        $this->modelid = modelid('survey');

		$this->cookie = factory::cookie();
	}
	
	function get($contentid)
	{
		$this->contentid = intval($contentid);
		
		$this->event = 'before_get';
		$this->notify();
		
		$this->data = $this->db->get("SELECT * FROM `#table_content` c, `#table_survey` s WHERE c.`contentid`=s.`contentid` AND c.`contentid`=$this->contentid");
		if (!$this->data)
		{
			$this->error = '调查不存在';
			return false;
		}
		
		$this->event = 'after_get';
		$this->notify();
		
		return $this->data;
	}
	
	function answer($contentid, $data)
	{
		$contentid = intval($contentid);
		$data = htmlspecialchars_deep($data);
		if(!$this->check($contentid, $data)) return false;

		$this->contentid = $contentid;
		$this->data = $data;
		
		$this->event = 'before_answer';
		$this->notify();

		if ($this->answerid) 
		{
			$result = $this->set_inc('answers', $this->contentid);
			if ($result)
			{
				// 提交后 存cookie 一年
				$this->cookie->set('issubmit', $this->_userid.$this->contentid, 31535999);
				$this->event = 'after_answer';
				$this->notify();
			}
			return $this->answerid;
		}
		return false;
	}
	
	private function check($contentid, $question)
	{
		$survey = $this->get($contentid);
		if(!$survey || $survey['status'] != 6)
		{
			$this->error = '调查不存在';
			return false;
		}
		elseif($survey['starttime'] && TIME < $survey['starttime'])
		{
			$this->error = '调查未开始';
			return false;
		}
		elseif ($survey['endtime'] && TIME > $survey['endtime'])
		{
			$this->error = '调查已结束';
			return false;
		}
		elseif($survey['minhours'])
		{
			$answer = loader::model('answer','survey');
			if($answer->get("contentid=$contentid AND ip='".IP."' AND created>=".(TIME - $survey['minhours']*3600)))
			{
				$this->error = '同IP'.$survey['minhours'].'小时内不得重复提交';
				return false;
			}
		}
		else
		{
			if($this->cookie->get('issubmit') == $this->_userid.$contentid) 
			{
				$this->error = '您已参与过本次调查';
				return false;
			}
		}

		$questions = loader::model('question','survey')->ls($contentid);
		foreach ($questions as $questionid=>$q)
		{
			if($q['type'] == 'radio')
			{
				if ($q['required'] && ((!$q['allowfill'] && !is_numeric($question[$questionid]['optionid'])) || ($q['allowfill'] && !is_numeric($question[$questionid]['optionid']) && empty($question[$questionid]['content']))))
				{
					$this->error = $q['subject'].'，必选';
					return false;
				}
			}
			elseif ($q['type'] == 'checkbox')
			{
				if (isset($question[$questionid]['optionid']) && $q['maxoptions'] && count($question[$questionid]['optionid']) > $q['maxoptions'])
				{
					$this->error = $q['subject'].'，最多只能选'.$q['maxoptions'].'项';
					return false;
				}
				elseif ($q['required'] && !is_array($question[$questionid]['optionid']) && empty($question[$questionid]['content']))
				{
					$this->error = $q['subject'].'，必选';
					return false;
				}
			}
			elseif ($q['type'] == 'select')
			{
				if ($q['required'] && (!is_numeric($question[$questionid]['optionid']) && ($q['allowfill'] && empty($question[$questionid]['content']))))
				{
					$this->error = $q['subject'].'，必选';
					return false;
				}
			}
			elseif ($q['type'] == 'text')
			{
				if ($q['required'] && empty($question[$questionid]))
				{
					$this->error = $q['subject'].'，必填';
					return false;
				}
				elseif ($q['maxlength'] && strlen($question[$questionid]) > $q['maxlength'])
				{
					$this->error = $q['subject'].'，不得超过'.$q['maxlength'].'字节';
					return false;
				}
				elseif ($q['validator'])
				{
					$validator = & factory::validator();
					if (!$validator->execute($question[$questionid], $q['validator']))
					{
						$this->error = $validator->error();
						return false;
					}
				}
			}
			elseif ($q['type'] == 'textarea')
			{
				if ($q['required'] && empty($question[$questionid]))
				{
					$this->error = $q['subject'].'，必填';
					return false;
				}
				elseif ($q['maxlength'] && strlen($question[$questionid]) > $q['maxlength'])
				{
					$this->error = $q['subject'].'，不得超过'.$q['maxlength'].'字节';
					return false;
				}
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