<?php

class plugin_answer extends object
{
	private $answer;
	
	public function __construct(& $answer)
	{
		$this->answer = $answer;
	}
	
	public function after_notes()
    {
        /*$note_model = loader::model('exam_notes', 'exam');
        $questionid = implode(',', $this->answer->question);
        $notes = $note_model->select("questionid in($questionid)");
        foreach ($notes as $note) {
            $notes_ed[$note['questionid']] = $note;
        }
        foreach ($this->answer->question as $qid) {
            if (!$notes_ed[$qid])$note_model->insert(array('questionid'=>$qid, 'content'=>''));
        }*/
    }
}