<?php

class plugin_survey_option extends object 
{
	private $question, $option;
	
	public function __construct(& $question)
	{
		$this->question = $question;
		$this->option = loader::model('admin/question_option','survey');
	}
	
	public function after_add()
	{
		if (!isset($this->question->data['option']) || empty($this->question->data['option'])) return false;
		$maxsort = 0;
		foreach ($this->question->data['option'] as $k=>$r)
		{
			$maxsort = $r['sort']>$maxsort?$r['sort']:$maxsort;
			$r['questionid'] = $this->question->questionid;
			$this->option->add($r);
		}
		
		if($this->question->data['allowfill']) $this->option->add(array('questionid'=>$this->question->questionid,'optionid'=>'','name'=>'其他','image'=>'','sort'=>$maxsort,'isfill'=>1));
	}
	
	public function after_edit()
	{
		if (!isset($this->question->data['option']) || empty($this->question->data['option'])) return false;
	
		$optionid = array();
		foreach ($this->question->data['option'] as $k=>$r)
		{		
			if ($r['optionid'])
			{
				$this->option->edit($r['optionid'], $r);
				$optionid[] = $r['optionid'];
			}
			else 
			{
				$r['questionid'] = $this->question->questionid;
				$optionid[] = $this->option->add($r);
			}
		}
		$this->option->delete_by($this->question->questionid, $optionid ,$this->question->data['allowfill']);
	}
	
	public function after_get()
	{
		$this->question->data['option'] = loader::model('admin/question_option','survey')->ls($this->question->questionid);
		$this->output($this->question->data);
	}
	
	public function after_ls()
	{
		foreach ($this->question->data as $i=>$r)
		{
			$r['option'] = loader::model('admin/question_option','survey')->ls($r['questionid']);
			$this->question->data[$i] = $r;
		}
		$this->question->data = array_map(array($this, 'output'), $this->question->data);
	}
	
	public function after_clear()
	{
		$questionids = $this->question->gets_field('questionid', "`contentid`=".$this->question->contentid);
		if ($questionids)
		{
			$questionids = implode(',', $questionids);
			$this->option->set_field('votes', 0, "`questionid` IN($questionids)");
		}
	}
	
	private function output(& $r)
	{
		$option = $this->option->ls($r['questionid']);
		foreach ($option as $k=>$v)
		{
			$option[$k]['percent'] = $r['votes'] ? round($v['votes']/$r['votes']*100, 2) : 0;
		}
		$r['option'] = $option;
                return $r;
	}
}