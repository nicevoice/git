<?php
/**
 * 标签管理
 *
 * @aca whole 标签管理
 */
final class controller_admin_tag extends dms_controller_abstract
{
	private $tag;
	function __construct(& $app)
	{
		parent::__construct($app);
        if (!license('dms')) cmstop::licenseFailure();
		$this->view->assign('head', array('title' => 'DMS:标签'));
		$this->tag	= loader::model('admin/dms_tag');
	}

	public function index()
	{
		$this->view->display('tag/index');
	}

	public function page()
	{
		$total = $this->tag->count($_GET);
		$data = $this->tag->page($_GET);
		$result = array('total'=>$total, 'data'=>$data);
		echo $this->json->encode($result);
	}
}