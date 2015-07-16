<?php
class controller_index extends digg_controller_abstract
{
	private $content;

	function __construct(& $app)
	{
		parent::__construct($app);
	}

	function index()
	{
		$catid = intval($_GET['catid']);
        if ($this->system['pagecached'])
		{
			$keyid = md5('pagecached_digg_index_index_' .$catid);
			cmstop::cache_start($this->system['pagecachettl'], $keyid);
		}
		
		if($catid)
		{
			$this->template->assign('childids', table('category', $catid, 'childids'));
			$this->template->assign('alias', table('category', $catid, 'alias'));
			$this->template->assign('name', table('category', $catid, 'name'));
			$this->template->display('digg/list.html');
		}
		else 
		{
			$this->template->display('digg/index.html');
		}
		
		if ($this->system['pagecached']) cmstop::cache_end();
	}
}