<?php
/**
 * 访谈管理
 *
 * @aca 访谈管理
 */
final class controller_admin_interview extends interview_controller_abstract
{
	private $interview, $pagesize = 15, $modelid, $weight = null;
	
	function __construct(& $app)
	{
		parent::__construct($app);
		if (!license('interview')) cmstop::licenseFailure();
		$this->interview = loader::model('admin/interview');
		$this->weight = loader::model('admin/admin_weight', 'system');
		$this->modelid = $this->interview->modelid;
		
		if (isset($_REQUEST['catid'])) $this->priv_category($_REQUEST['catid']);
	}
	
	public function __call($method, $args)
	{
		if(!priv::aca('interview', 'interview', $method)) return true;
		if(in_array($method, array('delete', 'clear', 'remove', 'restore', 'restores', 'pass', 'reject', 'islock', 'lock', 'unlock', 'publish', 'unpublish'), true)) 
		{
			$var = in_array($method, array('clear', 'restores')) ? 'catid' : 'contentid';
			$result = $this->interview->$method($_REQUEST[$var]) ? array('state'=>true) : array('state'=>false, 'error'=>$this->interview->error());
			echo $this->json->encode($result);
        }
	}

    /**
     * 浏览
     *
     * @aca 浏览
     */
	function index()
	{
		$status = isset($_GET['status']) ? intval($_GET['status']) : 6;
		$this->view->assign('status', $status);
		
		$statuss = table('status');
		arsort($statuss);
		$this->view->assign('statuss', $statuss);
		
		$head['title'] = table('category', $_GET['catid'], 'name').'_'.table('status', $status, 'name').table('model', $this->interview->modelid, 'name');
		$this->view->assign('head', $head);
		$this->view->display('index');
	}

    /**
     * 列表
     *
     * @aca 浏览
     */
	function page()
	{
		$catid = intval($_GET['catid']);
		$status = isset($_GET['status']) ? intval($_GET['status']) : 6;
		$page = isset($_GET['page']) ? intval($_GET['page']) : 0;
		$pagesize = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : $this->pagesize;
		$where = $_GET;
		$fields = '*';
		$order = isset($_GET['orderby']) ? str_replace('|', ' ', $_GET['orderby']) : ($status >= 5 ? '`published` DESC' : '`contentid` DESC');

		$data = $this->interview->ls($where, $fields, $order, $page, $pagesize);
		
		$result = array('total'=>$this->interview->total, 'data'=>$data);
		echo $this->json->encode($result);
	}

    /**
     * 搜索
     *
     * @aca 浏览
     */
	function search()
	{
		$catid = isset($_GET['catid']) ? intval($_GET['catid']) : 0;
		$modelid = isset($_GET['modelid']) ? intval($_GET['modelid']) : 0;
		$status = isset($_GET['status']) ? intval($_GET['status']) : 0;

		$this->view->assign('catname', $this->interview->category[$catid]['name']);
		$this->view->assign('modelname', table('model', $modelid, 'name'));
		$this->view->assign('statusname', table('status', $status, 'name'));
		$this->view->display('search');
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
			if ($contentid = $this->interview->add($_POST))
			{
				$result = array('state'=>true, 'contentid' => $contentid);
				$article = $this->interview->get($contentid, 'url, status');
				$article['status'] == 6 && $result['url'] = $article['url'];
			}
			else
			{
				$result = array('state'=>false, 'error'=>$this->interview->error());
			}
			echo $this->json->encode($result);
		}
		else
		{
			$catid = $_GET['catid'];
			$weight = $this->setting['weight'];
			$myweight = array_shift($this->weight->get_by('userid', $this->_userid, 'weight'));
			$data = array('status'=>6,
			              'weight' => $myweight ? (($myweight-$weight)>=0 ? $weight : $myweight) : 0,
			              'editor'=>table('admin', $this->_userid, 'name'),
			              'allowchat'=>1,
			              'allowcomment'=>1,
			              'mode'=>'text'
			              );
			
		    $this->view->assign($data);
            $this->view->assign('catname', $this->content->category[$catid]['name']);
		    $this->view->assign('head', array('title'=>'发布访谈'));
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
			if ($this->interview->edit($_POST['contentid'], $_POST))
			{
				$result = array('state'=>true);
			}
			else 
			{
				$result = array('state'=>false, 'error'=>$this->interview->error());
			}
			echo $this->json->encode($result);
		}
		else 
		{
			$myweight = array_shift($this->weight->get_by('userid', $this->_userid, 'weight'));
			$contentid = $_GET['contentid'];
			$data = $this->interview->get($contentid, '*', 'get');
			if (!$data) $this->showmessage($this->interview->error());
			
			$this->priv_category($data['catid']);
			
			$this->interview->lock($contentid);
			
			$this->view->assign($data);
			$this->view->assign('myweight', $myweight);
			$this->view->assign('head', array('title'=>'编辑访谈：'.$data['title']));
			$this->view->display('edit');
		}
	}

    /**
     * 查看
     *
     * @aca 浏览
     */
	function view()
	{
		$contentid = $_GET['contentid'];
		$r = $this->interview->get($contentid, '*', 'view');
		if (!$r) $this->showmessage($this->interview->error());
		
		$this->priv_category($r['catid']);
		
		$r['autostart'] = 'false';
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
		elseif(array_key_exists($fileext, $videoext))
		{
			$r['player'] = $videoext[$fileext];	
		}
		else 
		{
			$r['player'] = 'flash';
		}
		$this->view->assign($r);
		
		$head['title'] = $r['title'];
		$this->view->assign('head', $head);
		$this->view->display('view');
	}

    /**
     * 精彩观点
     *
     * @aca 精彩观点
     */
	function review()
	{
		if ($this->is_post())
		{
			$contentid = $_POST['contentid'];
			
			$this->priv_category(table('content', $contentid, 'catid'));
			
			$result = $this->interview->review($contentid, $_POST['review']) ? array('state'=>true, 'data'=>$_POST['review']) : array('state'=>false, 'error'=>$this->interview->error());
			echo $this->json->encode($result);
		}
		else 
		{
			$contentid = $_GET['contentid'];
			$r = $this->interview->get($contentid, '`catid`,`review`');
			if (!$r) $this->showmessage($this->interview->error());
			
			$this->priv_category($r['catid']);
			
			$this->view->assign('review', $r['review']);
			$this->view->display('review');
		}
	}

    /**
     * 滚动广告
     *
     * @aca 滚动广告
     */
	function notice()
	{
		if ($this->is_post())
		{
			$contentid = $_POST['contentid'];
			
			$this->priv_category(table('content', $contentid, 'catid'));
			
			$result = $this->interview->notice($contentid, $_POST['notice']) ? array('state'=>true, 'data'=>$_POST['notice']) : array('state'=>false, 'error'=>$this->interview->error());
			echo $this->json->encode($result);
		}
		else 
		{
			$contentid = $_GET['contentid'];
			$r = $this->interview->get($contentid, '`catid`,`notice`');
			if (!$r) $this->showmessage($this->interview->error());
			
			$this->priv_category($r['catid']);
			
			$this->view->assign('notice', $r['notice']);
			$this->view->display('notice');
		}
	}

    /**
     * 组图
     *
     * @aca 组图
     */
	function picture()
	{
		if ($this->is_post())
		{
			$this->priv_category(table('content', $_POST['contentid'], 'catid'));
			
			if ($this->interview->picture($_POST['contentid'], $_POST['picture']))
			{
	             $result = array('state'=>true);
			}
			else 
			{
				$result = array('state'=>false, 'error'=>$this->interview->error());
			}
			echo $this->json->encode($result);
		}
		else 
		{
			$contentid = $_GET['contentid'];
			$r = $this->interview->get($contentid, '`catid`,`picture`');
			if (!$r) $this->showmessage($this->interview->error());
			
			$this->priv_category($r['catid']);
			
			$this->view->assign('picture', $r['picture']);
			$this->view->display('picture');
		}
	}

    /**
     * 访谈状态
     *
     * @aca 访谈状态
     */
	function state()
	{
		$this->priv_category(table('content', $_REQUEST['contentid'], 'catid'));
		
		if ($this->interview->state($_REQUEST['contentid'], $_REQUEST['state']))
		{
             $result = array('state'=>true);
		}
		else 
		{
			$result = array('state'=>false, 'error'=>$this->interview->error());
		}
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
					$result = $this->interview->reference($contentid, $cid);
					if (!$result) break;
				}
			}
			else
			{
				$result = $this->interview->reference($contentid, $catid);
			}
			$result = $result ? array('state'=>true) : array('state'=>false, 'error'=>$this->interview->error());
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
			$result = $this->interview->move($contentid, $catid) ? array('state'=>true, 'contentid'=>$contentid) : array('state'=>false, 'error'=>$this->interview->error());
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
     * 定时上下线
     *
     * @aca 定时上下线
     */
	function cron()
	{
		@set_time_limit(600);
		
		$publishid = $this->interview->content->cron_publish($this->modelid);
		if ($publishid) array_map(array($this->interview, 'publish'),  $publishid);
		
		$unpublishid = $this->interview->content->cron_unpublish($this->modelid);
		if ($unpublishid) array_map(array($this->interview, 'unpublish'),  $unpublishid);
		
		exit ('{"state":true}');
	}

    /**
     * 上传
     *
     * @aca 上传
     */
	function upload()
	{
		$attachment = loader::model('admin/attachment', 'system');
		$file = $attachment->upload('Filedata', true, null, 'jpg|jpeg|gif|png|bmp', 2048);
		echo $file ? $attachment->aid[0].'|'.$file : '0';
	}	
}