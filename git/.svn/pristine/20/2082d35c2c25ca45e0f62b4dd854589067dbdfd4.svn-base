<?php

class model_exam_notes extends model {

    function __construct() {
        parent::__construct();
        /**
         * 会计网
         */
        $user =  login_member();
        $this->_userid = $this->_userid ? $this->_userid : $user['user_id'];
        $this->_username = $this->_username ? $this->_username : $user['name'];
        $this->_table = $this->db->options['prefix'] . 'exam_member_notes';
        $this->_primary = 'notesid';
        $this->_fields = array('notesid', 'questionid', 'createdby','created', 'content');
        $this->_readonly = array('notesid');
        $this->_create_autofill = array('created'=>TIME, 'createdby'=>$this->_userid);
        $this->_update_autofill = array();
        $this->_filters_input = array();
        $this->_filters_output = array();
        $this->_validators = array();
    }
    public function add($data) {
        $data['questionid'] = $data['id'];
        return $this->insert($data);
    }
    public function edit($data) {
        $data['questionid'] = $data['id'];
        return $this->update($data, $data['notesid']);
    }
    /**
     * 获取笔记的知识点
     */
    function get_knowledge($id)
    {
        if ($id > 0 && is_numeric($id))$subjectid = " AND q.subjectid=$id ";
        $sql = "SELECT q.knowledgeid FROM #table_exam_question q,#table_exam_member_notes n WHERE n.createdby={$this->_userid} AND q.questionid=n.questionid {$subjectid} group by q.knowledgeid";
        $lists = $this->db->select($sql);
        foreach ($lists as $val) {
            $list[$val['knowledgeid']] = $val['knowledgeid'];
        }
        return $list;
    }
    /**
     * 统计笔记本的知识点的题目数量
     * @param $keys
     * @return mixed
     */
    public function get_knowledges_count($id, $keys)
    {
        if ($id > 0 && is_numeric($id))$subjectid = " AND q.subjectid=$id ";
        if (is_array($keys))$keys = implode(',', $keys);
        $sql = "SELECT count(*) as c,q.knowledgeid FROM #table_exam_question q,#table_exam_member_notes n WHERE n.createdby={$this->_userid} AND q.questionid=n.questionid AND q.knowledgeid in ({$keys}) {$subjectid} group by q.knowledgeid";
        $lists = $this->db->select($sql);
        $counts = array();
        foreach ($lists as $val) {
            $counts[$val['knowledgeid']] = $val['c'];
        }
        return $counts;
    }
}