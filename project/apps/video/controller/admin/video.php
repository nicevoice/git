<?php
/**
 * 视频管理
 *
 * @aca 视频管理
 */
class controller_admin_video extends video_controller_abstract
{
	private $vedio,$pagesize = 15,$modelid,$upload_max_filesize, $weight = null;
	
	function __construct(& $app)
	{
		parent::__construct($app);
		$this->video = loader::model('admin/video');
		$this->weight = loader::model('admin/admin_weight', 'system');
		$this->modelid = $this->video->modelid;
		
		$this->upload_max_filesize = (intval(substr(ini_get('upload_max_filesize'),0,-1)))*1024*1024;
		
		if (isset($_REQUEST['catid'])) $this->priv_category($_REQUEST['catid']);
	}
	
	public function __call($method, $args)
	{
		if(in_array($method, array('delete', 'clear', 'remove', 'restore', 'restores', 'approve', 'pass', 'reject', 'islock', 'lock', 'unlock', 'publish', 'unpublish'), true)) 
		{
			$var = in_array($method, array('clear', 'restores')) ? 'catid' : 'contentid';
			$result = $this->video->$method($_REQUEST[$var]) ? array('state'=>true) : array('state'=>false, 'error'=>$this->video->error());
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
		if($this->is_post())
		{

			if ($contentid = $this->video->add($_POST))
			{
				$result = array('state'=>true, 'contentid' => $contentid);
				$video = $this->video->get($contentid, 'url, status');
				$video['status'] == 6 && $result['url'] = $video['url'];
			}
			else
			{
				$result = array('state'=>false, 'error'=>$this->video->error());
			}
			echo $this->json->encode($result);
		}
		else 
		{
			$catid = $_GET['catid'];
			$myweight = array_shift($this->weight->get_by('userid', $this->_userid, 'weight'));
			$weight = $this->setting['weight'];
			$data = array('status'=>6,
			              'weight' => $myweight ? (($myweight-$weight)>=0 ? $weight : $myweight) : 0,
			              'editor'=>table('admin', $this->_userid, 'name'),
			              'allowcomment'=>1,
			              'saveremoteimage'=>1,
			             );
			          
			$this->view->assign('ccid',setting('system', 'ccid'));
			$this->view->assign('openserver',$this->setting['openserver'] ? 1 : 0);
			
			$this->view->assign($data);
			$this->view->assign('catname', $this->video->category[$catid]['name']);
			$this->view->assign('upload_max_filesize',$this->upload_max_filesize);			
			$this->view->assign('head', array('title'=>'发布视频'));
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
			
			if ($this->video->edit($_POST['contentid'], $_POST))
			{
				$result = array('state'=>true);
			}
			else
			{
				$result = array('state'=>false, 'error'=>$this->video->error());
			}
			echo $this->json->encode($result);
		}
		else
		{
			$myweight = array_shift($this->weight->get_by('userid', $this->_userid, 'weight'));
			$contentid = $_GET['contentid'];
			$data = $this->video->get($contentid, '*', 'get', true);
			if (!$data) $this->showmessage($this->video->error());
			
			$this->priv_category($data['catid']);
			
			$data['playtime'] = $data['playtime']?$data['playtime']:'';
			
			$this->view->assign('ccid',setting('system', 'ccid'));
			$this->view->assign('openserver',$this->setting['openserver'] ? 1 : 0);
			
			$this->video->lock($contentid);
			$this->view->assign($data);
			$this->view->assign('myweight', $myweight);
			
			$this->view->assign('upload_max_filesize',$this->upload_max_filesize);	
			$this->view->assign('head', array('title'=>'编辑视频：'.$data['title']));
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
		$this->video->is_backend = true;
		$r = $this->video->get($_GET['contentid'], '*', 'view',true,true);
		if (!$r) $this->showmessage($this->video->error());	
		
		$r['autostart'] = 'false';
		
		$fileext = fileext($r['video']);
		if(preg_match('/^(\[cc\])([^\[]+)(\[\/cc\])$/i', $r['video'], $matches)) 
		{
			$r['video'] = $matches[2];
			$r['player'] = 'cc';
		}
		elseif($fileext && strlen($fileext)<7)
		{
			$r['player'] = $fileext;	
		}
		elseif(preg_match('/^(\[ctvideo\])([^\[]+)(\[\/ctvideo\])$/i', $r['video'], $matches)) 
		{
			$r['video'] = $matches[2];
			$r['player'] = 'ctvideo';
			$r['playerurl'] = $this->setting['player'];
		}
		else 
		{
			$r['player'] = 'swf';
		}
		
		$this->priv_category($r['catid']);
		
		$this->view->assign($r);  
		$this->view->assign('head', array('title'=>$r['title']));
		$this->view->display('view');
	}

	/**
	 * 生成调用代码HTML
     *
     * @aca 获取调用代码
	 */
	function code()
	{
		!isset($_GET['contentid']) && $this->showmessage('数据编号错误！');;
	    $r = $this->video->get($_GET['contentid'], '*', null,true,true);
		if (!$r)
		{
			$this->showmessage($this->survey->error());
		}
	  	if ($r['status'] != 6) $this->showmessage('数据状态错误，请先发布！');
	    $r['autostart'] = 'true';
		$videoext = array(
			'rm' => 'rmrmvb',
			'rmvb' => 'rmrmvb',
			'swf' => 'flash',
			'flv' => 'flv',
			'wmv' => 'wmv',
			'avi' => 'wmv'
		);
	    $fileext = fileext($r['video']);
		if(preg_match('/^(\[cc\])([^\[]+)(\[\/cc\])$/i', $r['video'], $matches)) 
		{
			$r['video'] = $matches[2];
			$r['player'] = 'cc';
		}
		elseif(preg_match('/^(\[ctvideo\])([^\[]+)(\[\/ctvideo\])$/i', $r['video'], $matches)) 
		{
			$ctv_setting = setting::get('video');
			$r['video'] = $matches[2];
			$r['player'] = 'ctvideo';
			$r['playerurl'] = $ctv_setting['player'];
		}
		elseif(array_key_exists($fileext, $videoext))
		{
			$r['player'] = $videoext[$fileext];	
		}
		else 
		{
			$r['player'] = 'flash';
		}
		$r['autostart'] = 'true';
		$this->template->assign($r);
		$code = $this->template->fetch('video/player/'.$r['player'].'.html', 'video');
		$this->view->assign('code', $code);
		$this->view->display('code');
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
        
		$data = $this->video->content->related($catid, $modelid, $keywords, $page, $pagesize);
		$result = $page == 1 ? array('state'=>true, 'data'=>$data, 'total'=>$this->video->related_total($catid, $modelid, $keywords)) : array('state'=>true, 'data'=>$data);
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
					$result = $this->article->reference($contentid, $cid);
					if (!$result) break;
				}
			}
			else
			{
				$result = $this->article->reference($contentid, $catid);
			}
			$result = $result ? array('state'=>true) : array('state'=>false, 'error'=>$this->article->error());
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
			$result = $this->video->move($contentid, $catid) ? array('state'=>true, 'contentid'=>$contentid) : array('state'=>false, 'error'=>$this->video->error());
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
     * 上传
     *
     * @aca 上传
     */
	function upload()
	{
		$attachment = loader::model('admin/attachment', 'system');
		$file = $attachment->upload('ctvideo',true, null,'swf|flv|avi|wmv|rm|rmvb',$this->upload_max_filesize,array());
		echo $file ? $attachment->aid[0].'|'.$file : '0';
	}

    /**
     * 定时上下线
     *
     * @aca cron 定时上下线
     */
	function cron()
	{
		@set_time_limit(600);
		
		$publishid = $this->video->content->cron_publish($this->modelid);
		if ($publishid) array_map(array($this->video, 'publish'),  $publishid);
		
		$unpublishid = $this->video->content->cron_unpublish($this->modelid);
		if ($unpublishid) array_map(array($this->video, 'unpublish'),  $unpublishid);
		
		exit ('{"state":true}');
	}

    /**
     * 设置 CC ID
     *
     * @aca 设置 CC ID
     */
	function setccid()
	{
		if($this->is_post())
		{
			$setting = new setting();
			if ($setting->set_array('system', $_POST))
			{
				$json = array('state'=>true,'info'=>'保存成功');
			}
			else
			{
				$json = array('state'=>false,'error'=>'保存失败');
			}
			exit ($this->json->encode($json));
		}
		else
		{
			$this->view->assign('ccid', setting('system', 'ccid'));
			$this->view->display('setting_cc');
		}
	}
}