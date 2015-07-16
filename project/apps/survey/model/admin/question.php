<?php
class model_admin_question extends model implements SplSubject
{
	public $data;
	
	private $observers = array();
	
	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'survey_question';
		$this->_primary = 'questionid';
		$this->_fields = array('questionid', 'contentid', 'subject', 'description', 'image', 'type', 'width', 'height', 'maxlength', 'validator', 'required', 'minoptions', 'maxoptions', 'allowfill', 'sort', 'votes', 'records');
		$this->_readonly = array('questionid');
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array('subject'=>array('not_empty' =>array('主题不能为空')));
	}
	
	function get($questionid)
	{
		$this->questionid = intval($questionid);
		
		$this->event = 'before_get';
		$this->notify();
		
		$this->data = parent::get($this->questionid);
		if ($this->data)
		{
			$this->event = 'after_get';
			$this->notify();
		}
		return $this->data;
	}
	
	function add($data)
	{
		$this->data = $data;
		
		$this->event = 'before_add';
		$this->notify();
		
		$data = $this->filter_array($this->data, array('contentid', 'subject', 'description', 'image', 'type', 'width', 'height', 'maxlength', 'validator', 'required', 'minoptions', 'maxoptions', 'allowfill', 'sort'));
		$this->questionid = $this->insert($data);
        if ($this->questionid)
        {
			$this->event = 'after_add';
			$this->notify();
        }
		return $this->questionid;
	}

	function edit($questionid, $data)
	{
		if (!isset($data['required'])) $data['required'] = 0;
		if (!isset($data['allowfill'])) $data['allowfill'] = 0;
		
		$this->questionid = intval($questionid);
		$this->data = $data;
		$this->event = 'before_edit';
		$this->notify();
		
		$data = $this->filter_array($this->data, array('contentid', 'subject', 'description', 'image', 'type', 'width', 'height', 'maxlength', 'validator', 'required', 'minoptions','maxoptions', 'allowfill', 'sort'));
		$result = $this->update($data, $this->questionid);
		if ($result)
		{
			$this->event = 'after_edit';
			$this->notify();
		}
		return $result;
	}
	
	function ls($contentid)
	{
		$this->contentid = intval($contentid);
		
		$this->event = 'before_ls';
		$this->notify();
		
		$this->data = $this->gets_by('contentid', $this->contentid, '*', '`sort` ASC, `questionid` ASC');
		if ($this->data)
		{
			$this->event = 'after_ls';
			$this->notify();
		}
		return $this->data;
	}
	
	function clear($contentid)
	{
		$this->contentid = intval($contentid);
		
		$this->event = 'before_clear';
		$this->notify();
		
		$result = $this->update(array('votes'=>0, 'records'=>0), "`contentid`=".$this->contentid);
		if ($result)
		{
			$this->event = 'after_clear';
			$this->notify();
		}
		return $result;
	}
	
    function sort($questionid, $sort)
    {
    	$questionid = intval($questionid);
    	$sort = intval($sort);
    	return $this->set_field('sort', $sort, $questionid);
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