<?php
/**
 * 审核用户
 *
 * @aca whole 审核用户
 */
class controller_admin_audit extends member_controller_abstract
{
	private $audit, $pagesize = 15;

	function __construct(& $app)
	{
		parent::__construct($app);
		$this->audit = loader::model('member');
	}

	function index()
	{
		$head = array('title'=>'审核用户');
		$this->view->assign('head', $head);
		$this->view->display("audit/index");
	}

	function page()
	{
		$where = null;
		$where[] = 'groupid=4';
		switch ($_GET['date'])
		{
			case 'today':
				$regtime['created_min'] = date('Y-m-d H:i:s', strtotime('today'));
				break;
			case 'yesterday':
				$regtime['created_min'] = date('Y-m-d H:i:s', strtotime('yesterday'));
				$regtime['created_max'] = date('Y-m-d H:i:s', strtotime('today'));
				break;
			case 'week':
				$regtime['created_min'] = date('Y-m-d H:i:s', strtotime('last week'));
				break;
			case 'month':
				$regtime['created_min'] = date('Y-m-d H:i:s', strtotime('last month'));
				break;
		}
		if (isset($_GET['keywords']) && $_GET['keywords']) 	$where[] = where_keywords('username', $_GET['keywords']);
		if (isset($_GET['groupid']) && $_GET['groupid']) 	$where[] = "`groupid`='".$_GET['groupid']."'";
		if (isset($_GET['email']) && $_GET['email']) 		$where[] = "`email`='".$_GET['email']."'";
		if (isset($regtime['created_min']) && $regtime['created_min']) $where[] = where_mintime('regtime', $regtime['created_min']);
		if (isset($regtime['created_max']) && $regtime['created_max']) $where[] = where_maxtime('regtime', $regtime['created_max']);
		
		if (isset($_GET['userid']) && $_GET['userid'])
		{
			$where = null;
			$where[] = "`userid`='".$_GET['userid']."'";
		}
		
		if ($where) $where = implode(' AND ', $where);
		$fields = '*';
		$order = isset($_GET['orderby']) ? str_replace('|', ' ', $_GET['orderby']) : '`userid` DESC';
		
		$page = max((isset($_GET['page']) ? intval($_GET['page']) : 1), 1);
		$size = max((isset($_GET['pagesize']) ? intval($_GET['pagesize']) : $this->pagesize), 1);
		$data = $this->audit->page($where, $fields, $order, $page, $size);
		$total = $this->audit->count($where);
		echo $this->json->encode(array('data' => $data, 'total' => $total));
	}

	function audit()
	{
		if ($this->is_post())
		{
			$userid = $_POST['userid'];
			$return = array();
			if($this->audit->update(array('groupid'=>6), "`userid` IN ({$userid})"))
			{
				$return['state'] = true;
			}
			else 
			{
				$return = array('state'=>false, 'error' => $this->audit->error);
			}
			echo $this->json->encode($return);
		}
		else
		{
			$this->showmessage('不存在的操作');
		}
	}
}