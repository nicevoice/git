<?php

class plugin_edition extends object 
{
	private $model;
	
	public function __construct(& $model)
	{
		$this->model = $model;
		import('helper.folder');
	}
	
	function before_delete()
	{
		$e = table('magazine_edition', $this->model->id);
		$this->deleteHTML($e);
	}
	
	function after_delete()
	{
		
	}

	function deleteHTML($e)
	{
		$m = table('magazine', $e['mid']);
		if(!$m) return true;
		$dir = $this->model->html_root.DS.$m['alias'].DS.$e['year'].DS.$e['eid'];
		folder::delete($dir);
	}
	
	//根据缓存生成版面
	public function after_insert()
	{
		$eid = $this->model->eid;
		$mid = table('magazine_edition', $eid, 'mid');
		$file = CACHE_PATH."table/magazine/$mid.php";
		if(!is_file($file)) return;
		$data = include($file);
		$page = loader::model('admin/page','magazine');
		foreach ($data as $item)
		{
			$item['mid'] = $mid;
			$item['eid'] = $eid;
			$page->insert($item);
		}
	}
}