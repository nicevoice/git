<?php

class plugin_answer extends object 
{
	private $survey, $answer;
	
	public function __construct(& $survey)
	{
		$this->survey = $survey;
		$this->answer = loader::model('answer','survey');
	}
	
	public function before_answer()
	{
        $this->survey->answerid = $this->answer->add($this->survey->contentid, $this->survey->data);
        if (!$this->survey->answerid)
        {
        	$this->survey->error = $this->answer->error();
        }
	}
}