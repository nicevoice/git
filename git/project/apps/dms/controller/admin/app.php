<?php
/**
 * 应用管理
 *
 * @aca 应用管理
 */
final class controller_admin_app extends dms_controller_abstract
{
	private $apps;
	function __construct(& $app)
	{
		parent::__construct($app);
        if (!license('dms')) cmstop::licenseFailure();
		$this->apps	= loader::model('admin/dms_app');
	}

    /**
     * 应用管理
     *
     * @aca 应用管理
     */
	public function index()
	{
		$this->view->assign('head', array('title' => 'DMS:应用管理'));
		$this->view->display('app/index');
	}

    /**
     * 应用列表
     *
     * @aca 应用管理
     */
	public function page()
	{
		$where = null;
		$total = $this->apps->count($where);
		$data = $this->apps->page($where);
		$result = array('total'=>$total, 'data'=>$data);
		echo $this->json->encode($result);
	}

    /**
     * 添加应用
     *
     * @aca 添加应用
     */
	public function add()
	{
		if ($this->is_post())
		{
			if ($this->apps->add($_POST))
			{
				exit('{"state":"true"}');
			}
			else
			{
				exit($this->json->encode(array('state'=>false, 'error'=>'添加失败')));
			}
		}
		else
		{
			$this->view->display('app/add');
		}
	}

    /**
     * 编辑应用
     *
     * @aca 编辑应用
     */
	public function edit()
	{
		$id	= intval($_GET['id']);
		if ($id < 1)
		{
			exit($this->json->encode(array('state'=>false, 'error'=>'id不存在')));
		}
		if ($this->is_post())
		{
			if ($this->apps->edit($_POST, $id))
			{
				exit('{"state":"true"}');
			}
			else
			{
				exit($this->json->encode(array('state'=>false, 'error'=>'添加失败')));
			}
		}
		else
		{
			if (!$data = $this->apps->get($id))
			{
				exit($this->json->encode(array('state'=>true, 'error'=>'app不存在')));
			}
			$this->view->assign($data);
			$this->view->display('app/edit');
		}
	}

    /**
     * 删除应用
     *
     * @aca 删除应用
     */
	public function delete()
	{
		$id	= intval($_GET['id']);
		if ($id < 1)
		{
			exit($this->json->encode(array('state'=>false, 'error'=>'id不存在')));
		}
		if ($this->apps->delete("appid=$id"))
		{
			exit('{"state":"true"}');
		}
		else
		{
			exit($this->json->encode(array('state'=>false, 'error'=>'删除失败')));
		}
	}

	/**
     * 从配置文件中读取API列表
     *
     * @aca 读取 API 列表
     */
	public function get_api_list()
	{
		$appid	= intval($_GET['appid']);
		$api	= Loader::import('config.api');
		$data	= array();
		$index	= 1;
		if ($appid)
		{
			$appdata	= $this->apps->get($appid);
		}
		foreach ($api as $group)
		{
			$gid	= $index;
			$data[$index] = array('id' => $index, 'parentid' => 0, 'name' => $group['group'], 'api' => '');
			$index++;
			$all_checked	= true;
			foreach ($group['data'] as $key => $item)
			{
				$checked	= $appid ? in_array($item['api'], $appdata['priv']) : $item['default_priv'];
				$all_checked	&= $checked;
				$data[]	= array('id' => $index, 'parentid' => $gid, 'name' => $item['name'], 'api' => $item['api'], 'checked' => $checked);
				$index++;
			}
			$data[$gid]['checked']	= $all_checked;
		}
		exit($this->json->encode($data));
	}
}