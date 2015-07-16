<?php

class plugin_exam_option extends object
{
	private $question, $option,$question_answer;
	
	public function __construct(& $question)
	{
		$this->question = $question;
		$this->option = loader::model('question_option','exam');
		$this->question_answer = loader::model('question_answer','exam');
	}
	public function after_get()
	{

		$this->question->data = $this->get_option($this->question->data);
		$this->output($this->question->data);
	}
    public function get_option($question)
    {
        if ($question['type'] == 'read') {
            $val = loader::model('question', 'exam')->select(array('bandid'=>$question['questionid']));
            foreach ($val as $v) {
                $question['questions'][] = $this->get_option($v);
            }
        } else {
            $question['option'] = loader::model('admin/question_option','exam')->select(array('questionid'=>$question['questionid']), 'optionid,questionid,name,sort');
        }
        $answer = loader::model('question_answer','exam')->get($question['answerid'],'answer,analysis');
        $question['description'] = htmlspecialchars_decode($question['description']);
        $answer['analysis'] = htmlspecialchars_decode($answer['analysis']);
        if ($answer)$question = array_merge($question, $answer);
        return $question;
    }
	public function after_ls()
	{
        $notes = array();
		foreach ($this->question->data as $i=>$r)
		{
            $r['description'] = htmlspecialchars_decode($r['description']);
			$r['option'] = loader::model('question_option','exam')->ls($r['questionid']);
            if ($r['answerid']) {
                $answer = loader::model('question_answer','exam')->get($r['answerid']);
                if ($answer)$r= array_merge($r,$answer);
            }
            if ($this->question->data['is_note']){
                $notes = loader::model('exam_notes','exam')->get(array('questionid'=>$r['questionid']));
                $r['notes'] = $notes;
            }
            if ($this->question->data['is_answer']){
                $notes = loader::model('answer_option','exam')->get(array('questionid'=>$r['questionid']));
                $r['notes'] = $notes;
            }
            if ($r['type'])$r = $this->get_option($r);
			$this->question->data[$i] = $r;
		}
		$this->question->data = array_map(array($this, 'output'), $this->question->data);
	}
	private function output(& $r)
	{
        return $r;
		$option = $this->option->ls($r['questionid']);
		foreach ($option as $k=>$v)
		{
			$option[$k]['percent'] = $r['votes'] ? round($v['votes']/$r['votes']*100, 2) : 0;
		}
		$r['option'] = $option;
                return $r;
	}
}