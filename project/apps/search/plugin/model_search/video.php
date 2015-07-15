<?php

class plugin_video extends object 
{
	private $search;
	
	public function __construct(& $search)
	{
		$this->search = $search;
	}

	public function after_search_video()
	{

	}
	
	public function before_search_video()
	{
		$this->search->cl->SetFilter('modelid',array(4));
	}
	
	public function before_search_mobile()
	{
		$this->search->cl->SetFilter('ismobile',array(1));
	}
}