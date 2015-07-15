<?php

class plugin_answer extends object 
{
	private $exam, $answer;
	
	public function __construct(& $exam)
	{
		$this->exam = $exam;
		$this->answer = loader::model('answer','exam');
	}
	
	public function before_answer()
	{
        $this->exam->answerid = $this->answer->add($this->exam->contentid, $this->exam->data);
        if (!$this->exam->answerid)
        {
        	$this->exam->error = $this->answer->error();
        }
	}
}