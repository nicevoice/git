<?php
@set_time_limit(3600);	// 如果一个小时还未能下载完，停止，下次再启动下载

/**
 * 附件管理
 *
 * @aca 附件管理
 */
final class controller_admin_attachment extends dms_controller_abstract
{
	private $article;
	function __construct(& $app)
	{
		parent::__construct($app);
        if (!license('dms')) cmstop::licenseFailure();
		$this->attachment	= loader::model('admin/dms_attachment');
	}

    /**
     * 附件管理
     *
     * @aca 附件管理
     */
	public function index()
	{
		$this->view->assign('head', array('title' => 'DMS:附件管理'));
		$this->view->display('attachment/index');
	}

    /**
     * 获取附件
     *
     * @aca 获取附件
     */
	public function get()
	{
		if ($id = intval($_GET['id']))
		{
			$data = $this->attachment->get($id);
			exit($this->json->encode($data));
		}
	}

    /**
     * 附件列表
     *
     * @aca 附件管理
     */
	public function page()
	{
		if (!$this->_is_search())
		{
			// 非查询时
			$data	= $this->attachment->page('status>0', '*', 'updatetime desc', $_GET['page'], $_GET['pagesize']);
			$total	= $this->attachment->count('status>0');
			exit($this->json->encode(array('total'=>$total, 'data'=>$data)));
		}

		$options = array();
        // 时间处理
        $createtime_start	= value($_GET, 'createtime_start');
        $createtime_end		= value($_GET, 'createtime_end');
        $createtime			= value($_GET, 'createtime');
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
        $type		= value($_GET, 'type');
        $keyword	= value($_GET, 'keyword');
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
		if (isset($_GET['minsize']))
		{
			$options['minsize']	= $_GET['minsize'];
		}
		if (isset($_GET['maxsize']))
		{
			$options['maxsize']	= $_GET['maxsize'];
		}
		$page		= isset($_GET['page']) ? intval($_GET['page']) : 0;
		$pagesize	= isset($_GET['pagesize']) ? intval($_GET['pagesize']) : 10;
		$data		= $this->attachment->search($options, $page, $pagesize);
		$result		= array('total'=>$data['total'], 'data'=>$data['data']);
		exit($this->json->encode($result));
	}

    /**
     * 添加附件
     *
     * @aca 添加附件
     */
	public function add()
	{
		if ($this->is_post())
		{
			if ($pid = $this->attachment->add($_POST))
			{
				$result	= array('state'=>true, 'data'=>$pid);
			}
			else
			{
				$result	= array('state'=>false, 'error'=>$this->attachment->error());
			}
			exit($this->json->encode($result));
		}
		else
		{
			$this->view->assign('head', array('title' => 'DMS:添加附件'));
			$this->view->display('attachment/add');
		}
	}

    /**
     * 删除附件
     *
     * @aca 删除附件
     */
	public function remove()
	{
		if ($id = intval($_GET['id']))
		{
			if ($this->attachment->remove($id))
			{
				exit($this->json->encode(array('state'=>true, 'data'=>'删除成功')));
			}
		}
		exit($this->json->encode(array('state'=>false, 'error'=>'删除失败')));
	}

    /**
     * 上传附件
     *
     * @aca 上传附件
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
		$allowed_ext = get_setting('allowed_ext');
		$file = $attachment->upload('Filedata', true, $dir, $allowed_ext, 81920);
		$file_ext = fileext($_FILES['Filedata']['name']);
		$ext_icon = IMG_PATH.'images/ext/'.$file_ext.'.gif';
		$ext_icon = file_exists($ext_icon) ? str_replace(IMG_PATH, IMG_URL,$ext_icon) : '';
		$result = $file ? $attachment->aid[0].'|'.$file.'|'.$ext_icon : '0';
        echo $result;
	}

	/**
	 * 队列上传
     *
     * @aca 队列上传
	 */
	function interval()
	{
		$db = &factory::db();
		$list = $db->select("SELECT attachmentid,path,size,createtime FROM #table_dms_attachment WHERE status=9 ORDER BY attachmentid ASC LIMIT 1");
		if(empty($list))
		{
			exit($this->json->encode(array('state'=>true,'info'=>'no queue data')));
		}
		foreach($list as $row)
		{
			if($this->_wget($row))
			{
				// 下载成功，更新到数据库
				$sql = "UPDATE #table_dms_attachment SET path='{$row['path']}',updatetime='".time()."',status='1' WHERE attachmentid='{$row['attachmentid']}'";
				$db->exec($sql);
			}
			else
			{
				// 下载失败
				continue;
			}
		}
		exit($this->json->encode(array('state'=>true,'info'=>'执行成功')));
	}
	
	/**
	 * 下载远程文件，支持断点续传
	 * @param array $data 数据中包括：远程文件路径，文件添加日期，文件大小
	 */
	private function _wget(&$data)
	{
		if(strpos($data['path'],'://') === false)
		{
			return false;
		}
		$remote_file = $data['path'];
		$remore_info = pathinfo($remote_file);
		$data['ext'] = $remore_info['extension'];
		// 从URL中无法获取到文件后缀，不是标准的URL格式，URL最后为文件名
		if(empty($data['ext']))
		{
			return false;
		}
		$local_file = md5($remote) .'.'.$data['ext'];
		$local_dir = ROOT_PATH.get_setting('pic_local_path').date('Ymd',$data['createtime']).'/';
		$data['path'] = str_replace(UPLOAD_PATH, '', $local_dir.$local_file);
		folder::create($local_dir);
		$local_file = $local_dir.$local_file;
		//判断文件是否下载完成
		$local_size = 0;
		if(file_exists($local_file))
		{
			$local_size = filesize($local_file);
			if($local_size == $data['size'])
			{
				return true;
			}
		}
		// DEBUG
		//echo "begin wget {$remote_file} ...\n";
		//使用CURL开始下载
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $remote_file);
		
		if ($local_size) {
		    curl_setopt($ch, CURLOPT_RANGE, $local_size . "-");
		}
		$fp = fopen($local_file, "a");
		if (!$fp)
		{
		    return false;
		}
		curl_setopt($ch, CURLOPT_FILE, $fp);
		$result = curl_exec($ch);
		curl_close($ch);		
		fclose($fp);
		return true;
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