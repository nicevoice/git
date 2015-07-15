<?php
/**
 * 资源类型
 *
 * @aca 资源类型
 */
final class controller_admin_model extends dms_controller_abstract
{
    protected $_model;
    protected $_pagesize = 15;

	function __construct(&$app)
	{
		parent::__construct($app);
		if (!license('dms')) cmstop::licenseFailure();

        $this->_model = loader::model('admin/dms_model');
	}

    /**
     * 资源类型
     *
     * @aca 资源类型
     */
	public function index()
	{
        $head = array('title' => 'DMS:资源类型');
        $this->view->assign('head', $head);
        $this->view->display('model/index');
	}

    /**
     * 资源类型列表
     *
     * @aca 资源类型
     */
    public function page()
    {
        $page = intval(value($_GET, 'page', 0));
        $pagesize = intval(value($_GET, 'pagesize', $this->_pagesize));
        $order = str_replace('|', ' ', value($_GET, 'orderby'));

		$data = $this->_model->ls($_GET, '*', $order, $page, $pagesize);
		$result = array('total'=>$this->_model->total, 'data' => $data);
		echo $this->json->encode($result);
    }

    /**
     * 添加资源类型
     *
     * @aca 添加
     */
    public function add()
    {
        if ($this->is_post())
        {
            if ($modelid = $this->_model->add($_POST))
            {
                $result = array('state' => true, 'data' => $this->_model->get($modelid));
            }
            else
            {
                $result = array('state' => false, 'error' => $this->_model->error());
            }

            echo json_encode($result);
        }
        else
        {
            $this->view->display('model/add');
        }
    }

    /**
     * 编辑资源类型
     *
     * @aca 编辑
     */
    public function edit()
    {
        $modelid = intval(value($_REQUEST, 'modelid'));

        if (!$modelid || !($model = $this->_model->get($modelid)))
        {
            $this->showmessage('要修改的资源类型不存在');
        }

        if ($this->is_post())
        {
            if ($this->_model->edit($modelid, $_POST))
            {
                $result = array('state' => true, 'data' => $this->_model->get($modelid));
            }
            else
            {
                $result = array('state' => false, 'error' => $this->_model->error());
            }

            echo json_encode($result);
        }
        else
        {
            $this->view->assign($model);
            $this->view->display('model/edit');
        }
    }

    /**
     * 删除资源类型
     *
     * @aca 删除
     */
    public function delete()
    {
        $modelids = (array) id_format(value($_REQUEST, 'modelid'));

        if ($modelids)
        {
            foreach ($modelids as $modelid)
            {
                $this->_model->delete($modelid);
            }
            echo json_encode(array('state' => true));
        }
        else
        {
            echo json_encode(array('state' => false, 'error' => '要删除的资源类型不存在'));
        }
    }
}