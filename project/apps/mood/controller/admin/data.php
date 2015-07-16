<?php
/**
 * 数据查看
 *
 * @aca whole 数据查看
 */
class controller_admin_data extends mood_controller_abstract
{
	private $data, $mood, $pagesize = 10;

	function __construct(&$app)
	{
		parent::__construct($app);
	}

	function index()
	{
		$this->mood = loader::model('admin/mood');
		$ranksort = $this->mood->by_sort();
		
		$this->view->assign('head', array('title' => '心情排行'));
		$this->view->assign('rank', $ranksort);
		$this->view->display("data/index");
	}

	function page()
	{
		$this->data = loader::model('admin/data');
		
		$order = isset($_GET['orderby']) ? str_replace('|', ' ', $_GET['orderby']) : '`published` DESC';
		$page = max((isset($_GET['page']) ? intval($_GET['page']) : 1), 1);
		$pagesize = max((isset($_GET['pagesize']) ? intval($_GET['pagesize']) : $this->pagesize),1);
		
		$data = $this->data->ls($_GET, '*', $order, $page, $pagesize);
		$total = $this->data->total();
		echo $this->json->encode(array('data' => $data, 'total' => $total));
	}
}