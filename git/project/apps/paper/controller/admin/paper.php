<?php
/**
 * 报纸管理
 *
 * @aca 报纸管理
 */
final class controller_admin_paper extends paper_controller_abstract
{
	private $paper,$edition,$page,$content, $pagesize = 10;

	function __construct(& $app)
	{
		parent::__construct($app);
		if (!license('paper')) cmstop::licenseFailure();
		$this->paper   = loader::model('admin/paper');
		$this->edition   = loader::model('admin/edition');
	}

    /**
     * 浏览
     *
     * @aca 浏览
     */
	function index()
	{
		$this->view->assign('head', array('title'=>'报纸'));
		$this->view->display("paper/index");
	}

    /**
     * 列表
     *
     * @aca 浏览
     */
	function page()
	{
		$data = $this->paper->getPapers();
		echo $this->json->encode($data);
	}

    /**
     * 保存
     *
     * @aca 保存
     */
	function save()
	{
		if ($this->is_post())
		{
			if ($id = $this->paper->save($_POST))
			{
				$_POST['paperid'] && $id = intval($_POST['paperid']);
				$result = array('state'=>true, 'data'=>$this->paper->getPaper($id));
			}
			else
			{
				$result = array('state'=>false, 'error'=>$this->paper->error());
			}
			exit($this->json->encode($result));
		}
		else
		{
			$paper = $this->paper->get(intval($_GET['id']));
			if(!$paper['template_content']) {
				$paper['template_content'] = 'paper/content.html';
			}
			$this->view->assign('paper', $paper);
		    $this->view->display('paper/form');
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
		$id = intval($_GET['id']);
		if(!$id) return ;
		$this->paper->delete($id);
		$this->encode(true);
	}
}