<?php
/**
 * 类型管理
 *
 * @aca 类型管理
 */
class controller_admin_type extends guestbook_controller_abstract
{
	private $guestbook_type;

	function __construct(&$app)
	{
		parent::__construct($app);
		$this->guestbook_type = loader::model('admin/guestbook_type');
	}

    /**
     * 浏览
     *
     * @aca 浏览
     */
	function index()
	{
		$this->view->assign('head', array('title'=>'类型管理'));
		$this->view->display("type");
	}

    /**
     * 列表
     *
     * @aca 浏览
     */
	function page()
	{
		$where = null;
		$data = $this->guestbook_type->select();
		$total = $this->guestbook_type->count($where);
		echo $this->json->encode(array('total'=>$total,'data'=>$data));
	}

    /**
     * 添加
     *
     * @aca 添加
     */
	function add()
	{
		if ($this->is_post())
		{
			$data = $_POST;
			if ($typeid = $this->guestbook_type->add($data))
			{
				$result = array('state'=>true, 'data'=>$this->guestbook_type->get($typeid));
			}
			else
			{
				$result = array('state'=>false, 'error'=>$this->guestbook_type->error());
			}
			echo $this->json->encode($result);
		}
		else
		{
			$this->view->display('add');
		}
	}

    /**
     * 编辑
     *
     * @aca 编辑
     */
	function edit()
	{
		$typeid = intval($_GET['typeid']);
		if ($this->is_post())
		{
			$data = $_POST;
			if ($this->guestbook_type->edit($typeid, $data) !== false)
			{
				$result = array('state'=>true, 'data'=>$this->guestbook_type->get($typeid));
			}
			else
			{
				$result = array('state'=>false, 'error'=>$this->guestbook_type->error());
			}
			echo $this->json->encode($result);
		}
		else
		{
			$type = $this->guestbook_type->get($typeid);
			$this->view->assign($type);
			$this->view->display('edit');
		}
	}

    /**
     * 删除
     *
     * @aca 删除
     */
	function delete()
	{
		$typeid = $_GET['id'];
		$result = $this->guestbook_type->delete($typeid) ? array('state'=>true) : array('state'=>false,'error'=>$this->guestbook_type->error());
		echo $this->json->encode($result);
	}
}