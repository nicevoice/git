<?php

class plugin_all extends object 
{
	private $search;
	
	public function __construct(& $search)
	{
		$this->search = $search;
	}
	
	public function before_search_all()
	{
		//$this->search->cl->SetFilter('modelid',array(1,2,5,10)); //搜索的模型ID为 1,2,5,10 同时可以做搜索前的过滤
	}
	
	public function after_search_all()
	{
		//可以自定义一些返回的不同数据 操作的数据位$this->search->data (数组格式)
		
	}
}