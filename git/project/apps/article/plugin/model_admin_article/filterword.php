<?php

class plugin_filterword extends object 
{
	private $article;
	
	public function __construct(& $article)
	{
		$this->article = $article;
	}
	
	public function before_add()
	{
		if(isset($_GET['ignoreword']) && $_GET['ignoreword']) return true;
		$this->filter();
		$this->filterimg();
	}
	
	public function before_edit()
	{
		if(isset($_GET['ignoreword']) && $_GET['ignoreword']) return true;
		$this->filter();
	}
	
	private function filter()
	{
		$this->article->filterword = array();
		
		$words = table('filterword');
		foreach ($words as $w)
		{
			if(strpos($this->article->data['content'], $w['pattern']) !== false)
			{
				$this->article->filterword[] = $w['pattern'];
			}
		}
	}
	
	private function filterimg()
	{
		$this->article->data['content'] = preg_replace_callback('/(<img\s+[^>]*src\s*=\s*")([^>"\']+)("[^>]*[\/]?>)/isU',array($this, 'do_replace'),$this->article->data['content']);
	}
	
	private function do_replace($matches)
	{
		return  $matches[1].preg_replace('/(http:\/\/[^\?]+)\?[\d\.]+$/isU',"$1",$matches[2]).$matches[3];
	}
}