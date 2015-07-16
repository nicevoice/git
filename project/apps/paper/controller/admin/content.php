<?php
/**
 * 版面内容管理
 *
 * @aca 版面内容管理
 */
final class controller_admin_content extends paper_controller_abstract
{
	private $paper, $edition, $page;
	private $disabled = array('未发布', '已发布', '休眠');

	function __construct(& $app)
	{
		parent::__construct($app);
		if (!license('paper')) cmstop::licenseFailure();
		$this->page = loader::model('admin/page');
		$this->content   = loader::model('admin/content');
		$this->html   = loader::model('admin/html');
	}

    /**
     * 热点选择的主界面
     *
     * @aca 浏览
     */
	function index () 
	{
		$page = $this->page->getPage(intval($_GET['id']));
		$total_number = table('paper_edition', $page['editionid'], 'total_number');
		$this->view->assign($page);
		$this->view->assign('total_number', $total_number);
		$this->view->assign('head', array('title' => $page['title']));
		
		//读取热点
		$coords = $this->content->getCoords(intval($_GET['id']));
		$this->view->assign('coords', $this->json->encode($coords));
		$this->view->display("content/index");
    }
	
	/**
     * 弹出框：关联文章
     *
     * @aca 关联文章
     */
	function relate () 
	{
		$this->view->assign('head', array('title'=>'关联文章'));
		$data = $this->content->select(intval($_GET['pid']));
		$this->view->assign('size', 20);
		$this->view->assign('contents', $data);
        $this->view->display("content/relate");
    }

    /**
     * 获取文章
     *
     * @aca 获取文章
     */
	function getArticle()
	{
		$data = $this->content->search($_GET);
		echo $this->json->encode($data);
	}
	
	/**
     * 保存一个热点
     *
     * @aca 保存热区
     */
	function saveMap()
	{
		if(!$_POST['pageid'] || !$_POST['contentid']) exit;
		$page = table('paper_edition_page', $_POST['pageid']);
		if(!$page) exit;
		$data = array_merge($_POST, $page);
		$mapid = $this->content->saveMap($data);
		exit("$mapid");
	}
	
	/**
     * 删除热点
     *
     * @aca 删除热区
     * @return bool
     */
    function delMap()
	{
		$id = intval($_GET['id']);
		if(!$id) return false;
		if($this->content->delMap($id))
		{
			echo 1;
		}
	}
	
	/**
     * 预览
     *
     * @aca 预览
     */
	function prevView()
	{
		$cid = intval($_GET['cid']);
		$pageid = intval($_GET['pageid']);
		if(!$cid || !$pageid) exit;
		$html = loader::model('admin/html');
		$html->content($cid, $pageid, 'prevView');
	}
}