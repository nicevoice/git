<?php

class plugin_article extends object 
{
	private $search;
	
	public function __construct(& $search)
	{
		$this->search = $search;
	}
	
	public function before_search_article()
	{
		$this->search->cl->SetFilter('modelid',array(1));
	}
	
	public function after_search_article()
	{
		
	}
}