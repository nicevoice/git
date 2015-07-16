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
		$data = loader::model('admin/exam_qtype','exam')->select(array('contentid'=>$this->exam->contentid), '*', '', 40);

        $db = factory::db();
        foreach ($data as $k=>$v) {
            $sql = "SELECT q.questionid,q.subject,qq.id FROM #table_exam_qtype_question qq, #table_exam_question q WHERE qq.questionid=q.questionid AND qq.qtypeid={$v['qtypeid']}";
            $question = $db->select($sql);

            $data[$k]['question'] = $question;
        }
        $this->exam->data['question']  = $data;
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