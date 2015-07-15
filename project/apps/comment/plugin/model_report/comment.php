<?php

class plugin_comment extends object 
{
	private $report, $comment;
	
	public function __construct(& $report)
	{
		$this->report = $report;
		$this->comment = loader::model('comment', 'comment');
	}
	
	public function after_add()
	{
    	$this->comment->set_inc('reports', array('commentid'=>$this->report->commentid));
	}
}