<?php
/**
 * 栏目内容管理
 *
 * @aca 栏目内容管理
 */
final class controller_admin_content extends magazine_controller_abstract
{
	private $magazine, $edition, $page;
	private $disabled = array('未发布', '已发布', '休眠');

	function __construct(& $app)
	{
		parent::__construct($app);
		if (!license('magazine')) cmstop::licenseFailure();
		$this->content = loader::model('admin/content');
	}

    /**
     * 浏览
     *
     * @aca 浏览
     */
	function index()
	{
		$p = table('magazine_page', intval($_GET['pid']));
		$m = table('magazine', $p['mid']);
		$e = table('magazine_edition', $p['eid']);
		$this->view->assign(array('m' => $m, 'e' => $e, 'p' => $p));
		$this->view->assign('head', array('title' => "{$p['name']}-{$m['name']}-{$e['year']}-{$e['number']}期"));
		$this->view->display("content/index");
	}

    /**
     * 列表
     *
     * @aca 浏览
     */
	function page()
	{
		$orderby = $_GET['orderby'] ? $_GET['orderby'] : "sort ASC";
		$orderby = addslashes(str_replace('|', ' ', $orderby));
		$total = $this->content->count();
		$data = $this->content->getContents(intval($_GET['pid']), $orderby);
		$data = array('total' => $total, 'data' => $data);
		echo $this->json->encode($data);
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
		$this->content->delete($id, intval($_GET['pid']));	
		$result = array('state'=>true);
		exit($this->json->encode($result));
	}
	
	/**
     * 关联文章
     *
     * @aca 关联文章
     */
	function relate () 
	{
		$this->view->assign('head', array('title'=>'关联文章'));
		$data = $this->content->getContents(intval($_GET['pid']));
		$this->view->assign('size', 15);
		$this->view->assign('contents', $data);
        $this->view->display("content/relate");
    }

    /**
     * 获取文章列表
     *
     * @aca 关联文章
     */
	function getArticle()
	{
		$data = $this->content->search($_POST);
		echo $this->json->encode($data);
	}

    /**
     * 保存相关文章
     *
     * @aca 关联文章
     * @return mixed
     */
	function saveRelate()
	{
		$pid = intval($_POST['pid']);
		if(!$pid) return;
		$this->content->saveRelate($pid, $_POST['ids']);
		exit('1');
	}
}