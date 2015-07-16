<?php
/**
 * 授权管理
 *
 * @aca 授权管理
 */
final class controller_admin_priv extends dms_controller_abstract
{
	private $apps;
	function __construct(& $app)
	{
		parent::__construct($app);
        if (!license('dms')) cmstop::licenseFailure();
		$this->apps	= loader::model('admin/dms_app');
		$this->priv	= loader::model('admin/dms_priv');
	}

    /**
     * 授权管理
     *
     * @aca 授权管理
     */
	public function index()
	{
		$app_data	= $this->apps->select(null, 'appid, name');
		$this->view->assign('app_data', $app_data);
		$this->view->assign('head', array('title' => 'DMS:授权管理'));
		$this->view->display('priv/index');
	}

    /**
     * 授权列表
     *
     * @aca 授权管理
     */
	public function page()
	{
		$appid = $_GET['id'];
		if (empty($appid))
		{
			exit($this->json->encode(array('state'=>false, 'data'=>'')));
		}
		$app_data	= $this->apps->select("appid != $appid", 'appid, name');
		foreach ($this->priv->select("source=$appid AND target != $appid", 'target, priv') as $item)
		{
			$priv_data[$item['target']] = $item['priv'];
		}
		$data		= array();
		foreach ($app_data as $item)
		{
			$priv		= $priv_data[$item['appid']];
			$priv		= $priv ? $priv : 0;
			$item['r']	= ($priv & 1) ? 1 : 0;
			$item['e']	= ($priv & 2) ? 1 : 0;
			$item['d']	= ($priv & 4) ? 1 : 0;
			$data[]		= $item;
		}
		exit($this->json->encode(array('state'=>true, 'data'=>$data)));
	}

    /**
     * 启用授权
     *
     * @aca 启用
     */
	public function set_enable()
	{
		$source	= intval($_GET['source']);
		$target	= intval($_GET['target']);
		$priv	= intval($_GET['priv']);
		if ($this->priv->set_enable($source, $target, $priv))
		{
			exit($this->json->encode(array('state'=>'true', 'data'=>'操作成功')));
		}
		else
		{
			exit($this->json->encode(array('state'=>'false', 'error'=>'操作失败')));
		}
	}

    /**
     * 禁用授权
     *
     * @aca 禁用
     */
	public function set_disable()
	{
		$source	= intval($_GET['source']);
		$target	= intval($_GET['target']);
		$priv	= intval($_GET['priv']);
		if ($this->priv->set_disable($source, $target, $priv))
		{
			exit($this->json->encode(array('state'=>'true', 'data'=>'操作成功')));
		}
		else
		{
			exit($this->json->encode(array('state'=>'false', 'error'=>'操作失败')));
		}
	}
}