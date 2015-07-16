<?php
/**
 * 设置
 *
 * @aca whole 设置
 */
final class controller_admin_setting extends magazine_controller_abstract
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
            $_POST['setting']['path'] = $_POST['path'];
            unset($_POST['path']);
			$result = $setting->set_array($this->app->app, $_POST['setting']) ? array('state'=>true,'message'=>'保存成功') : array('state'=>false,'error'=>'保存失败');
			echo $this->json->encode($result);
		}
		else
		{
			$head = array('title'=>'杂志设置');

			$this->view->assign('head', $head);
			$this->view->assign('setting', $this->setting);
			$this->view->display('setting');
		}
	}
}