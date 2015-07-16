<?php
/**
 * 引用记录
 *
 * @aca whole 引用记录
 */
final class controller_admin_quote extends dms_controller_abstract
{
	private $quote;
	function __construct(& $app)
	{
		parent::__construct($app);
        if (!license('dms')) cmstop::licenseFailure();
		$this->quote	= loader::model('admin/dms_quote');
	}

	public function index()
	{
		$this->view->assign('head', array('title' => 'DMS:引用追踪'));
		$this->view->display('quote/index');
	}

	public function page()
	{
		$page	= intval(value($_GET, 'page'));
		$pagesize	= intval(value($_GET, 'pagesize'));
		$query = array();
		if (!empty($_GET['title']))
		{
			$query[] = 'dc.title LIKE "%'.value($_GET, 'title').'%"';
		}
		if (!empty($_GET['appid']))
		{
			$query[] = 'dq.appid = '.value($_GET, 'appid');
		}
		if (!empty($_GET['starttime']))
		{
			$query[] = 'dq.time > '.strtotime(value($_GET, 'starttime'));
		}
		if (!empty($_GET['endtime']))
		{
			$query[] = 'dq.time < '.strtotime(value($_GET, 'endtime'));
		}
		$data	= $this->quote->page($page, $pagesize, implode(' AND ', $query));
		exit($this->json->encode($data));
	}
}