<?php
class model_question extends model implements SplSubject
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
		$this->_fields = array('questionid', 'subject', 'description', 'image', 'type', 'width', 'height', 'maxlength', 'validator', 'required', 'minoptions', 'maxoptions', 'allowfill', 'sort', 'votes', 'records', 'answerid', 'subjectid','knowledgeid','qtypeid','bandid','source','md5id');
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
    /**
     * 缓存各个科目下的题量
     * @return mixed
     */
    public function inserCache2subject($subjectid = null)
    {

        $files = "subject_count.php";
        if (!$cache = cache_read($files)) {

            $_knowledge = config::get('exam', 'knowledge');
            $subject = array_keys($_knowledge);
            $randquestion = config::get('exam', 'randquestion');
            $qtype = get_property_child(101000);
            foreach ($subject as $s) {
                foreach ($qtype as $k=>$type) {

                    $sql = " subjectid={$s} AND qtypeid={$k}";
                    $counts[$s][$k] = $this->count($sql);
                }
            }
            $cache = array('time'=>time(), 'data'=>$counts);
            $counts && cache_write($files, $cache);
        } else {
            if (time() - $cache['time'] > 24*60*60*7)cache_delete($files);
        }
        return $subjectid ? $cache['data'][$subjectid] : $cache['data'];
    }

	function ls($where =null, $fields = '*', $order = 'questionid ASC', $page = 1, $size = 20, $note = true)
	{

		$this->event = 'before_ls';
		$this->notify();
        $answer_model =  loader::model('admin/question_answer','exam');
		$this->data = $this->page($where, $fields, $order, $page, $size);
        $answer = array();
        foreach($this->data as $k =>$val){
            $answer = $answer_model->get($val['answerid']);
            if ($answer)$this->data[$k] = array_merge($this->data[$k],$answer);
        }
        if ($note)$this->data['is_note'] = 1;
		if ($this->data)
		{
			$this->event = 'after_ls';
			$this->notify();
		}
        unset($this->data['is_note']);
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

    /**
     * 错误题目
     * @param $where
     * @param $page
     * @param $size
     * @return mixed
     */
    public function get_error_question($knowledgeid, $subjectid, $page, $pagesize = 20, $userid = null)
    {
        if ($subjectid > 0 && is_numeric($subjectid))$subjectsql = " AND q.subjectid=$subjectid ";
        if ($knowledgeid > 0 && is_numeric($knowledgeid))$knowledgesql = " AND q.knowledgeid IN ({$knowledgeid}) ";
        $userid && $this->_userid = $userid;
        $offset = ($page-1)*$pagesize;
        $sql = "SELECT q.knowledgeid,q.subject,q.questionid,q.qtypeid,q.source,q.answerid,q.votes,q.description FROM #table_exam_question q,#table_exam_answer a, #table_exam_answer_option o WHERE a.createdby={$this->_userid} AND q.type in('radio','checkbox','select')  AND q.questionid=o.questionid AND a.answerid=o.answerid  AND o.isdel = 0 AND o.wrong=0 {$subjectsql} {$knowledgesql} group by q.questionid  ORDER BY questionid DESC LIMIT $offset,$pagesize ";
        $this->data = $this->db->select($sql);
        $this->data['is_note'] = $userid ? 0 : 1;
        if ($this->data)
        {
            $this->event = 'after_ls';
            $this->notify();
        }
        unset($this->data['is_note']);
        return $this->data;
    }
    /**
     * 错误题目
     * @param $where
     * @param $page
     * @param $size
     * @return mixed
     */
    public function get_error_question_count($knowledgeid, $subjectid, $userid =null)
    {

        if ($subjectid > 0 && is_numeric($subjectid))$subjectsql = " AND q.subjectid=$subjectid ";
        if ($knowledgeid > 0 && is_numeric($knowledgeid))$knowledgesql = " AND q.knowledgeid IN ({$knowledgeid}) ";
        $userid && $this->_userid = $userid;
        $sql = "SELECT q.questionid FROM #table_exam_question q,#table_exam_answer a, #table_exam_answer_option o WHERE a.createdby={$this->_userid} AND q.questionid=o.questionid AND q.qtypeid in(101001, 101002, 101003, 101004) AND a.answerid=o.answerid AND o.isdel = 0 AND o.wrong=0 {$subjectsql}{$knowledgesql} GROUP BY q.questionid";
        $result = $this->db->select($sql);
        return count($result);
    }

    /**
     * 获取收藏的问题列表
     * @param $knowledgeid
     * @param $page
     * @param int $pagesize
     * @return bool
     */
    public function get_favorite_knowledges_question($knowledgeid, $subjectid, $page, $pagesize = 10)
    {
        $offset = ($page-1)*$pagesize;
        if ($subjectid > 0 && is_numeric($subjectid))$subjectid = " AND q.subjectid=$subjectid ";
        $sql = "SELECT q.knowledgeid,q.type,f.favoriteid,q.subject,q.questionid,q.qtypeid,q.source,q.answerid,q.votes,q.description FROM #table_exam_question q,#table_exam_member_favorite f WHERE f.createdby={$this->_userid} AND q.questionid=f.questionid AND q.knowledgeid in ({$knowledgeid}) {$subjectid} LIMIT $offset,$pagesize";
        $this->data =  $this->db->select($sql);
        // return $this->data;
        $this->data['is_note'] = 1;
        if ($this->data)
        {
            $this->event = 'after_ls';
            $this->notify();
        }
        unset($this->data['is_note']);
        return $this->data;
    }

    /**
     * 统计收藏的问题列表
     * @param $where
     * @param $page
     * @param $size
     * @return mixed
     */
    public function get_favorite_knowledges_question_count($knowledgeid, $subjectid)
    {
        if ($subjectid > 0 && is_numeric($subjectid))$subjectid = " AND q.subjectid=$subjectid ";
        $sql = "SELECT f.favoriteid FROM #table_exam_question q,#table_exam_member_favorite f WHERE f.createdby={$this->_userid} AND q.questionid=f.questionid AND q.knowledgeid in ({$knowledgeid}) {$subjectid}";
        $this->data = $this->db->select($sql);
        if ($this->data)
        {
            $this->event = 'after_ls';
            $this->notify();
        }
        return count($this->data);
    }



    /**
     * 获取笔记列表
     * @param $knowledgeid
     * @param $page
     * @param int $pagesize
     * @return bool
     */
    public function get_notes_knowledges_question($knowledgeid, $subjectid, $page, $pagesize = 10)
    {
        $offset = ($page-1)*$pagesize;
        if ($subjectid > 0 && is_numeric($subjectid))$subjectid = " AND q.subjectid=$subjectid ";
        $sql = "SELECT q.knowledgeid,q.subject,q.questionid,q.qtypeid,q.source,q.answerid,q.votes,q.description FROM #table_exam_question q,#table_exam_member_notes n WHERE n.createdby={$this->_userid} AND q.questionid=n.questionid AND q.knowledgeid in ({$knowledgeid}) {$subjectid}  LIMIT $offset,$pagesize";
        $this->data =  $this->db->select($sql);
        // return $this->data;
        $this->data['is_note'] = 1;
        if ($this->data)
        {
            $this->event = 'after_ls';
            $this->notify();
        }
        unset($this->data['is_note']);
        return $this->data;
    }

    /**
     * 统计收藏的问题列表
     * @param $where
     * @param $page
     * @param $size
     * @return mixed
     */
    public function get_notes_knowledges_question_count($knowledgeid, $subjectid)
    {
        if ($subjectid > 0 && is_numeric($subjectid))$subjectid = " AND q.subjectid=$subjectid ";
        $sql = "SELECT n.notesid FROM #table_exam_question q,#table_exam_member_notes n WHERE n.createdby={$this->_userid} AND q.questionid=n.questionid AND q.knowledgeid in ({$knowledgeid}) {$subjectid}";
        $result = $this->db->select($sql);
        return count($result);
    }

    /**
     * 搜索试题
     * @param $wd
     * @param $page
     * @param $pagesize
     */
    public function so($where, $page, $pagesize)
    {
        $this->data = $this->page($where, 'questionid,bandid,subject,source,subjectid', null, $page, $pagesize);
        if ($this->data)
        {
            $this->event = 'after_ls';
            $this->notify();
        }
        //printR($this->data);
        return $this->data;
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