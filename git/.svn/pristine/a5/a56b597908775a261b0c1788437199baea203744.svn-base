<?php
class model_admin_question extends model implements SplSubject
{
	public $data;
	
	private $observers = array();
	
	function __construct()
	{
		parent::__construct();
        /**
         * 会计网
         */
        $user =  login_member();
        $this->_userid = $this->_userid ? $this->_userid : $user['user_id'];
        $this->_username = $this->_username ? $this->_username : $user['name'];
		$this->_table = $this->db->options['prefix'].'exam_question';
		$this->_primary = 'questionid';
		$this->_fields = array('questionid', 'subject', 'description', 'image', 'type', 'width', 'height', 'maxlength', 'validator', 'required', 'minoptions', 'maxoptions', 'allowfill', 'sort', 'votes', 'records', 'answerid', 'subjectid','knowledgeid','qtypeid','bandid','source','md5id', '91id');
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
        $answer = loader::model('admin/question_answer','exam')->get($this->data['answerid']);
        if ($answer)$this->data= array_merge($this->data,$answer);
		if ($this->data)
		{
			$this->event = 'after_get';
			$this->notify();
		}
		return $this->data;
	}
	function write($questionid)
    {

        $this->data = parent::get($questionid);

        if ($this->data)
        {
            $this->event = 'after_write';
            $this->notify();
        }
        return true;
    }
	function add($data)
	{
        //printR($data);
		$this->data = $data;
        $qids = implode(',', $data['qids']);
		$this->event = 'before_add';
		$this->notify();
		
		$data = $this->filter_array($this->data, array('subject', 'description', 'image', 'type', 'width', 'height', 'maxlength', 'validator', 'required', 'minoptions', 'maxoptions', 'allowfill', 'sort', 'subjectid','knowledgeid','qtypeid', 'source', '91id'));
		$this->questionid = $this->insert($data);
        $this->update(array('md5id'=>md5($this->questionid.'exam')), $this->questionid);
        if ($this->questionid)
        {
			$this->event = 'after_add';
			$this->notify();
        }
        if ($qids)$this->update(array('bandid'=>$this->questionid), $qids);
		return $this->questionid;
	}
	function edit($questionid, $data)
	{
		if (!isset($data['required'])) $data['required'] = 0;
		if (!isset($data['allowfill'])) $data['allowfill'] = 0;
        $qids = implode(',', $data['qids']);

		$this->questionid = intval($questionid);
		$this->data = $data;
		$this->event = 'before_edit';
		$this->notify();
		
		$data = $this->filter_array($this->data, array( 'subject', 'description', 'image', 'type', 'width', 'height', 'maxlength', 'validator', 'required', 'minoptions','maxoptions', 'allowfill', 'sort', 'subjectid','knowledgeid','qtypeid', 'source', '91id'));
		$result = $this->update($data, $this->questionid);
        if ($qids)$this->update(array('bandid'=>$this->questionid), $qids);
        $this->update(array('md5id'=>md5($questionid.'exam')), $questionid);
		if ($result)
		{
			$this->event = 'after_edit';
			$this->notify();
		}

        record2url(WWW_URL . 'exam/question/'.md5($this->questionid.'exam'). '.html');
		return $result;
	}
	function del($questionid)
    {
        $this->data = $this->get($questionid);
        if($this->delete($questionid)){
            $this->event = 'after_clear';
            $this->notify();
        }
        return $questionid;

    }
	function ls($where =null, $fields = '*', $order = 'questionid DESC', $page = 1, $size = 20)
	{

		$this->event = 'before_ls';
		$this->notify();

		$this->data = $this->page($where, $fields, $order, $page, $size);
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