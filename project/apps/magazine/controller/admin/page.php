<?php
/**
 * 栏目管理
 *
 * @aca 栏目管理
 */
final class controller_admin_page extends magazine_controller_abstract
{
	private $magazine,$edition;
	private $disabled = array('未发布', '已发布', '休眠');

	function __construct(& $app)
	{
		parent::__construct($app);
		if (!license('magazine')) cmstop::licenseFailure();
		$this->magazine   = loader::model('admin/magazine');
		$this->edition   = loader::model('admin/edition');
		$this->page   = loader::model('admin/page');
	}

    /**
     * 栏目管理
     *
     * @aca 浏览
     */
	function index()
	{
		$e = table('magazine_edition', intval($_GET['id']));
		$m = table('magazine', $e['mid']);
		$this->view->assign('page', $page);
		$this->view->assign(array('m' => $m, 'e' => $e));
		$this->view->assign('head', array('title' => "版面管理-{$m['name']}{$e['total_number']}期"));
		$this->view->display("page/index");
	}

    /**
     * 栏目列表
     *
     * @aca 浏览
     */
	function page()
	{
		$orderby = $_GET['orderby'] ? $_GET['orderby'] : "p.pageno ASC";
		$orderby = addslashes(str_replace('|', ' ', $orderby));
		$total = $this->page->count();
		$data = $this->page->getPages(intval($_GET['eid']), $orderby);
		$data = array('total' => $total, 'data' => $data);
		echo $this->json->encode($data);
	}
	
    /**
     * 行编辑模式
     *
     * @aca 保存
     */
	function save()
	{
		$this->page->save($_REQUEST);
		exit('1');
	}
	
	/**
     * 普通编辑模式
     *
     * @aca 添加
     */
	function add()
	{
		if ($this->is_post())
        {
        	$_POST['count'] = 0;
            if($_POST['pid'] = $this->page->insert($_REQUEST))
            {
            	$arr = array('state' => true, 'data' => $_POST);
            	echo $this->json->encode($arr);
            }
            else
            {
            	$arr = array('state' => false, 'error' => '添加失败');
            	echo $this->json->encode($arr);
            }
        }
        else
        {
        	$eid = intval($_GET['eid']);
        	$mid = table('magazine_edition', $eid, 'mid');
        	$sql = "SELECT max(pageno) AS max FROM #table_magazine_page WHERE eid = $eid";
        	$db = factory::db();
        	$rs = $db->get($sql);
        	$this->view->assign(array('pageno' => ++$rs['max'], 'eid' => $eid, 'mid' => $mid));
            $this->view->display('page/add');
        }
	}

    /**
     * 删除
     *
     * @aca 删除
     * @return mixed
     */
	function delete()
	{
		$id = $_GET['id'];
		if(!$id) return ;
		$this->page->delete($id);
		$result = array('state'=>true);
		exit($this->json->encode($result));
	}
	
	/**
     * 发布期
     *
     * @aca 发布
     * @return mixed
     */
	function publish()
	{
		$eid = intval($_GET['eid']);
		if(!$eid) return ;
		$html = loader::model('admin/html');
		$html->edition($eid);
		$rs = array('state' => true, 'total'=>$html->success);
		exit($this->json->encode($rs));
	}
}