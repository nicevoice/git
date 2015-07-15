<?php
/**
 * 搜索设置
 *
 * @aca whole 搜索设置
 */
class controller_admin_setting extends search_controller_abstract
{
	function __construct(&$app)
	{
		parent::__construct($app);
	}

	function index()
	{
		$this->setting = new setting();
		
		if($this->is_post())
		{
			if(!isset($_POST['setting']['open']))
				$_POST['setting']['open'] = 0;
			$return = $this->setting->set_array('search',$_POST['setting'])?array('state' =>true,'message'=>'保存成功'):array('state' =>false,'message'=>'保存失败');
			echo $this->json->encode($return);exit;
		}
		
		$st = $this->setting->get('search');
		$head = array('title'=>'搜索设置');
		
		$this->view->assign('head', $head);
		$this->view->assign('setting', $st);
		$this->view->display('setting');
	}
	
	function test()
	{
		if(!is_numeric($_POST['port']))
		{
			$return = array('state' => false,'message' => '端口号错误');
		}
		else
		{
			$fp = @fsockopen($_POST['host'], $_POST['port'], $errno, $errstr, 2);
			if(!$fp)
			{
				$errstr = trim($errstr);
				$return = array('state' => false, 'error' =>"连接 {$host}:{$port} 服务器失败 (errno=$errno, msg=$errstr)");
			}
			else
			{
				$return = array('state' => true, 'message' => "恭喜，服务器连接成功!");
			}
		}
		echo $this->json->encode($return);
	}
}