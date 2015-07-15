<?php
/**
 * 版面管理
 *
 * @aca 版面管理
 */
final class controller_admin_page extends paper_controller_abstract
{
	private $paper,$edition,$page,$content,$pagesize = 15;
	private $disabled = array('未发布', '已发布', '休　眠');

	function __construct(& $app)
	{
		parent::__construct($app);
		if (!license('paper')) cmstop::licenseFailure();
		$this->paper   = loader::model('admin/paper');
		$this->edition   = loader::model('admin/edition');
		$this->content = loader::model('admin/content');
		$this->page   = loader::model('admin/page');
		$this->html   = loader::model('admin/html');
	}

    /**
     * 浏览
     *
     * @aca 浏览
     */
	function index()
	{
		$edition = table('paper_edition', intval($_GET['id']));
		$paper = table('paper', $edition['paperid']);
		$title = $paper['name'].'总第'.$edition['total_number'].'期';
		$this->view->assign('edition', $edition);
		$this->view->assign('paper', $paper);
		$this->view->assign('disabled', $this->disabled[$edition['disabled']]);
		$this->view->assign('head', array('title' => $title));
		$this->view->display("page/index");
	}

    /**
     * 列表
     *
     * @aca 浏览
     */
	function page()
	{
		$data = $this->page->getPages(intval($_GET['id']));
		$this->view->assign('data', $data);
		echo $this->json->encode($data);
	}

    /**
     * 保存
     *
     * @aca 保存
     */
	function save()
	{
		if(!$_POST['id'] && !$_POST['k'] && !$_POST['v']) exit;
		$_POST = addslashes_deep($_POST);
		if(!$this->page->save($_POST['id'], $_POST['k'], $_POST['v']))
		{
			exit('0');
		}
		exit('1');
	}
	
	/**
     * 添加版面
     *
     * @aca 添加
     */
	function add()
	{
		$eid = intval($_GET['eid']);
		$data = $this->page->add($eid);
		$rs = array('state' => true, 'data' => $data);
		exit($this->json->encode($rs));
	}
	
	/**
     * 删除版面
     *
     * @aca 删除
     * @return mixed
     */
	function delete()
	{
		$pageid = intval($_GET['pageid']);
		if(!$pageid) return ;
		$this->page->delete($pageid);
		$this->encode(true);
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
		if($html->success > 0)
		{
			$rs = array('state' => true, 'total'=>$html->success);
		}
		else
		{
			$rs = array('state' => false, 'error' => '没有文章发布');
		}
		// 生成之前的期号
		$oldeid	= array_pop($this->edition->get('paperid='.$this->edition->get_field('paperid', $eid).' and editionid<'.$eid, 'editionid', 'editionid desc'));
		$html->edition($oldeid);
		exit($this->json->encode($rs));
	}

    /**
     * 休眠
     *
     * @aca 休眠
     * @return mixed
     */
	function sleep()
	{
		$eid = intval($_GET['eid']);
		if(!$eid) return ;
		$html = loader::model('admin/html');
		$html->delEdition($eid);
		$rs = array('state' => true);
		exit($this->json->encode($rs));
	}

    /**
     * 下线
     *
     * @aca 下线
     */
	function unpublish()
	{
		$eid = intval($_GET['eid']);
		$this->edition->set_field('disabled', 0, $eid);
		$this->encode(true);
	}
}