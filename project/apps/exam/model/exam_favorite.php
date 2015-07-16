<?php

class model_exam_favorite extends model {

    function __construct() {
        parent::__construct();
        /**
         * 会计网
         */
        $user =  login_member();
        $this->_userid = $this->_userid ? $this->_userid : $user['user_id'];
        $this->_username = $this->_username ? $this->_username : $user['name'];
        $this->_table = $this->db->options['prefix'] . 'exam_member_favorite';
        $this->_primary = 'favoriteid';
        $this->_fields = array('favoriteid', 'questionid', 'createdby','created', 'openid');
        $this->_readonly = array('favoriteid');
        $this->_create_autofill = array('created'=>TIME, 'createdby'=>$this->_userid);
        $this->_update_autofill = array();
        $this->_filters_input = array();
        $this->_filters_output = array();
        $this->_validators = array();
    }

    /**
     * 增加收藏
     * @param $data
     * @return bool|int
     */

    function add($data)
    {
        $data['questionid'] = $data['qid'];
		$this->_userid = $data['uid'] ? $data['uid'] : $this->_userid;
        if(!$data['questionid'] || (!$this->_userid && !$data['openid']))
        {
            $this->error = '网络延迟...';
            return false;
        }

        $this->data = $data;
        if ($this->get(array('createdby'=>$this->_userid, 'questionid'=>$this->data['qid']))){
            $this->error = '已收藏';
            return false;
        }
        $insert = $this->filter_array($this->data, $this->_fields);
        $insert['createdby'] = $insert['openid'] ? 0 : $this->_userid;
        return $this->insert($insert);
    }

    /**
     * 获取收藏本的知识点
     */
    function get_knowledge($id)
    {
        if ($id > 0 && is_numeric($id))$subjectid = " AND q.subjectid=$id ";
        $sql = "SELECT q.knowledgeid FROM #table_exam_question q,#table_exam_member_favorite f WHERE f.createdby={$this->_userid} AND q.questionid=f.questionid {$subjectid} group by q.knowledgeid";

        $lists = $this->db->select($sql);
        foreach ($lists as $val) {
            $list[$val['knowledgeid']] = $val['knowledgeid'];
        }
        return $list;
    }
    /**
     * 统计收藏本的知识点的题目数量
     * @param $keys
     * @return mixed
     */
    public function get_knowledges_count($id, $keys)
    {
        if ($id > 0 && is_numeric($id))$subjectid = " AND q.subjectid=$id ";
        if (is_array($keys))$keys = implode(',', $keys);
        $sql = "SELECT count(*) as c,q.knowledgeid FROM #table_exam_question q,#table_exam_member_favorite f WHERE f.createdby={$this->_userid} AND q.questionid=f.questionid AND q.knowledgeid in ({$keys}) {$subjectid} group by q.knowledgeid";

        $lists = $this->db->select($sql);
        $counts = array();
        foreach ($lists as $val) {
            $counts[$val['knowledgeid']] = $val['c'];
        }
        return $counts;
    }


}