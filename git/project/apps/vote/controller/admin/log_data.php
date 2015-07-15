<?php
/**
 * 投票结果记录
 *
 * @aca whole 投票结果记录
 */
class controller_admin_log_data extends vote_controller_abstract
{
	private $log_data, $option, $pagesize = 15;
	
	function __construct(& $app)
	{
		parent::__construct($app);
		$this->option = loader::model('admin/option');
		$this->log_data = loader::model('admin/log_data');
	}
	
	function index()
	{
		$optionid = $_GET['optionid'];
		
		$r = $this->option->get($optionid);
		if (!$r) $this->showmessage($this->option->error());

		$this->view->assign($r);
		$this->view->assign('total', $this->log_data->total($contentid));
		$this->view->assign('head', array('title'=>'投票记录：'.$r['name']));
		$this->view->display('log_data');
	}
	
	function page()
	{
		$optionid = $_GET['optionid'];
		$page = isset($_GET['page']) ? intval($_GET['page']) : 0;
		$pagesize = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : $this->pagesize;

		$data = $this->log_data->ls($optionid, $page, $pagesize);
		$total = $this->log_data->total($optionid);

		$result = array('total'=>$total, 'data'=>$data);
		echo $this->json->encode($result);
	}
}