<?php

class plugin_question extends object 
{
	private $survey;
	
	public function __construct(& $survey)
	{
		$this->survey = $survey;
	}
	
	public function after_get()
	{
		$this->survey->data['question'] = loader::model('admin/question','survey')->ls($this->survey->contentid);
	}
	
	public function after_ls()
	{
		foreach ($this->survey->data as $i=>$r)
		{
			$r['question'] = loader::model('admin/question','survey')->ls($r['contentid']);
			$this->survey->data[$i] = $r;
		}
	}
}