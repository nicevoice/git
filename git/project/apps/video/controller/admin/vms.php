<?php
/**
 * 视频管理系统
 *
 * @aca 视频管理系统
 */
class controller_admin_vms extends video_controller_abstract
{
	private $vms;

	function __construct(& $app)
	{
		parent::__construct($app);
		$this->vms = loader::model('admin/vms');
	}

    /**
     * 视频管理
     *
     * @aca 浏览
     */
    public function index()
    {
		if(!$this->setting['openserver'])
		{
			$this->showmessage('视频服务器功能已关闭');
		}
		$manage = $_GET['selector'] ? 0 : 1;
    	$this->view->assign('head', array('title'=>$_GET['selector'] ? '选择视频' : '管理视频'));
    	$url = $this->vms->api_url;
    	$url = $url . (strpos($url, '?') ? '&' : '?') . 'do=upload';
    	$this->view->assign('upurl', $url);
    	$this->view->assign('playerurl', $this->setting['player']);
    	$this->view->assign('filetype', $this->setting['filetype']);
    	$this->view->assign('apikey', $this->vms->api_key);
		if($manage)
		{
			$this->view->display('vms/file');
		}
		else
		{
			$this->view->display('vms/select');
		}
    }

    /**
     * 查看视频
     *
     * @aca 浏览
     */
	public function view()
	{
		$vid = intval($_GET['vid']);
		$this->view->assign('vid' ,$vid);
		$this->view->assign('head', array('title'=>'查看视频'));
		$this->view->display('vms/view');
	}

    /**
     * 预览
     *
     * @aca 预览
     */
	public function preview()
	{
		$vid = intval($_GET['vid']);
		$this->view->assign('vid', $vid);		
		$this->view->assign('playerurl', $this->setting['player']);
		$this->view->assign('head', array('title'=>'预览视频'));
		$this->view->display('vms/preview');
	}

    /**
     * 编辑
     *
     * @aca 编辑
     */
	public function edit()
	{
		$vid = intval($_GET['vid']);
		$this->view->assign('vid', $vid);
		$this->view->assign('head', array('title'=>'编辑视频'));
		$this->view->display('vms/edit');
		
	}

    /**
     * 设置
     *
     * @aca 接口配置
     */
	public function setting()
	{
		if ($this->is_post())
		{
			$setting = new setting();
			$result = $setting->set_array($this->app->app, $_POST['setting']) ? array('state'=>true,'message'=>'保存成功') : array('state'=>false,'error'=>'保存失败');
			echo $this->json->encode($result);
		}
		else
		{
			$head = array('title'=>'接口设置');
			$this->view->assign('head', $head);
			$this->view->assign('setting', $this->setting);
			$this->view->display('vms/setting');
		}
	}
    
    public function __call($do, $args)
    {
		if(in_array($do, array('ls', 'info', 'info_by_file', 'setinfo', 'delete'), true)) 
		{
			if($result = $this->vms->$do($args))
			{
				exit($result);
			}
		}
		exit;
    }
}