<?php
/**
 * 投票记录
 *
 * @aca whole 投票记录
 */
class controller_admin_log extends vote_controller_abstract
{
	private $vote, $log, $pagesize = 15;
	
	function __construct(& $app)
	{
		parent::__construct($app);
		$this->vote = loader::model('admin/vote');
		$this->log = loader::model('admin/vote_log');
	}
	
	function index()
	{
		$contentid = $_GET['contentid'];
		
		$r = $this->vote->get($contentid);
		if (!$r) $this->showmessage($this->vote->error());

		$this->priv_category($r['catid']);
		
		$this->view->assign($r);
		$this->view->assign('people', $this->log->total($contentid));
		$this->view->assign('head', array('title'=>'投票记录：'.$r['title']));
		$this->view->display('log');
	}
	
	function page()
	{
		$this->priv_category(table('content', $_GET['contentid'], 'catid'));
		
		$contentid = $_GET['contentid'];
		$page = isset($_GET['page']) ? intval($_GET['page']) : 0;
		$pagesize = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : $this->pagesize;

		$data = $this->log->ls($contentid, $page, $pagesize);
		$total = $this->log->total($contentid);

		$result = array('total'=>$total, 'data'=>$data);
		echo $this->json->encode($result);
	}
}