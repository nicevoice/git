<?php

class plugin_special extends object 
{
	private $search;
	
	public function __construct(& $search)
	{
		$this->search = $search;
	}
	
	public function before_search_special()
	{
		$this->search->cl->SetFilter('modelid',array(10));
	}
	
	public function after_search_special()
	{
		
	}
}