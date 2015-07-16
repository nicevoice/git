<?php
/**
 * 规则管理
 *
 * @aca 规则管理
 */
class controller_admin_rules extends cdn_controller_abstract
{
	public	$cdn, $cdn_rules, $cdn_type;

	function __construct(&$app)
	{
		parent::__construct($app);
		$this->cdn		= loader::model('admin/cdn');
		$this->cdn_rules	= loader::model('admin/cdn_rules');
	}

    /**
     * 规则列表
     *
     * @aca 规则列表
     */
	function page()
	{
		$where = "cdnid=$_GET[cdnid]";
		$total = $this->cdn_rules->count($where);
		$data = $this->cdn_rules->page($where);
		$result = array('total'=>$total, 'data'=>$data);
		echo $this->json->encode($result);
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
			if ($this->cdn_rules->add($_POST))
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
			$this->view->display('rules/add');
		}
	}

    /**
     * 编辑
     *
     * @aca 编辑
     */
	function edit()
	{
		$id	= intval($_GET['id']);
		$id > 0 || $this->showmessage('规则不存在');
		if ($this->is_post())
		{
			if ($this->cdn_rules->edit($_POST, $id))
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
			$ls	= $this->cdn_rules->get($id);
			$this->view->assign($id);
			$this->view->assign($ls);
			$this->view->display('rules/edit');
		}
	}

    /**
     * 删除
     *
     * @aca 删除
     */
	function delete()
	{
		$id	= intval($_GET['id']);
		$id > 0 || $this->showmessage('规则不存在');
		if ($this->cdn_rules->delete("id=$id"))
		{
			exit('{"state":"true"}');
		}
		else
		{
			exit($this->json->encode(array('state'=>false, 'error'=>'删除失败')));
		}
	}
}