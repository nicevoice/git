<?php

class plugin_interview extends object 
{
	private $search;
	
	public function __construct(& $search)
	{
		$this->search = $search;
	}
	
	public function before_search_interview()
	{
		$this->search->cl->SetFilter('modelid',array(5));
	}
	
	public function after_search_interview()
	{
		
	}
}