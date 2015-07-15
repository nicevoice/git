<?php

class plugin_page extends object 
{
	private $model;
	
	public function __construct(& $model)
	{
		$this->model = $model;
		import('helper.folder');
	}
	
	//更新栏目缓存
	public function after_save()
	{
		$m = & $this->model;
		$eid = $m->eid;
		$db = factory::db();
		$sql = "SELECT * FROM #table_magazine_page WHERE eid = $eid";
		$data = $db->select($sql);
		if(!$data) return;
		foreach ($data AS & $v)
		{
			$mid || $mid = $v['mid'];
			unset($v['eid']);
			unset($v['pid']);
			unset($v['mid']);
		}
		$file = "../../data/cache/table/magazine/$mid.php";
		if(!is_dir(dirname($file))) 
		{
			import('helper.folder');
			folder::create(dirname($file));
		}
		write_file($file, "<?php\nreturn ".var_export($data, true).";");
	}
	public function after_insert()
	{
		$this->after_save();
	}
	public function after_delete()
	{
		$this->after_save();
	}
}