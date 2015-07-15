<?php
/**
 * 页面权限
 *
 * @aca 页面权限
 */
class controller_admin_page_priv extends page_controller_abstract
{
	private $page_priv;

	function __construct(& $app)
	{
		parent::__construct($app);
		$this->page_priv = loader::model('admin/page_priv');
	}

    /**
     * 浏览
     *
     * @aca 浏览
     */
	function index()
	{
		$this->view->assign('data', $this->page_priv->ls($_GET['pageid']));
		$this->view->display('page/priv');
	}

    /**
     * 添加
     *
     * @aca 添加
     */
	function add()
	{
		if (isset($_POST['username']) && isset($_POST['pageid']))
		{
			$userid = userid($_POST['username']);
			if ($this->page_priv->add($_POST['pageid'], $userid))
			{
				$roleid = table('admin', $userid, 'roleid');
				$rolename = table('role', $roleid, 'name');
				$result = array('state'=>true, 'pageid'=>$_POST['pageid'], 'userid'=>$userid, 'username'=>$_POST['username'], 'rolename'=>$rolename);
			}
			else
			{
				$result = array('state'=>false, 'error'=>$this->page_priv->error());
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
		if (isset($_GET['pageid']) && isset($_GET['userid']))
		{
			if ($this->page_priv->delete($_GET['pageid'], $_GET['userid']))
			{
				$result = array('state'=>true);
			}
			else
			{
				$result = array('state'=>false, 'error'=>$this->page_priv->error());
			}
			echo $this->json->encode($result);
		}
	}
}