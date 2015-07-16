<?php
class plugin_html extends object 
{
	private $model;
	
	public function __construct(& $model)
	{
		$this->model = $model;
		import('helper.folder');
	}

	function after_delete()
	{
		$mid = $this->model->mid;
		$m = table('magazine', $mid);
		
		$html = loader::model('admin/html','magazine');
		$html->elist1($m, $this->model->year);
	}

	//更新期列表片段
	function updateSection($e)
	{
		$m = table('magazine', $e['mid']);
		if(!$m) return true;
		$dir = $this->model->html_root.DS.$m['alias'].DS.$e['year'].DS.$e['eid'];
		folder::delete($dir);
	}
}