<?php
/**
 * 组图管理
 *
 * @aca 组图管理
 */
final class controller_admin_picture_group extends dms_controller_abstract
{
	private $picture_group;
	function __construct(& $app)
	{
		parent::__construct($app);
        if (!license('dms')) cmstop::licenseFailure();
		$this->picture			= loader::model('admin/dms_picture');
		$this->picture_group	= loader::model('admin/dms_picture_group');
	}

    /**
     * 组图管理
     *
     * @aca 组图管理
     */
	public function index()
	{
		$this->view->assign('head', array('title' => 'DMS:组图管理'));
		$this->view->display('picture_group/index');
	}

    /**
     * 组图列表
     *
     * @aca 组图管理
     */
    public function page()
    {
		if (!$this->_is_search())
		{	// 非查询时
			exit($this->json->encode($this->picture_group->page($_GET['page'], $_GET['pagesize'])));
		}
        $options = array();

        // 时间处理
        $createtime_start = value($_GET, 'createtime_start');
        $createtime_end = value($_GET, 'createtime_end');
        $createtime = value($_GET, 'createtime');
        if ($createtime_start || $createtime_end)
        {
            $createtime_start && ($options['createtime_start'] = strtotime($createtime_start));
            $createtime_end && ($options['createtime_end'] = strtotime($createtime_end));
        }
        elseif ($createtime)
        {
            import('helper.date');
            $date = new date();

            switch ($createtime)
            {
                case 'today':
                    $createtime = strtotime(date('Y-m-d 00:00:00', TIME));
                    break;
                case 'yesterday':
                    $createtime = strtotime(date('Y-m-d 00:00:00', strtotime('-1 day')));
                    break;
                case 'week':
                    $createtime = $date->totime($date->firstday_of_week());
                    break;
                case 'month':
                    $createtime = $date->totime($date->firstday_of_month());
                    break;
                default:
                    $createtime = NULL;
                    break;
            }
            $createtime && ($options['createtime_start'] = $createtime);
        }

        // 关键词处理
        $type = value($_GET, 'type');
        $keyword = value($_GET, 'keyword');
        if ($keyword)
        {
            if ($type && in_array($type, array('title', 'source', 'author', 'description', 'content', 'tags')))
            {
                $options[$type] = $keyword; // 字段检索
            }
            else
            {
                $options['keyword'] = $keyword; // 全文检索
            }
        }

        $page = max(1, intval(value($_GET, 'page', 0)));
        $pagesize = intval(value($_GET, 'pagesize', 15));
        $data = $this->picture_group->search($options, $page, $pagesize);
        if ($data)
        {
            foreach ($data['data'] as $d)
            {
                //$d['cover'] = array_shift(value($d, 'pictures', array()));
                $result['data'][] = $d;
            }
            $result['state'] = true;
            $result['total'] = $data['total'];
			if ($result['total'] == 0)
			{
				$result['data']	= array();
			}
        }
        else
        {
            $result = array('state' => false, 'data' => array());
        }
		echo json_encode($result);
    }

    /**
     * 查看组图
     *
     * @aca 查看
     */
	public function view()
	{
		if (!$id = intval($_GET['id']))
		{
			$this->showmessage('ID不存在');
		}
		$this->view->assign('groupid', $id);
		$this->view->display('picture_group/list');
	}

    /**
     * 组图图片列表
     *
     * @aca 查看
     */
	public function ls()
	{
		if (!$id = intval($_GET['groupid']))
		{
			exit($this->json->encode(array('total'=>0, 'data'=>'')));
		}
		$data	= $this->picture_group->get_pic_list($id);
		$total	= count($data);
		exit($this->json->encode(array('total'=>$total, 'data'=>$data)));
	}

    /**
     * 添加组图
     *
     * @aca 添加
     */
	public function add()
	{
		if ($this->is_post())
		{
			if (count($_POST['pictures']) < 1)
			{
				exit($this->json->encode(array('state' => false, 'error' => '未上传图片')));
			}
			$rst	= $this->picture_group->add($_POST);
			exit($this->json->encode($rst));
		}
		else
		{
			$this->view->assign('head', array('title' => 'DMS:添加组图'));
			$this->view->display('picture_group/add');
		}
	}

    /**
     * 删除组图
     *
     * @aca 删除
     */
	public function del()
	{
		if ($ids = $_GET['id'])
		{
			$result	= $this->picture_group->delete(explode(',', $ids));
			exit($this->json->encode(array('state'=>$result)));
		}
		exit($this->json->encode(array('state'=>false, 'error'=>'ID不存在')));
	}

	private function _is_search()
	{
		$arr	= array('keyword', 'createtime', 'createtime_start', 'createtime_end');
		foreach ($arr as $key)
		{
			if ($_GET[$key])
			{
				return true;
			}
		}
		return false;
	}
}