<?php
/**
 * 设置
 *
 * @aca whole 设置
 */
final class controller_admin_setting extends dms_controller_abstract
{

	function __construct(&$app)
	{
		parent::__construct($app);
		if (!license('dms')) cmstop::licenseFailure();
	}

    // 数据中心配置
	public function index()
	{
        $setting = new setting();

		if ($this->is_post())
		{
			$setting->set_array($this->app->app, $_POST['setting']);
			$result = array('state'=>true);
			echo $this->json->encode($result);
		}
		else
		{
			$head = array('title'=>'DMS:基础设置');
			$this->view->assign('head', $head);
			$this->view->assign('setting', $setting->get($this->app->app));
			$this->view->display('setting');
		}
	}

    // 搜索设置检查
    public function search_ping()
    {
        $host = trim(value($_POST, 'host'));
        $port = value($_POST, 'port');

        if(!is_numeric($port))
		{
			$return = array('state' => false, 'error' => '端口号错误');
		}
		else
		{
			$fp = @fsockopen($host, $port, $errno, $errstr, 2);
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