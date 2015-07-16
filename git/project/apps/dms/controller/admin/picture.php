<?php
/**
 * 图片管理
 *
 * @aca 图片管理
 */
final class controller_admin_picture extends dms_controller_abstract
{
	private $picture;
	function __construct(& $app)
	{
		parent::__construct($app);
        if (!license('dms')) cmstop::licenseFailure();
		$this->picture	= loader::model('admin/dms_picture');
	}

    /**
     * 图片管理
     *
     * @aca 图片管理
     */
	public function index()
	{
		$this->view->assign('head', array('title' => 'DMS:图片管理'));
		$this->view->display('picture/index');
	}

    /**
     * 添加图片
     *
     * @aca 添加
     */
	public function add()
	{
		if ($this->is_post())
		{
			if ($pid = $this->picture->add($_POST))
			{
				$result	= array('state'=>true, 'data'=>$pid);
			}
			else
			{
				$result	= array('state'=>false, 'error'=>$this->picture->error());
			}
			exit($this->json->encode($result));
		}
		else
		{
			$this->view->assign('head', array('title' => 'DMS:添加图片'));
			$this->view->display('picture/add');
		}
	}

    /**
     * 获取图片
     *
     * @aca 获取
     */
	public function get()
	{
		if ($id = intval($_GET['id']))
		{
			$data = $this->picture->get($id);
			exit($this->json->encode($data));
		}
	}

    /**
     * 图片列表
     *
     * @aca 图片管理
     */
	public function page()
	{
		if (!$this->_is_search())
		{	// 非查询时
			exit($this->json->encode($this->picture->page($_GET['page'], $_GET['pagesize'])));
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
        $data = $this->picture->search($options, $page, $pagesize);
        if ($data)
        {
            foreach ($data['data'] as $d)
            {
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
     * 删除图片
     *
     * @aca 删除
     */
	public function del()
	{
		if (!$id = intval($_GET['id']))
		{
			exit($this->json->encode(array('state'=>false, 'error'=>'id不存在')));
		}
		$rst = $this->picture->remove($id);
		if ($rst)
		{
			exit($this->json->encode(array('state'=>true, 'data'=>'删除成功')));
		}
		else
		{
			exit($this->json->encode(array('state'=>false, 'error'=>$this->picture->error())));
		}
	}

    /**
     * 上传图片
     *
     * @aca 上传
     * @return bool
     */
	public function upload()
	{
		import('helper.folder');
		$dir	= ROOT_PATH.get_setting('pic_local_path').date('Ymd').'/';
		if (!folder::create($dir))
		{
			return false;
		}
		$attachment = loader::model('admin/attachment', 'system');
		if (fileext($_FILES['Filedata']['name']) == 'zip')
		{
			import('attachment.upload');
			$upload = new upload(CACHE_PATH, 'zip', 102400);
			$zipfile = $upload->execute('Filedata', true);
			if (!$zipfile)
			{
				echo '0';
				exit;
			}
			$zip = new ZipArchive;
			if (!$zip->open($zipfile))
			{
				echo '0';
				exit;
			}
			
			$tmpdir = CACHE_PATH.'tmp/'.$this->_userid.'/';
			folder::create($tmpdir);
			
			$zip->extractTo($tmpdir);
			$zip->close();
			
			$file = array();
			$files = $attachment->download_by_dir($tmpdir, $dir, 'jpg|jpeg|gif|png');
			foreach ($files as $k=>$v)
			{
				$file[] = $attachment->aid[$k].'|'.$v;
			}
			
			unlink($zipfile);
			folder::delete($tmpdir);
			
			$result = implode(',', $file);
		}
		else 
		{
			$file = $attachment->upload('Filedata', true, $dir, 'jpg|jpeg|gif|png|bmp|zip', 2048);
			$result = $file ? $attachment->aid[0].'|'.$file : '0';
		}
		
		$setting = setting('picture');
		if ($setting['watermark'] || $setting['thumb_width'] || $setting['thumb_height'])
        {
        	$image = & factory::image();
        	if ($setting['thumb_width'] || $setting['thumb_height']) $image->set_thumb($setting['thumb_width'], $setting['thumb_height']);           	
			
        	$files = $attachment->get_files();
            foreach ($files as $file)
            {
            	$file = UPLOAD_PATH.$file;
            	if ($setting['thumb_width'] || $setting['thumb_height']) $image->thumb($file);
            }
        }
        
        echo $result;
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