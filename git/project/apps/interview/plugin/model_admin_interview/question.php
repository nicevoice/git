<?php
class plugin_question extends object 
{
	private $interview, $question;
	
	public function __construct(& $interview)
	{
		$this->interview = $interview;
		$this->question = loader::model('admin/question','interview');
	}
	
	public function after_get()
	{
		if ($this->interview->action == 'view')
		{
			$this->interview->data['questions'] = $this->question->count("`contentid`={$this->interview->contentid} AND `state`>1");
		}
	}
}