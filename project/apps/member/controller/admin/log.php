<?php
/**
 * 登陆日志
 *
 * @aca 登陆日志
 */
class controller_admin_log extends member_controller_abstract
{
	private $login_log, $pagesize = 15;
	
	function __construct(& $app)
	{
		parent::__construct($app);
		$this->login_log = loader::model('member_login_log');
	}

    /**
     * 浏览
     *
     * @aca 浏览
     */
	function index()
	{
		$head = array('title'=>'登录日志');
		
		$this->view->assign('head', $head);
		$this->view->display('log/index');
	}

    /**
     * 列表
     *
     * @aca 浏览
     */
	function page()
	{
		$username = isset($_GET['username'])?$_GET['username']:null;
		$ip =isset($_GET['ip'])?$_GET['ip']:null;

        $success_fix = array(1 => '1', 2 => '0', 4 => null);
		$succeed = isset($_GET['succeed']) && array_key_exists($_GET['succeed'], $success_fix) ? $success_fix[$_GET['succeed']] : null;

        $starttime = isset($_GET['publish_d'])?$_GET['publish_d']:null;
		$endtime = isset($_GET['unpublish_d'])?$_GET['unpublish_d']:null;
		if($endtime < $starttime)
		{
			$endtime = null;
		}
		$page = max((isset($_GET['page']) ? intval($_GET['page']) : 1), 1);
		$size = max((isset($_GET['pagesize']) ? intval($_GET['pagesize']) : $this->pagesize),1);
		$order = isset($_GET['orderby']) ? str_replace('|', ' ', $_GET['orderby']) : '`logid` DESC';
		
		$data = $this->login_log->ls($username, $ip, $succeed, $starttime, $endtime, $order, $page, $size);
		$total = $this->login_log->total();
		
		echo $this->json->encode(array('data' => $data, 'total' =>intval($total)));
	}

    /**
     * 删除
     *
     * @aca 删除
     */
	function delete()
	{
		if($this->is_post())
		{
			$return = ($this->login_log->del($_POST))
					? array('state'=>true, 'info'=>'登录记录删除成功')
					: array('state'=>false, 'error'=>$this->login_log->error);
			echo $this->json->encode($return);
		}
		else
		{
			$this->view->display('log/delete');
		}
	}
}