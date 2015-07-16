<?php

class plugin_magazine extends object 
{
	private $model;
	
	public function __construct(& $model)
	{
		$this->model = $model;
		import('helper.folder');
	}
	
	function before_delete()
	{
		$alias = table('magazine', $this->model->id, 'alias');
		if($alias) 
		{
			$dir = $this->model->html_root."/$alias/";
			folder::delete($dir);
		}
		$this->updateIndex();
	}
	
	function after_delete()
	{
		$this->updateIndex();
	}
	
	function after_save()
	{
		$this->updateIndex();
	}
	
	//更新杂志首页
	function updateIndex()
	{
		$html = loader::model('admin/html','magazine');
		$html->index();
	}
}