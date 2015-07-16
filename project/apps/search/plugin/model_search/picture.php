<?php

class plugin_picture extends object 
{
	private $search;
	
	public function __construct(& $search)
	{
		$this->search = $search;
	}
	
	public function before_search_picture()
	{
		$this->search->cl->SetFilter('modelid',array(2));
	}
}