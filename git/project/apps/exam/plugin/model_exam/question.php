<?php

class plugin_question extends object 
{
	private $exam;
	
	public function __construct(& $exam)
	{
		$this->exam = $exam;
	}

	public function after_get()
	{
        $redis = redis('exam_question_cache');
        $key = "exam_content_". $this->exam->contentid;
        //减少数据库查询
        if (!$data = $redis->get($key) ){
            $propertys = common_data('property_0', 'brand');
            $data = loader::model('admin/exam_qtype','exam')->select(array('contentid'=>$this->exam->contentid), '*', '', 40);
            $db = factory::db();

            foreach ($data as $k=>$v) {
                $data[$k]['alias'] = $v['alias'] ? $v['alias'] : $propertys[$v['qid']]['name'];
                $sql = "SELECT * FROM #table_exam_qtype_question qq, #table_exam_question q WHERE qq.questionid=q.questionid AND qq.qtypeid={$v['qtypeid']}";
                $question = $db->select($sql);
                //echo $db->sql,'<hr>';
                $data[$k]['question'] = $this->get_option($question);
            }
            $redis->set($key, $data, 24*60);
        }
        $this->exam->data['question']  = $data;
	}
    public function get_option($question)
    {
        $notes = $answer = array();
        foreach ($question as $k1=>$v1) {

            $v1['description'] && $question[$k1]['description'] = stripslashes_deep($v1['description']);
            $question[$k1]['description'] && $question[$k1]['description'] = htmlspecialchars_decode($question[$k1]['description']);
            !$v1['description'] && $question[$k1]['description'] = '没有解析...';
            if ($v1['type'] == 'read') {

                $val = loader::model('question', 'exam')->select(array('bandid'=>$v1['questionid']));
                $question[$k1]['questions'] = $this->get_option($val);

            }

            $question[$k1]['options'] = loader::model('admin/question_option','exam')->select(array('questionid'=>$v1['questionid']), 'optionid,questionid,name,sort');
            $notes = loader::model('exam_notes','exam')->get(array('questionid'=>$v1['questionid']));
            if ($notes)$question[$k1]['notes'] = $notes;
            $answer = loader::model('question_answer','exam')->get($v1['answerid'],'answer,analysis');
            $answer['analysis'] && $answer['analysis'] = stripslashes_deep($answer['analysis']);
            $answer['analysis'] && $answer['analysis'] = htmlspecialchars_decode($answer['analysis']);
            if ($answer)$question[$k1] = array_merge($question[$k1], $answer);
        }
        return $question;
    }
	
	public function after_ls()
	{
		foreach ($this->exam->data as $i=>$r)
		{
			$r['question'] = loader::model('admin/question','exam')->ls($r['contentid']);
			$this->exam->data[$i] = $r;
		}
	}
}