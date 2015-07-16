<?php
/**
 * Wap 设置
 *
 * @aca whole Wap 设置
 */
class controller_admin_setting extends wap_controller_abstract
{

	function __construct(& $app)
	{
		parent::__construct($app);
	}
	
	function index()
	{
		$setting = new setting();
		if ($this->is_post())
		{
			$_POST['setting']['catids'] = (array) id_format($_POST['setting']['catids']);
			$result = $setting->set_array('wap', $_POST['setting']) ? array('state'=>true,'data'=>'保存成功') : array('state'=>false,'error'=>'保存失败');
			echo $this->json->encode($result);
		}
		else 
		{
			$head['title'] = 'WAP设置';
			$this->view->assign('head', $head);
			$this->view->assign('setting', $this->setting);
			$this->view->assign('catids', implode_ids(array_values($this->setting['catids'])));
			$this->view->display("setting");
		}
	}
}