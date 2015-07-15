<?php
/**
 * 文章管理
 *
 * @aca 文章管理
 */
final class controller_admin_article extends dms_controller_abstract
{
	private $article;
	function __construct(& $app)
	{
		parent::__construct($app);
        if (!license('dms')) cmstop::licenseFailure();
		$this->article	= loader::model('admin/dms_article');
	}

    /**
     * 文章管理
     *
     * @aca 文章管理
     */
	public function index()
	{
		$this->view->assign('head', array('title' => 'DMS:文章管理'));
		$this->view->display('article/index');
	}

    /**
     * 文章列表
     *
     * @aca 文章管理
     */
	public function page()
	{

		if (!$this->_is_search())
		{	// 非查询时
			exit($this->json->encode($this->article->page($_GET['page'], $_GET['pagesize'])));
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
                    $yesterday = strtotime('-1 day');
                    $options['createtime_start'] = strtotime(date('Y-m-d 00:00:00', $yesterday));
                    $options['createtime_end'] = strtotime(date('Y-m-d 23:59:59', $yesterday));
                    $createtime = NULL;
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
		$page		= isset($_GET['page']) ? intval($_GET['page']) : 0;
		$pagesize	= isset($_GET['pagesize']) ? intval($_GET['pagesize']) : 10;
		$data		= $this->article->search($options, $page, $pagesize);
		$result		= array('total'=>$data['total'], 'data'=>$data['data']);
		exit($this->json->encode($result));
	}

    /**
     * 添加文章
     *
     * @aca 添加文章
     */
	public function add()
	{
		if ($this->is_post())
		{
			$rst	= $this->article->add($_POST);
			exit($this->json->encode($rst));
		}
		else
		{
			$this->view->assign('head', array('title' => 'DMS:添加文章'));
			$this->view->display('article/add');
		}
	}

    /**
     * 编辑文章
     *
     * @aca 编辑文章
     */
	public function edit()
	{
		if (!$id = intval($_GET['id']))
		{
			$this->showmessage('文章ID不存在');
		}
		if ($this->is_post())
		{
			$rst	= $this->article->edit($id, $_POST);
			exit($this->json->encode($rst));
		}
		else
		{
			if (!$data = $this->article->get($id))
			{
				$this->showmessage('文章ID不存在');
			}
			$this->view->assign('head', array('title' => 'DMS:编辑文章'));
			$this->view->assign('id', $id);
			$this->view->assign($data);
			$this->view->display('article/edit');
		}
	}

    /**
     * 删除文章
     *
     * @aca 删除文章
     */
	public function del()
	{
		if ($ids = $_GET['id'])
		{
			$result	= $this->article->delete(explode(',', $ids));
			exit($this->json->encode(array('state'=>$result)));
		}
		exit($this->json->encode(array('state'=>false, 'error'=>'ID不存在')));
	}

    /**
     * 浏览引用记录
     *
     * @aca 浏览引用记录
     */
	public function quote()
	{
		if (!$id = intval($_GET['id']))
		{
			exit($this->json->encode(array('state'=>false, 'error'=>'ID不能为空')));
		}
		$quote	= loader::model('admin/dms_quote');
		$data	= $quote->select($id, 'dq.target, dq.appid, dq.time, dq.operator, dq.status, dq.disable, dc.title', 'dq.time desc');
		$this->view->assign('data', $data);
		$this->view->display('article/quote');
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