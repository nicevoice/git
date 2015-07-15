<?php
/**
 * 顶踩设置
 *
 * @aca whole 顶踩设置
 */
class controller_admin_setting extends digg_controller_abstract
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
			$head = array('title'=>'Digg设置');

			$this->view->assign('head', $head);
			$this->view->assign('setting', $this->setting);
			$this->view->display('setting');
		}
	}
}