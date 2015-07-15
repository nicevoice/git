<?php
/**
 * 顶踩管理
 *
 * @aca whole 顶踩管理
 */
class controller_admin_digg extends digg_controller_abstract
{
	private $digg, $pagesize = 15;

	function __construct(& $app)
	{
		parent::__construct($app);
		$this->digg = loader::model('admin/digg');
	}

	function index()
	{
		$this->view->assign('head', array('title'=>'顶踩排行'));
		$this->view->display("index");
	}

	function page()
	{
		$order = isset($_GET['orderby']) ? str_replace('|', ' ', $_GET['orderby']) : '`published` DESC';
		$page = max((isset($_GET['page']) ? intval($_GET['page']) : 1), 1);
		$pagesize = empty($_GET['pagesize']) ? $this->pagesize : $_GET['pagesize'];
		
		$data = $this->digg->ls($_GET, $order, $page, $pagesize);
		$total = $this->digg->total();
		$result = array('data' => $data, 'total' => $total);
		
		echo $this->json->encode($result);
	}
	
	function publish()
	{
		$channels = channel();
		foreach ($channels as $value)
		{
			$this->_publish($value['alias'], 'list', $channels, $value['name'], $value['childids']);
		}
		$ismake = $this->_publish('index', 'index', $channels, '顶帖排行');
		
		if ($ismake)
		{
			$return['state'] = true;
			$return['message'] .= '排行已生成'.$_GET['cron'];
		}
		else
		{
			$return['state'] = false;
			$return['error'] = '错误';
		}
		echo $this->json->encode($return);
	}

	function _publish($bulid_path, $tpl_path, $channels, $name, $childids)
	{
		$page_file = WWW_PATH."digg/$bulid_path".SHTML;
		$this->template->assign('channel', $bulid_path);
		$this->template->assign('name', $name);
		$this->template->assign('category', $channels);
		$this->template->assign('childids', $childids);
		$html = $this->template->fetch("digg/$tpl_path.html");
		import('helper.folder');
		folder::create(dirname($page_file));
		write_file($page_file,$html);
		return true;
	}
	
	function view()
	{
		$modelid = $_GET['modelid'];
		$contentid = $_GET['contentid'];
		$alias = table('model', $modelid, 'alias');
		$url = ADMIN_URL.'?app='.$alias.'&controller='.$alias.'&action=view&contentid='.$contentid;
		$this->redirect($url);
	}
}