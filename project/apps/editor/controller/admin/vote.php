<?php
/**
 * 投票
 *
 * @aca public 投票
 */
class controller_admin_vote extends editor_controller_abstract
{
	private $vote;
	
	function __construct(& $app)
	{
		parent::__construct($app);
		$this->vote = loader::model('admin/vote','vote');
	}
	
	function index()
	{
		$this->view->display('vote');
	}
	
	function add()
	{
		if($contentid = $this->vote->add($_POST))
		{
			$code = $this->_voteCode($contentid);
			$this->view->assign('code',$code);
			$this->view->display('votecode');
		}
		else 
		{
			echo false;
		}
	}

	function page()
	{
		$catid = intval($_GET['catid']);
		$status = isset($_GET['status']) ? intval($_GET['status']) : 6;
		$page = isset($_GET['page']) ? intval($_GET['page']) : 0;
		$pagesize = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : $this->pagesize;
		$where = $_GET;
		$fields = '*';
		$order = isset($_GET['orderby']) ? str_replace('|', ' ', $_GET['orderby']) : ($status >= 5 ? '`published` DESC' : 'c.`contentid` DESC');
		$data = $this->vote->ls($where, $fields, $order, $page, $pagesize,true);
	    $result = array('total'=>$this->vote->total, 'data'=>$data);
		echo $this->json->encode($result);
	}
	
	function getVotecode()
	{
		$contentid = $_GET['contentid'];
		$code = $this->_voteCode($contentid);
		$this->view->assign('code',$code);
		$this->view->display('votecode');
	}
	
	function _voteCode($contentid)
	{
		$r = $this->vote->get($contentid);
		if (!$r) $this->showmessage($this->vote->error());
		
		$this->template->assign($r);
		$code = $this->template->fetch('vote/code.html','vote');
		return preg_replace("/<script\s*[^>]*>(.*?)<\/script>/isU", '', $code);
	}
	
}