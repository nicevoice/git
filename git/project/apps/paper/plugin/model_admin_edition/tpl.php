<?php
class plugin_tpl extends object 
{
	private $model;
	
	public function __construct(& $model)
	{
		$this->model = $model;
	}
	
	public function after_save()
	{
		if($this->model->mod == 'insert')
		{
			$this->defaultPage();
		}
	}
	
	//当创建期的时候，用版面模板数据生成默认的版面
	private function defaultPage()
	{
		$pid = $this->model->pid;
		if(!$pid) return;
		$eid = $this->model->eid;
		$file = ROOT_PATH.'data/cache/table/paper/'.$pid.'.php';
		
		
		if(is_file($file))
		{
			$tpl = include($file);
			foreach ($tpl AS $k => $r)
			{
				$values[] = "($pid, $eid, $k, '{$r['name']}', '{$r['editor']}', '{$r['arteditor']}', '', '')";
			}
			if($values)
			{
				$sql = "INSERT INTO #table_paper_edition_page (paperid, editionid, pageno, name, editor, arteditor, image, pdf) VALUES "
						.implode(',', $values).';';
				$db = & factory::db();
				$db->exec($sql);
			}
		}
	}
}