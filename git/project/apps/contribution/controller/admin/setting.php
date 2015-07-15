<?php
/**
 * 投稿设置
 *
 * @aca whole 投稿设置
 */
class controller_admin_setting extends contribution_controller_abstract
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
			$result = $setting->set_array('contribution',$_POST['setting']) 
					? array('state'=>true,'message'=>'保存成功') 
					: array('state'=>false,'error'=>'保存失败');
			echo $this->json->encode($result);
		}
		else
		{
			$head = array('title'=>'投稿设置');

			$this->view->assign('head', $head);
			$this->view->assign('setting', $this->setting);
			$this->view->display('setting');
		}
	}
}