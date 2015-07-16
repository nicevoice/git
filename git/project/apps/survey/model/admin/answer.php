<?php

class model_admin_answer extends model {

    private $iplocation;

    function __construct() {
        parent::__construct();
        $this->_table = $this->db->options['prefix'] . 'survey_answer';
        $this->_primary = 'answerid';
        $this->_fields = array('answerid', 'contentid', 'created', 'createdby', 'ip');
        $this->_readonly = array('answerid');
        $this->_create_autofill = array('created' => TIME, 'createdby' => $this->_userid, 'ip' => IP);
        $this->_update_autofill = array();
        $this->_filters_input = array();
        $this->_filters_output = array();
        $this->_validators = array();
    }

    function ls($contentid) {
        return $this->gets_by('contentid', $contentid, '*', 'answerid ASC');
    }

    function clear($contentid) {
        return $this->delete_by('contentid', $contentid);
    }

    function _after_select(& $data, $multiple) {
        import('helper.iplocation');
        $this->iplocation = new iplocation();

        if ($multiple) {
            $data = array_map(array($this, 'output'), $data);
        } else {
            $data = $this->output($data);
        }
        return $data;
    }

    private function output(& $r) {
        if (!$r)
            return;
        $r['createdbyname'] = $r['createdby'] > 0 ? username($r['createdby']) : '匿名';
        $r['created'] = date('Y-m-d H:i', $r['created']);
        $r['iparea'] = $this->iplocation->get($r['ip']);
        return $r;
    }

    function search($data, $contentid) {
        $post = $data;
        $question = loader::model('question', 'survey');
        $export = loader::model('admin/export', 'survey');
        $alldata = $this->select("`contentid`=$contentid", '*', 'answerid DESC');
        $questions = $question->ls($_GET['contentid']);
        foreach ($post['question'] as $qid => $qv) {
            if ($questions[$qid]['type'] == 'select') {
                $qv != -1 && $post['option'][$qv] = $qv;
                unset($post['question'][$qid]);
            }
            if (empty($qv))
                unset($post['question'][$qid]);
        }
        if (empty($post['question']))
            unset($post['question']);
        if (!empty($post['data'])) {
            foreach ($post['data'] as $qid => $fill) {
                !empty($fill['content']) && $post['question'][$qid] = $fill['content'];
            }
            unset($post['data']);
        }

        $answers = array();
        foreach ($alldata as $k => $v) {
            $ok = true;
            if (isset($post['option'])) {
                $options = $export->getoptions($v['answerid']);
                $optionids = array();
                foreach ($options as $o) {
                    $optionids[] = $o['optionid'];
                }
                $diff = array_diff($post['option'], $optionids);
                $ok = !empty($diff) ? false : true;
            }
            if (isset($post['question'])) {
                $records = $export->getrecodes($v['answerid']);
                $recordsqids = array();
                foreach ($records as $r => $rv) {
                    $recordsqids[$rv['questionid']] = $rv['questionid'];
                }
                $diff = array_diff_key($post['question'], $recordsqids);
                $ok = !empty($diff) ? false : true;
                if ($ok) {
                    foreach ($records as $r) {
                        if (array_key_exists($r['questionid'], $post['question'])) {
                            !is_numeric(strpos($r['content'], $post['question'][$r['questionid']])) && $ok = false;
                            break;
                        }
                    }
                }
            }
            $ok && $answers[] = $v;
        }
        return $answers;
    }

    /**
     * 删除一条调查记录
     * @param int $answerid 调查记录回答编号
     * @return boolean
     */
    public function delete($answerid, $contentid = 0) {
        $this->survey = loader::model('admin/survey');
        $this->question = loader::model('admin/question');
        $this->question_option = loader::model('admin/question_option');
        $this->answer_record = loader::model('admin/answer_record');
        $this->answer_option = loader::model('admin/answer_option');
        // 先取出 contentid
        if (!$contentid) {
            $contentid = $this->get_by('contentid', $answerid);
        }
        // 因为有外键级联删除，这里我要先取数据，只删主表即可
        if ($answerid && $contentid) {
            // 取出 answer中的questionid和optionid，给所有项目的统计减一（checkbox，radio，select）
            $answer_info = $this->answer_option->select("answerid=$answerid");
            foreach ($answer_info as $row) {
                $this->question->set_dec('votes', $row['questionid']);
                $this->question_option->set_dec('votes', $row['optionid']);
            }
            // 取出 answer中的questionid和optionid，给所有项目的统计减一（text，textarea）
            $answer_info = $this->answer_record->select("answerid=$answerid");
            foreach ($answer_info as $row) {
                $this->question->set_dec('records', $row['questionid']);
            }
            parent::delete($answerid);
            $this->survey->set_dec('answers', "contentid=$contentid");
            return TRUE;
        }
        return FALSE;
    }

}