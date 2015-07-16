<?php
/**
 * 设置
 *
 * @aca public 设置
 */
class controller_admin_setting extends editor_controller_abstract
{
	function __construct(& $app)
	{
		parent::__construct($app);
	}
	
	public function index()
	{
		if ($this->is_post())
		{
			$setting = new setting();
			$setting->set_array($this->app->app, $_POST['setting']);
			$result = array('state'=>true);
			echo $this->json->encode($result);
		}
		else
		{
			$head = array('title'=>'编辑器设置');

			$this->view->assign('head', $head);
			$this->view->assign('setting', $this->setting);
			$this->view->display('setting');
		}
	}
}