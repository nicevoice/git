<?php
/**
 * 活动管理
 *
 * @aca 活动管理
 */
class controller_admin_activity extends activity_controller_abstract
{
	private $activity, $pagesize = 15, $modelid, $weight = null;

	function __construct(& $app)
	{
		parent::__construct($app);

		$this->activity = loader::model('admin/activity');
		$this->weight = loader::model('admin/admin_weight', 'system');
		$this->modelid = $this->activity->modelid;
		
		if (isset($_REQUEST['catid'])) $this->priv_category($_REQUEST['catid']);
	}

	public function __call($method, $args)
	{
		if(in_array($method, array('delete', 'clear', 'remove', 'restore', 'restores', 'approve', 'pass', 'reject', 'islock', 'lock', 'unlock', 'publish', 'unpublish'), true)) 
		{
			$var = in_array($method, array('clear', 'restores')) ? 'catid' : 'contentid';
			$result = $this->activity->$method($_REQUEST[$var]) ? array('state'=>true) : array('state'=>false, 'error'=>$this->activity->error());
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
			if ($contentid = $this->activity->add($_POST))
			{
				$result = array('state'=>true, 'contentid' => $contentid);
				$article = $this->activity->get($contentid, 'url, status');
				$article['status'] == 6 && $result['url'] = $article['url'];
			}
			else
			{
				$result = array('state'=>false, 'error'=>$this->activity->error());
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
			              'baidumapkey'=>setting('system','baidumapkey')
			             );
			$this->view->assign($data);
			$this->view->assign('catname', $this->activity->category[$catid]['name']);
			$this->view->assign('head', array('title'=>'发布活动'));
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
			
			if ($contentid = $this->activity->edit($_POST['contentid'], $_POST))
			{
				 $result = array('state'=>true);
			}
			else
			{
				$result = array('state'=>false, 'error'=>$this->activity->error());
			}
			echo $this->json->encode($result);
		}
		else
		{
			$myweight = array_shift($this->weight->get_by('userid', $this->_userid, 'weight'));
			$contentid = $_GET['contentid'];
			$data = $this->activity->get($contentid, '*', 'get');
			if (!$data) $this->showmessage($this->activity->error());
			
			$this->priv_category($data['catid']);
			
			$this->activity->lock($contentid);
			$data['baidumapkey'] = setting('system','baidumapkey');
			$this->view->assign($data);
			$this->view->assign('myweight', $myweight);
			$this->view->assign('head', array('title'=>'编辑活动：'.$data['title']));
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
		$r = $this->activity->get($_GET['contentid'], '*', 'view');
		if (!$r) $this->showmessage($this->activity->error());
		
		$this->priv_category($r['catid']);
		
		$this->view->assign($r);        
		$this->view->assign('head', array('title'=>$r['title']));
		$this->view->display('view');
	}

    /**
     * 查看报名信息
     *
     * @aca 查看报名信息
     */
	function viewsigns()
	{  
		$state = isset($_GET['state']) ? intval($_GET['state']) : 0;
		$statename = $state?'已审核':'待审核';
		$this->view->assign('state', $state);
		
		$data = $this->activity->get($_GET['contentid'],'status,catid,title,selected,required',null,true);
		
		$this->priv_category($data['catid']);
		
		$head['title'] = $data['title'].'_'.$statename.'报名者';
		$this->view->assign('fields', $data['fields']);
		$this->view->assign('status', $data['status']);
		$this->view->assign('head', $head);
		$this->view->display('sign');
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

		$data = $this->activity->content->related($catid, $modelid, $keywords, $page, $pagesize);
		$result = $page == 1 ? array('state'=>true, 'data'=>$data, 'total'=>$this->activity->content->related_total($catid, $modelid, $keywords)) : array('state'=>true, 'data'=>$data);
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
					$result = $this->activity->reference($contentid, $cid);
					if (!$result) break;
				}
			}
			else
			{
				$result = $this->activity->reference($contentid, $catid);
			}
			$result = $result ? array('state'=>true) : array('state'=>false, 'error'=>$this->activity->error());
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
			$result = $this->activity->move($contentid, $catid) ? array('state'=>true, 'contentid'=>$contentid) : array('state'=>false, 'error'=>$this->activity->error());
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
     * 结束活动
     *
     * @aca 是否结束
     */
	function stop()
	{
		$contentid = intval($_GET['contentid']);
		if($this->activity->stop($contentid))
		{
			$result = array('state'=>true, 'data'=>$data);
		}
		else 
		{
			$result = array('state'=>false, 'error'=>$this->activity->error());
		}
		echo $this->json->encode($result);
	}

    /**
     * 开始活动
     *
     * @aca 是否结束
     */
	function unstop()
	{
		$contentid = intval($_GET['contentid']);
		if($this->activity->unstop($contentid))
		{
			$result = array('state'=>true, 'data'=>$data);
		}
		else 
		{
			$result = array('state'=>false, 'error'=>$this->activity->error());
		}
		echo $this->json->encode($result);
	}

    /**
     * 定时上下线
     *
     * @aca 定时上下线
     */
	function cron()
	{
		@set_time_limit(600);
		
		$publishid = $this->activity->content->cron_publish($this->modelid);
		if ($publishid) array_map(array($this->activity, 'publish'),  $publishid);
		
		$unpublishid = $this->activity->content->cron_unpublish($this->modelid);
		if ($unpublishid) array_map(array($this->activity, 'unpublish'),  $unpublishid);
		
		exit ('{"state":true}');
	}
}