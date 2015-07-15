<?php

class plugin_paper extends object 
{
	private $model;
	
	public function __construct(& $model)
	{
		$this->model = $model;
		import('helper.folder');
	}
	
	//生成报纸的版面缓存
	public function after_save()
	{
		$pid = $this->model->pid;
		$pages = $this->model->pages;
		$file = ROOT_PATH."data/cache/table/paper/$pid.php";
		$tpl = array();
		$item = array
		(
			'name'	=>	'版面名称',
			'editor'=>	'主编',
			'arteditor'=>'美编'
		);
		if(file_exists($file)) 
		{
			$tpl = include($file);
			$count = max($pages, count($tpl));
			for($i=1; $i<=$count; $i++)
			{
				if($i > $pages)
				{
					unset($tpl[$i]);
					continue;
				}
				$tpl[$i] || $tpl[$i] = $item;
			}
		}
		else
		{
			for($i=1; $i<=$pages; $i++)
			{
				$tpl[$i] = $item;
			}
		}
		
		folder::create(dirname($file));
		write_file($file, "<?php\nreturn ".var_export($tpl, true).";");
		
		$this->updateIndex();
	}
	
	
	function before_delete()
	{
		$alias = table('paper', $this->model->id, 'alias');
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
	
	//更新报纸首页,生成报纸列表片段
	function updateIndex()
	{
		$html = loader::model('admin/html','paper');
		$html->paperIndex();
		$html->paper_select(1);
	}
}