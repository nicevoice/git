<?php
/**
 * RSS 设置
 *
 * @aca whole RSS 设置
 */
class controller_admin_setting extends rss_controller_abstract
{
	function __construct(& $app)
	{
		parent::__construct($app);
	}
	
	function index()
	{
		$setting = new setting();
		if($this->is_post())
		{
			$data = $_POST['setting'];
			$data['weight']['min'] = intval($data['weight']['min']);
			$data['weight']['max'] = intval($data['weight']['max']);
			$data['size'] = intval($data['size']);
			if ($setting->set_array('rss', $data))
			{
				$json = array('state'=>true,'info'=>'保存成功');
			}
			else
			{
				$json = array('state'=>false,'error'=>'保存失败');
			}
			exit ($this->json->encode($json));
		}
		else
		{
			$data = $setting->get('rss');
			$this->view->assign($data);
			
			$categorys = $this->json->encode($data['category']);
			$this->view->assign('categorys', $categorys);
			$this->view->assign('head', array('title'=>'RSS设置'));
			$this->view->display('setting');
		}
	}
}