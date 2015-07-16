<?php
/**
 * 自由列表页面 -- 分组管理
 *
 * @aca 分组管理
 */
class controller_admin_group extends freelist_controller_abstract
{
	private $freelist_group;

	public function __construct(& $app)
	{
		parent::__construct($app);
		$this->freelist_group = loader::model('admin/freelist_group');
	}

    /**
     * 分组管理
     *
     * @aca 浏览
     */
	public function index()
	{
		$this->view->assign('head', array('title'=>'分组管理'));
		$this->view->display("freelist_group/index");
	}

    /**
     * 分组列表
     *
     * @aca 浏览
     */
	public function page()
	{
		$data = $this->freelist_group->select();
		$total = $this->freelist_group->count();
		echo $this->json->encode(array('total'=>$total,'data'=>$data));
	}

    /**
     * 添加分组
     *
     * @aca 添加
     */
	function add()
	{
		if ($this->is_post())
		{
			if ($gid = $this->freelist_group->add($_POST))
			{
				$json = array(
					'state'=>true, 
					'data'=>$this->freelist_group->get($gid)
				);
			}
			else
			{
				$json = array(
					'state'=>false, 
					'error'=>$this->freelist_group->error()
				);
			}
			exit($this->json->encode($json));
		}
		else
		{
			$this->view->display('freelist_group/add');
		}
	}

	/**
     * 编辑分组
     *
     * @aca 编辑
     */
	public function edit()
	{
		$gid = intval($_GET['gid']);
		if ($this->is_post())
		{
			if($gid = $this->freelist_group->edit($_POST))
			{
				$json = array(
					'state' =>true,
					'message' => '修改成功',
					'data' => $this->freelist_group->get($gid)
				);
			}
			else
			{
				$json = array(
					'state' =>false,
					'error' => $this->freelist_group->error()
				);
			}
			echo $this->json->encode($json);
		}
		else
		{
			$group = $this->freelist_group->get($gid);
			$this->view->assign('head', array('title'=>'编辑分组'));
			$this->view->assign('group', $group);
			$this->view->display('freelist_group/edit');
		}
	}

	/**
     * 删除分组
     *
     * @aca 删除
     */
	public function delete()
	{
		$typeid = intval($_GET['id']);
		$result = $this->freelist_group->delete($typeid) ? array('state'=>true) : array('state'=>false,'error'=>$this->freelist_group->error());
		echo $this->json->encode($result);
	}
}