<?php
class plugin_html extends object 
{
	private $model;
	
	public function __construct(& $model)
	{
		$this->model = $model;
	}
	
	public function after_save()
	{
		$this->edtion_section();
	}
	
	public function after_delete()
	{
		$this->edtion_section();
	}
	
	private function edtion_section()
	{
		$pid = $this->model->pid;
		if(!$pid) return;
		$html = loader::model('admin/html','paper');
		$html->edtion_section($pid, 1);		//前台期片段更新
	}
}