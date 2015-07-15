<?php

class model_answer extends model implements SplSubject
{
    public $data,$question;
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
		$this->_table = $this->db->options['prefix'].'exam_answer';
		$this->_primary = 'answerid';
		$this->_fields = array('answerid', 'contentid', 'created', 'createdby', 'plantform_id','examtime', 'isfinish', 'right','ip', 'openid');
		$this->_readonly = array('answerid');
		$this->_create_autofill = array('created'=>TIME, 'createdby'=>$this->_userid, 'ip'=>IP);
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();
	}
	function add($contentid, $data)
	{

        /*****
         * 格式化时间 59:58 ----> 3598s
         */

        $examtime = $data['examtime'];
        $examtime = explode(':', $examtime);
        $examtime = $examtime[0] * 60 + $examtime[1];
        $answers = $data['answer'];
		$contentid = intval($contentid);
        $insert = array('contentid'=>$contentid, 'right'=>0, 'examtime'=>$examtime, 'isfinish'=>$data['isfinish'],'plantform_id'=>$data['plantform_id']);
        if($data['userid']){
            $this->_userid = $data['userid'];
            $insert['created'] = TIME;
            $insert['createdby'] =  $this->_userid;
            $insert['ip'] = IP;
        }
        if ($data['openid']) {
            $insert['openid'] = $data['openid'];
            $insert['createdby'] =  0;
        }
		$answerid = $this->insert($insert);
		if ($answerid)
		{
			$option = loader::model('answer_option','exam');
			$record = loader::model('answer_record','exam');
            $questionids = implode(',', array_keys($answers));
            $sql = "SELECT q.type,a.answer,q.questionid,q.subjectid FROM #table_exam_question q, #table_exam_question_answer a WHERE q.answerid=a.answerid AND q.questionid IN ({$questionids})";

			$questions = $this->db->select($sql);
            $exams = $this->db->get("SELECT integral FROM #table_exam WHERE contentid={$contentid}");
            $worng = $right = $integral = 0;
			foreach ($questions as $q)
			{
				if ($q['type'] == 'checkbox' || $q['type'] == 'radio' || $q['type'] == 'select')
				{
					if (isset($answers[$q['questionid']]))
					{
                        if ($q['type'] == 'checkbox') {
                            $answers[$q['questionid']] = is_array($answers[$q['questionid']]) ? implode(',', $answers[$q['questionid']]) : $answers[$q['questionid']];
                            $q_answers = explode(',', $q['answer']);
                            $q_answers_u = explode(',', $answers[$q['questionid']]);
                            sort($q_answers);
                            sort($q_answers_u);
                            $worng = $q_answers == $q_answers_u ? 1 : 0;
                        } else {
                            $worng = $q['answer'] == $answers[$q['questionid']] ? 1 : 0;
                        }

					} else {
                        $answers[$q['questionid']] = 0;
                        $worng = 0;
                    }
                    if($worng) ++$right;
                    $_inserts[] = "({$answerid}, {$q['questionid']}, 0, {$worng}, '{$answers[$q['questionid']]}')";
				}
				elseif (($q['type'] == 'text' || $q['type'] == 'textarea') && !empty($answers[$q['questionid']]))
				{
					$record->add($q['questionid'], $answerid, $answers[$q['questionid']]);
				}
                if(empty($this->data['isfinish'])) {
                    $sql = "UPDATE #table_exam_question SET `votes` = `votes` + 1 WHERE questionid={$q['questionid']}";
                    $this->db->query($sql);
                }
			}
            $qtype_insert = "INSERT INTO #table_exam_answer_option(answerid, questionid, isdel, wrong, optionid) VALUES" . implode(',', $_inserts);
            $this->db->query($qtype_insert);
            $integral = $right * $exams['integral'];
            $this->update(array('right'=>$right), $answerid);
            $this->data = $data;
            if(empty($this->data['isfinish'])) {
                if($integral && $this->_userid){
                    $rank = loader::model('exam_rank','exam');
                    $up = array(  'plantform_id'=>$data['plantform_id'],
                                        'gold'         =>$integral,
                                        'user_id'      =>$this->_userid,
                                        'created'      =>TIME,
                                        'subjectid'    =>$questions['0']['subjectid'],
                    );
                    $rank->add($up);
                }
                $sql = " contentid ={$contentid} AND isfinish=0 AND createdby={$this->_userid} AND answerid != $answerid";
                if (!$this->get($sql, 'answerid')){
                    $sql = "UPDATE #table_exam SET `count` = `count` + 1 WHERE contentid={$contentid}";
                    $this->db->query($sql);
                    if ($integral > 0){
                    	$this->update_bbs_integral($integral);
                    }
                }
            }

		}
		return $answerid;
	}
	
    /**
     * 加金币
     * @param $integral
     */
    private function update_bbs_integral($integral)
    {
		
        $r = load_rpc('bbs')->add_glodcoin($this->_userid, $integral);
    }
    
    public function edit($contentid, $data )
    {


        $answerid = intval($data['answerid']);
        /*****
         * 格式化时间 59:58 ----> 3598s
         */
        $examtime = $data['examtime'];
        $examtime = explode(':', $examtime);
        $examtime = $examtime[0] * 60 + $examtime[1];
        $answers = $data['answer'];
        if ($answerid && $answers)
        {
            $question = loader::model('question','exam');
            $option = loader::model('answer_option','exam');
            $record = loader::model('answer_record','exam');
            $questionids = implode(',', array_keys($answers));
            $sql = "SELECT q.type,a.answer,q.questionid FROM #table_exam_question q, #table_exam_question_answer a WHERE q.answerid=a.answerid AND q.questionid IN ({$questionids})";

            $questions = $this->db->select($sql);
            // printR($questions);

            //查找对错
            $exams = $this->db->get("SELECT integral FROM #table_exam WHERE contentid={$contentid}");
            $worng = $right = $integral = 0;
            foreach ($questions as $q)
            {

                if ($q['type'] == 'checkbox' || $q['type'] == 'radio' || $q['type'] == 'select')
                {

                    if (isset($answers[$q['questionid']]))
                    {
                        if ($q['type'] == 'checkbox') {
                            $q_answers = explode(',', $q['answer']);
                            $q_answers_u = explode(',', $answers[$q['questionid']]);
                            sort($q_answers);
                            sort($q_answers_u);
                            $worng = $q_answers == $q_answers_u ? 1 : 0;
                        } else {
                            $worng = $q['answer'] == $answers[$q['questionid']] ? 1 : 0;
                        }

                    } else {
                        $answers[$q['questionid']] = 0;
                        $worng = 0;
                    }
                    if($worng) ++$right;
                    $option->vote_update($q['questionid'], $answerid, $answers[$q['questionid']], $worng);
                }
                elseif (($q['type'] == 'text' || $q['type'] == 'textarea') && !empty($answers[$q['questionid']]))
                {
                    $record->update(array('content' => $answers[$q['questionid']]), array('questionid'=>$q['questionid'], 'answerid'=>$answerid));
                }
            }
            $integral = $right * $exams['integral'];
            $this->update(array('examtime'=>$examtime, 'isfinish'=>$data['isfinish'],'right'=>$right), $answerid);
            $this->data = $data;
            if(empty($this->data['isfinish'])) {
                if($integral&&$this->_userid){
                    $rank = loader::model('exam_rank','exam');
                    $data = array(  'plantform_id'=>$data['plantform_id'],
                                    'gold'         =>$integral,
                                    'user_id'      =>$this->_userid,
                                    'created'      =>TIME,
                                    'subjectid'    =>$questions['0']['subjectid'],
                    );
                 //   $rank->add($data);
                }
                $sql = " contentid ={$contentid} AND isfinish=0 AND createdby={$this->_userid} AND answerid != $answerid";
                if (!$this->get($sql, 'answerid')){
                    $sql = "UPDATE #table_exam SET `count` = `count` + 1 WHERE contentid={$contentid}";
                    $this->db->query($sql);
                    if ($integral > 0)$this->update_bbs_integral($integral);
                }
            }

        }
        return $answerid;
    }

    /**
     * 获取答案
     * @param $answerid
     */
    function get_my_answer($answerid)
    {
        if (empty($answerid))return array();
        $answer = $this->get(array('answerid'=>$answerid,'createdby'=>$this->_userid));
        $options =  loader::model('answer_option','exam')->select($answerid);
        foreach($options as $op) {
            $answer['option'][$op['questionid']] = $op;
        }
        return $answer;
    }

    /**
     * 获取头像
     * @param $contentids
     * @return mixed
     */
    function get_answer_createdby($contentids)
    {

        if (!is_array($contentids))$contentids = explode(',', $contentids);
        foreach ($contentids as $contentid) {
            $lists = $this->db->select("SELECT createdby FROM #table_exam_answer WHERE contentid ={$contentid} GROUP BY createdby ORDER BY answerid  DESC LIMIT 15");
            foreach($lists as $list) {
                $return[$contentid][$list['createdby']] = UC_URL."avatar.php?uid={$list['createdby']}&size=small";
            }
        }
        return $return;
    }
    /**
     * 获取头像
     * @param $contentids
     * @return mixed
     */
    function getreward($contentid, $num =40)
    {

        $lists = $this->select("contentid = {$contentid} AND `right` > 0 AND isfinish =0", 'createdby', null, $num);
        foreach($lists as $list) {
            $return[$list['createdby']] = UC_URL."avatar.php?uid={$list['createdby']}&size=small";
        }
        return $return;
    }

    /**
     * 用户的错误题目  知识点
     * @return mixed
     */
    public function get_error_knowledges($id)
    {
        if ($id > 0 && is_numeric($id))$subjectid = " AND q.subjectid=$id ";
        $sql = "SELECT q.knowledgeid FROM #table_exam_question q,#table_exam_answer a, #table_exam_answer_option o WHERE a.createdby={$this->_userid} AND q.type in('radio','checkbox','select') AND q.questionid=o.questionid AND o.wrong=0 AND a.answerid=o.answerid AND o.isdel = 0 {$subjectid} group by q.knowledgeid";

        $lists = $this->db->select($sql);
        foreach ($lists as $val) {
            $list[$val['knowledgeid']] = $val['knowledgeid'];
        }
        return $list;
    }

    /**
     * 统计错误知识点的题目数量
     * @param $keys
     * @return mixed
     */
    public function get_error_knowledges_count($id, $keys)
    {
        if ($id > 0 && is_numeric($id))$subjectid = " AND q.subjectid=$id ";
        if (is_array($keys))$keys = implode(',', $keys);
        $sql = "SELECT q.knowledgeid, count(*) as c FROM #table_exam_question q,#table_exam_answer a, #table_exam_answer_option o WHERE a.createdby={$this->_userid} AND o.wrong=0 AND q.knowledgeid in ({$keys}) AND q.questionid=o.questionid AND q.type in('radio','checkbox','select') AND a.answerid=o.answerid AND o.isdel = 0 {$subjectid} group by q.knowledgeid";
        $lists = $this->db->select($sql);

        foreach ($lists as $val) {
                $counts[$val['knowledgeid']] = $val['c'];
        }
        return $counts;
    }


    public function get_answer_by_contentid($contentids)
    {
        if (is_array($contentids))$contentids = implode(',', $contentids);
        $sql = "SELECT contentid, count(*) as c FROM #table_exam_answer WHERE contentid in ({$contentids})  GROUP BY contentid";
        $lists = $this->db->select($sql);
        $counts = array();
        foreach ($lists as $val) {
            $counts[$val['contentid']] = $val['c'];
        }
        return $counts;
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