<?php
/**
 * 系统日志
 *
 * @aca 系统日志
 */
final class controller_admin_log extends dms_controller_abstract
{
    protected $_log;
    protected $_pagesize = 15;

	function __construct(&$app)
	{
		parent::__construct($app);
		if (!license('dms')) cmstop::licenseFailure();

        $this->_log = loader::model('admin/dms_log');
	}

    /**
     * 系统日志
     *
     * @aca 系统日志
     */
	public function index()
	{
        $head = array('title' => 'DMS:系统日志');
        $this->view->assign('head', $head);
        $this->view->assign('models', loader::model('admin/dms_model')->form());
        $this->view->display('log/index');
	}

    /**
     * 日志列表
     *
     * @aca 日志列表
     */
    public function page()
    {
        $page = intval(value($_GET, 'page', 0));
        $pagesize = intval(value($_GET, 'pagesize', $this->_pagesize));
        $order = str_replace('|', ' ', value($_GET, 'orderby', 'logid desc'));

		$data = $this->_log->ls($_GET, '*', $order, $page, $pagesize);
		$result = array('total'=>$this->_log->total, 'data' => $data);
		echo $this->json->encode($result);
    }

    /**
     * 查看日志
     *
     * @aca 查看日志
     */
    public function view()
    {
        $logid = intval(value($_GET, 'logid'));

        if (!$logid || !($log = $this->_log->get($logid)))
        {
            $this->showmessage('日志信息不存在');
        }

        $this->view->assign($log);
        $this->view->display('log/view');
    }

    /**
     * 删除日志
     *
     * @aca 删除日志
     */
    public function delete()
    {
        $logids = (array) id_format(value($_REQUEST, 'logid'));

        if ($logids)
        {
            foreach ($logids as $logid)
            {
                $this->_log->delete($logid);
            }
            echo json_encode(array('state' => true));
        }
        else
        {
            echo json_encode(array('state' => false, 'error' => '要删除的日志信息不存在'));
        }
    }
}