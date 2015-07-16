<?php

class model_admin_automatic extends model implements SplSubject
{
	public $content, $catid, $modelid, $contentid, $data, $fields,$category;
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
		$this->_table = $this->db->options['prefix'].'exam_automatic';
		$this->_primary = 'automaticid';
		$this->_fields = array('automaticid', 'catid', 'title', 'description', 'pub', 'integral', 'qtype','knowledge', 'subject', 'created', 'createdby', 'examtime','isday');
		$this->_readonly = array('automaticid');
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
        $this->contentid = 0;
        $this->content = loader::model('admin/content', 'system');
        $this->category = & $this->content->category;
        $this->modelid = modelid('exam');

	}
    public function add($post){
        $this->data = $post;
        if (!in_array($this->modelid, array_keys(unserialize($this->category[$this->data['catid']]['model'])))){
            $this->error = '栏目不支持该模型';
            return '';

        }
        //3月31日会计从业考试每日一练，全部答对奖励6金币！
        if ($this->data['isday'])$this->data['title'] = date('m月d日', time()) .$this->category[$this->data['catid']]['name'] .'每日一练';
        $this->data['created'] = time();
        $this->data['qtype'] = serialize($this->data['qtype']);
        if ($this->data['knowledge'])$this->data['knowledge'] = serialize($this->data['knowledge']);
        $this->data['subject'] = $this->data['subject'];
        $this->data['createdby'] = $this->_userid;
        $data = $this->filter_array($this->data, $this->_fields);
        $this->automaticid = $this->insert($data);
        if ($this->automaticid) {
            $this->event = 'after_assemble';
            $this->notify();
        }
        return $this->contentid;
    }
    public function cexam($data)
    {
        $this->data = $data;
        if ($this->data['isday'])$this->data['title'] = date('m月d日', time()) .$this->category[$this->data['catid']]['name'] .'每日一练';
        $this->modelid = modelid('exam');
        $this->automaticid = $data['automaticid'];
        $this->event = 'after_assemble';
        $this->notify();
        return $this->contentid;
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