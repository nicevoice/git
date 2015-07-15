<?php
/**
 * 区块权限
 *
 * @aca 区块权限
 */
class controller_admin_section_priv extends page_controller_abstract
{
	private $section_priv;

	function __construct(& $app)
	{
		parent::__construct($app);
		$this->section_priv = loader::model('admin/section_priv');
	}

    /**
     * 浏览
     *
     * @aca 浏览
     */
	function index()
	{
		$this->view->assign('data', $this->section_priv->ls($_GET['sectionid']));
		$this->view->display('section/priv');
	}

    /**
     * 添加
     *
     * @aca 添加
     */
	function add()
	{
		if (isset($_POST['username']) && isset($_POST['sectionid']))
		{
			$userid = userid($_POST['username']);
			if ($this->section_priv->add($_POST['sectionid'], $userid))
			{
				$roleid = table('admin', $userid, 'roleid');
				$rolename = table('role', $roleid, 'name');
				$result = array('state'=>true, 'sectionid'=>$_POST['sectionid'], 'userid'=>$userid, 'username'=>$_POST['username'], 'rolename'=>$rolename);
			}
			else
			{
				$result = array('state'=>false, 'error'=>$this->section_priv->error());
			}
			echo $this->json->encode($result);
		}
	}

    /**
     * 删除
     *
     * @aca 删除
     */
	function delete()
	{
		if (isset($_GET['sectionid']) && isset($_GET['userid']))
		{
			if ($this->section_priv->delete($_GET['sectionid'], $_GET['userid']))
			{
				$result = array('state'=>true);
			}
			else
			{
				$result = array('state'=>false, 'error'=>$this->section_priv->error());
			}
			echo $this->json->encode($result);
		}
	}
}