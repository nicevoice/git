<?php
/**
 * 存储管理
 *
 * @aca 存储管理
 */
final class controller_admin_server extends dms_controller_abstract
{
    protected $_server;
    protected $_pagesize = 15;

	function __construct(&$app)
	{
		parent::__construct($app);
		if (!license('dms')) cmstop::licenseFailure();

        $this->_server = loader::model('admin/dms_server');
	}

    /**
     * 存储管理
     *
     * @aca 存储管理
     */
	public function index()
	{
        $head = array('title' => 'DMS:存储管理');
        $this->view->assign('head', $head);
        $this->view->display('server/index');
	}

    /**
     * 存储服务器列表
     *
     * @aca 存储管理
     */
    public function page()
    {
        $page = intval(value($_GET, 'page', 0));
        $pagesize = intval(value($_GET, 'pagesize', $this->_pagesize));
        $order = str_replace('|', ' ', value($_GET, 'orderby'));

		$data = $this->_server->ls($_GET, '*', $order, $page, $pagesize);
		$result = array('total'=>$this->_server->total, 'data' => $data);
		echo $this->json->encode($result);
    }

    /**
     * 添加存储
     *
     * @aca 添加
     */
    public function add()
    {
        if ($this->is_post())
        {
            if ($serverid = $this->_server->add($_POST))
            {
                $result = array('state' => true, 'data' => $this->_server->get($serverid));
            }
            else
            {
                $result = array('state' => false, 'error' => $this->_server->error());
            }

            echo json_encode($result);
        }
        else
        {
            $this->view->display('server/add');
        }
    }

    /**
     * 编辑存储
     *
     * @aca 编辑
     */
    public function edit()
    {
        $serverid = intval(value($_REQUEST, 'serverid'));

        if (!$serverid || !($server = $this->_server->get($serverid)))
        {
            $this->showmessage('要修改的服务器信息不存在');
        }

        if ($this->is_post())
        {
            if ($this->_server->edit($serverid, $_POST))
            {
                $result = array('state' => true, 'data' => $this->_server->get($serverid));
            }
            else
            {
                $result = array('state' => false, 'error' => $this->_server->error());
            }

            echo json_encode($result);
        }
        else
        {
            $this->view->assign($server);
            $this->view->display('server/edit');
        }
    }

    /**
     * 删除存储
     *
     * @aca 删除
     */
    public function delete()
    {
        $serverids = (array) id_format(value($_REQUEST, 'serverid'));

        if ($serverids)
        {
            foreach ($serverids as $serverid)
            {
                $this->_server->delete($serverid);
            }
            echo json_encode(array('state' => true));
        }
        else
        {
            echo json_encode(array('state' => false, 'error' => '要删除的服务器信息不存在'));
        }
    }
}