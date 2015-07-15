<?php
/**
 * 延伸阅读
 *
 * @aca public 延伸阅读
 */
class controller_admin_moreread extends editor_controller_abstract
{
	function __construct(& $app)
	{
		parent::__construct($app);
	}
	
	function index()
	{
		$this->view->display('moreread');
	}
	function search()
	{
		$page = isset($_GET['page']) ? intval($_GET['page']) :0;
		$pagesize = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : 20;
		$_POST['status'] = 6;
		$where = $_POST;
		$fields = '*';
		$order = '`published` DESC';
		$content = loader::model('admin/content','system');
		$data = $content->ls($where, $fields, $order, $page, $pagesize);
		
		$result = array('total'=>$content->total, 'data'=>$data);
		exit($this->json->encode($result));
	}
}