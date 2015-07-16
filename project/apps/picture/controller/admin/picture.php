<?php
/**
 * 组图管理
 *
 * @aca 组图管理
 */
class controller_admin_picture extends picture_controller_abstract
{
	private $picture, $pagesize = 15, $upload_max_filesize = 2048, $modelid, $weight = null,$attachment;
	
	function __construct(& $app)
	{
		parent::__construct($app);
		$this->picture = loader::model('admin/picture');
		$this->weight = loader::model('admin/admin_weight', 'system');
		$this->modelid = $this->picture->modelid;
		$this->attachment = loader::model('admin/attachment', 'system');
		
		if (isset($_REQUEST['catid'])) $this->priv_category($_REQUEST['catid']);

        $upload_max_filesize = intval(ini_get('upload_max_filesize'));
        if ($upload_max_filesize) 
		{
            $this->upload_max_filesize = $upload_max_filesize * 1024;
        }
	}
	
	public function __call($method, $args)
	{
		if(in_array($method, array('delete', 'clear', 'remove', 'restore', 'restores', 'approve', 'pass', 'reject', 'islock', 'lock', 'unlock', 'publish', 'unpublish'), true)) 
		{
			$var = in_array($method, array('clear', 'restores')) ? 'catid' : 'contentid';
			$result = $this->picture->$method($_REQUEST[$var]) ? array('state'=>true) : array('state'=>false, 'error'=>$this->picture->error());
			echo $this->json->encode($result);
        }
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
			if ($contentid = $this->picture->add($_POST))
			{
				$result = array('state'=>true, 'contentid' => $contentid);
				$article = $this->picture->get($contentid, 'url, status');
				$article['status'] == 6 && $result['url'] = $article['url'];
			}
			else
			{
				$result = array('state'=>false, 'error'=>$this->picture->error());
			}
			echo $this->json->encode($result);
		}
		else
		{
			$setting = setting('system');
			$catid = $_GET['catid'];
			$myweight = array_shift($this->weight->get_by('userid', $this->_userid, 'weight'));
			$weight = $this->setting['weight'];
			$data = array('status'=>6,
			              'weight' => $myweight ? (($myweight-$weight)>=0 ? $weight : $myweight) : 0,
			              'editor'=>table('admin', $this->_userid, 'name'),
			              'allowcomment'=>1,
			             );
			$this->view->assign($data);
            $this->view->assign('catname', $this->picture->category[$catid]['name']);
		    $this->view->assign('head', array('title'=>'发布组图'));
			$this->view->assign('repeatcheck', value(setting::get('system'), 'repeatcheck', 0));
			$this->view->display('add');
		}
	}

    /**
     * 编辑
     *
     * @aca 编辑
     */
	function edit()
	{
		if ($this->is_post())
		{		
			if ($this->picture->edit($_POST['contentid'], $_POST))
			{
                $result = array('state'=>true, 'contentid'=>$_POST['contentid'], 'url'=>table('content', $_POST['contentid'], 'url'));
			}
			else 
			{
				$result = array('state'=>false, 'contentid'=>$_POST['contentid'], 'error'=>$this->picture->error());
			}
			echo $this->json->encode($result);
		}
		else 
		{
			$myweight = array_shift($this->weight->get_by('userid', $this->_userid, 'weight'));
			$contentid = $_GET['contentid'];			
			$data = $this->picture->get($contentid, '*', 'get');
			if (!$data) $this->showmessage($this->picture->error());
			
			$this->priv_category($data['catid']);
			
			$this->picture->lock($contentid);
			
			$this->view->assign($data);
			$this->view->assign('myweight', $myweight);
		    $this->view->assign('head', array('title'=>'编辑组图：'.$data['title']));
			$this->view->display('edit');
		}
	}

    /**
     * 查看
     *
     * @aca 查看
     */
	function view()
	{
		$contentid = $_GET['contentid'];
		$r = $this->picture->get($contentid, '*', 'view');
		if (!$r) $this->showmessage($this->picture->error());
		
        $this->priv_category($r['catid']);
        
		if(is_array($r['pictures']))
		foreach ($r['pictures'] as $key=>$val)
		{
			$r['pictures'][$key]['info'] = $this->_get_pic_info(UPLOAD_PATH.$val['image'], $val['image']);
		}
		
		$this->view->assign($r);
		$this->view->assign('head', array('title'=>$r['title']));
		$this->view->display('view');
	}

    /**
     * 相关
     *
     * @aca 相关
     */
	function related()
	{
		$keywords = $_GET['keywords'];
		$catid = intval($_GET['catid']);
		$modelid = intval($_GET['modelid']);
		$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
		$pagesize = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : 20;

		$data = $this->picture->content->related($catid, $modelid, $keywords, $page, $pagesize);
		$result = $page == 1 ? array('state'=>true, 'data'=>$data, 'total'=>$this->picture->content->related_total($catid, $modelid, $keywords)) : array('state'=>true, 'data'=>$data);
		echo $this->json->encode($result);
	}

    /**
     * 引用
     *
     * @aca 引用
     */
	function reference()
	{
		if ($this->is_post())
		{
			$contentid = $_REQUEST['contentid'];
			$catid = $_REQUEST['catid'];
			if (is_array($catid))
			{
				foreach ($catid as $cid)
				{
					$result = $this->picture->reference($contentid, $cid);
					if (!$result) break;
				}
			}
			else
			{
				$result = $this->picture->reference($contentid, $catid);
			}
			$result = $result ? array('state'=>true) : array('state'=>false, 'error'=>$this->picture->error());
			echo $this->json->encode($result);
		}
		else 
		{
			$category = table('category');
			foreach ($category as $k=>$c)
			{
				$category[$k]['checkbox'] = '';
				if ($c['childids'])
				{
					if (!priv::category($k, true)) unset($category[$k]);
				}
				elseif (!priv::category($k))
				{
					unset($category[$k]);
				}
				else 
				{
					$category[$k]['checkbox'] = '<input type="checkbox" name="catid[]" value="'.$c['catid'].'" class="radio_style" />';
				}
			}
			import('helper.treeview');
			$treeview = new treeview($category);
			$data = $treeview->get(null, 'category_tree', '<li><span id="{$catid}">{$checkbox}{$name}</span>{$child}</li>');
			$this->view->assign('data', $data);
			$this->view->display('content/reference', 'system');
		}
	}

    /**
     * 移动
     *
     * @aca 移动
     */
	function move()
	{
		if ($this->is_post())
		{
			$contentid = $_REQUEST['contentid'];
			$catid = $_REQUEST['catid'];
			$result = $this->picture->move($contentid, $catid) ? array('state'=>true, 'contentid'=>$contentid) : array('state'=>false, 'error'=>$this->picture->error());
			echo $this->json->encode($result);
		}
		else 
		{
			$category = table('category');
			foreach ($category as $k=>$c)
			{
				$category[$k]['radio'] = '';
				if ($c['childids'])
				{
					if (!priv::category($k, true)) unset($category[$k]);
				}
				elseif (!priv::category($k))
				{
					unset($category[$k]);
				}
				else 
				{
					$category[$k]['radio'] = '<input type="radio" name="catid" value="'.$c['catid'].'" class="radio_style" />';
				}
			}
			import('helper.treeview');
			$treeview = new treeview($category);
			$data = $treeview->get(null, 'category_tree', '<li><span id="{$catid}">{$radio}{$name}</span>{$child}</li>');
			$this->view->assign('data', $data);
			$this->view->display('content/move', 'system');
		}
	}

    /**
     * 远程采集
     *
     * @aca 远程采集
     */
	function remote()
	{
		if ($this->is_post())
		{
			$remote_pictures = $_POST['remote_pictures'];
			if (empty($remote_pictures))
			{
				$result = array('state'=>false, 'error'=>'远程图片地址不能为空！');
			}
			else
			{
				$attachment = loader::model('admin/attachment', 'system');
				$k = 0;
				$pictures = array();
				$imgext = array('jpeg','jpg','gif','png');
				$remote_pictures = array_filter(array_map('trim', explode("\n", $remote_pictures)));
				foreach ($remote_pictures as $url)
				{
					 if(in_array(strtolower(pathinfo($url, PATHINFO_EXTENSION)), $imgext))
					 {	
					 	$file = $attachment->download_by_file($url);
					 	if ($file)
					 	{
						 	$pictures[] = $attachment->aid[$k].'|'.$file;
						 	$k++;
					 	}
					 }
				}
				$setting = setting('picture');
				if($k > 0)
				{
                    $result = array('state'=>true, 'data'=>$pictures);
				}
				else
				{
					$result = array('state'=>false, 'error'=>'远程图片获取错误！');
				}
			}
			echo $this->json->encode($result);
		}
		else 
		{
            $this->view->assign('single', intval(value($_GET, 'single', 0)));
			$this->view->display('remote');
		}
	}

    /**
     * 定时上下线
     *
     * @aca 定时上下线
     */
	function cron()
	{
		@set_time_limit(600);
		
		$publishid = $this->picture->content->cron_publish($this->modelid);
		if ($publishid) array_map(array($this->picture, 'publish'),  $publishid);
		
		$unpublishid = $this->picture->content->cron_unpublish($this->modelid);
		if ($unpublishid) array_map(array($this->picture, 'unpublish'),  $unpublishid);
		
		exit ('{"state":true}');
	}

    /**
     * 上传
     *
     * @aca 上传
     */
	function upload()
	{
		$filename = $_FILES['multiUp']['name'];
		$upload_max_filesize = (intval(substr(ini_get('upload_max_filesize'),0,-1)))*1024*1024;

		$file	= $this->attachment->upload('multiUp',true,null, UPLOAD_FILE_EXTS,$upload_max_filesize);
		$aid	= $this->attachment->aid[0];
		$filext = strtolower(substr($filename,strrpos($filename,'.')+1));
		$isimage = in_array($filext,array('jpg','jpeg','gif','png'));
		$isvideo = in_array($filext,array('wmv','flv','swf','rm','rmvb'));
		$exts = array('chm','do','doc','docx','exe','hlp','htm','html','js','mid','midi','mp3','pdf','psd','rar','shtml','tif','txt','xls','xlsx','xml','zip','ppt','pptx');
				
		if (!$isimage)
		{
			exit($this->json->encode(array('state'=>false)));
		}
		// 缩略图
		$setting = setting('picture');
		if ($setting['thumb_width'] || $setting['thumb_height'])
		{
			$image = & factory::image();
			if ($setting['thumb_width'] || $setting['thumb_height']) $image->set_thumb($setting['thumb_width'], $setting['thumb_height']);
			$sfile = UPLOAD_PATH.$file;
			$tmp_arr = explode('/', $file);
			array_push($tmp_arr, 'orig_'.array_pop($tmp_arr));
			$ofile = UPLOAD_PATH.implode('/', $tmp_arr);
			@copy($sfile, $ofile);
			if ($setting['thumb_width'] || $setting['thumb_height']) $image->thumb($sfile);
		}
		
		$code = '<img src="'.UPLOAD_URL.$file.'" alt="'.array_shift(explode('.', $filename)).'" />';
		
		$result = array(
			'state' => !!$file,
			'code'	=> $code,
			'msg'	=> !!$file ? '上传成功' : $this->attachment->error(),
			'aid'	=> $aid,
			'img'	=> UPLOAD_URL.$file
		);
		exit($this->json->encode($result));
	}

	/**
	 * 图组模型中插入图片的iframe
     *
     * @aca 添加图片
	 */
	function image()
	{
		$setting	= setting('system');
		$use_watermark = setting('picture', 'watermark') && $setting['watermark_enabled'];
		$watermark	= loader::model('admin/watermark', 'system')->select('disable=0' ,'`watermarkid` as id, `name`');
		$dmsc = setting::get('dmsc', 'status');
		$this->view->assign('dmsc', (bool)$dmsc);
		$this->view->assign('use_watermark', $use_watermark);
		$this->view->assign('default_watermark', $setting['default_watermark']);
		$this->view->assign('watermark', $watermark);
        $this->view->assign('single', value($_GET, 'single', false));
		$this->view->display('image');
	}

    /**
     * 删除图片
     *
     * @aca 删除图片
     */
	function delete_pic()
	{
		$aid	= intval($_GET['id']);
		if ($this->attachment->delete($aid))
		{
			$result = array('state'=>true, 'data'=>'删除成功');
		}
		else
		{
			$result	= array('state'=>false, 'error'=>$this->attachment->error());
		}
		exit($this->json->encode($result));
	}
}