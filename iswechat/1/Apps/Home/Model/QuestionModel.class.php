<?php
namespace Home\Model;

use Think\Model;

class QuestionModel extends Model {
    protected $tablePrefix = 'cms_';
    protected $tableName = 'exam_question';

    private $alphabet = array(1=>'A',2=>'B',3=>'C',4=>'D',5=>'E',6=>'F',7=>'G');

    public function getQuestion2Key($key = null)
    {
        $where = $this->segmentKey($key);
        $data = M('cms_exam_question')->where($where)->field('questionid,subject,description,answerid')->find();
        if(!$data)$data = M('cms_exam_question')->order(" rand() ")->field('questionid,subject,description,answerid')->find();
        if($data){
            $op = M('cms_exam_question_option')->where("questionid={$data['questionid']}")->field('optionid,name,sort')->select();
            $answer = M('cms_exam_question_answer')->where("answerid={$data['answerid']}")->field('answer')->find();
            $_data = $this->questionOp($op, $answer['answer']);
            $data = $_data ? array_merge($data, $_data) : $data;
        }
        return $data;
    }

    private function questionOp($op, $answer)
    {
        $answer = explode(',', $answer);
        $_answer = null;
        foreach($op as $v) {
            $ops[$this->alphabet[$v['sort']]] = $v['name'];
            if (in_array($v['optionid'], $answer)){
                $_answer = $_answer ? ','.$this->alphabet[$v['sort']] : $this->alphabet[$v['sort']];
            }
        }
        return array('op'=>$ops, 'answer'=>$_answer);
    }
    private function segmentKey($key)
    {
        if (!$key)return null;
        $seg = new \SaeSegment();
        $key = $seg->segment($key, 1);

        $where = array();
        foreach ($key as $k){
            $where[]  = " subject LIKE '%{$k['word']}%'";
        }
        if ($where) {
            $where = implode(' or ', $where);
        }
        return $where;
    }


}