<?php
class plugin_url extends object 
{
	private $model;
	
	public function __construct(& $model)
	{
		$this->model = $model;
	}
	
	public function after_save()
	{
		$mapid = $this->model->mapid;
		$eid = table('paper_content', $mapid, 'editionid');
		$this->updateUrl($eid);
	}
	
	public function after_delete()
	{
		$eid = $this->model->eid;
		$this->updateUrl($eid);
	}
	
	private function updateUrl($eid)
	{
		if(!$eid) return;
		$html = loader::model('admin/html','paper');
		$html->updateUrl($eid);
	}
}