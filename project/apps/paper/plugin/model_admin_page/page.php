<?php

class plugin_page extends object 
{
	private $model;
	
	public function __construct(& $model)
	{
		$this->model = $model;
		import('helper.folder');
	}
	
	public function after_save()
	{
		$this->updateTpl();
	}
	
	public function after_add()
	{
		$this->updateTpl();
	}
	
	public function after_delete()
	{
		$this->updateTpl();
		$this->deleteHTML();
	}
	
	//根据版面id或期id，更新报纸的版面缓存
	private function updateTpl()
	{
		if($this->model->editionid) 
		{
			$editionid = $this->model->editionid;
			$paperid = table('paper_edition', $editionid, 'paperid');
		}
		else
		{
			$id = $this->model->pageid;
			$page = table('paper_edition_page', $id);
			$paperid = $page['paperid'];
			$editionid = $page['editionid'];
		}
		
		$file = ROOT_PATH."data/cache/table/paper/{$paperid}.php";
		if(!is_dir(dirname($file))) folder::create(dirname($file));
		$pages = $this->model->select("editionid = {$editionid}", 'name, editor, arteditor', "pageno ASC");
		write_file($file, "<?php\nreturn ".var_export($pages, true).";");
	}
	
	//删除某一版的前台文件
	private function deleteHTML($eid)
	{
		$paperid = $this->model->paperid;
		$eid = $this->model->editionid;
		$pageid = $this->model->pageid;
		
		
		$paper = table('paper', $paperid);
		$dir = $this->model->html_root."/{$paper['alias']}/$eid/$pageid/";
		folder::delete($dir);
	}
}