<?php

class plugin_comment extends object 
{
	private $support, $comment;
	
	public function __construct(& $support)
	{
		$this->support = $support;
		$this->comment = loader::model('comment', 'comment');
	}
	
	public function after_add()
	{
    	$this->comment->set_inc('supports', array('commentid'=>$this->support->commentid));
	}
}