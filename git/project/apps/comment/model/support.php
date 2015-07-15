<?php

class model_support extends model implements SplSubject
{
    public $data = array(), $event, $commentid;

    private $observers = array();

    function __construct()
    {
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'comment_support';
		$this->_primary = 'supportid';
		$this->_fields = array('supportid', 'commentid', 'ip', 'created', 'createdby');
		$this->_readonly = array('supportid');
		$this->_create_autofill = array('ip'=>IP, 'created'=>TIME, 'createdby'=>$this->_userid);
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array(
		'commentid' => array('integer' => array('评论ID必须是整数'),
		                  ),
        );
    }

    //支持
	// 2011-04-18 新增参数 $timeinterval 控制时间间隔 
    public function add($commentid, $timeinterval = null)
    {
    	$this->commentid = intval($commentid);
    	$this->data = array('commentid'=>$this->commentid);
    	$where = "commentid=".$this->commentid;
    	$where .= $this->_userid ? " AND createdby=".$this->_userid : " AND ip=".IP;
    	$r = $timeinterval ? parent::get($where, 'created', 'created DESC') : null;
    	if ($r && $r['created'] >= TIME - $timeinterval)
    	{
    		$this->error = '您已经顶过了';
    		return false;
    	}
    	$this->event = 'before_add';
    	$this->notify();
    	if ($this->supportid = $this->insert($this->data))
    	{
	    	$this->event = 'after_add';
	    	$this->notify();
    	}
    	return $this->supportid;
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