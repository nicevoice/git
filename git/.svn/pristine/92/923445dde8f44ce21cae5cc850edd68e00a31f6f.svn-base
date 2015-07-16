<?php
/**
 * 投票管理
 *
 * @aca 投票管理
 */
class controller_admin_vote extends vote_controller_abstract
{
	private $vote, $modelid, $pagesize = 15, $weight = null;
	
	function __construct(& $app)
	{
		parent::__construct($app);
		$this->vote = loader::model('admin/vote');
		$this->weight = loader::model('admin/admin_weight', 'system');
		$this->modelid = $this->vote->modelid;
		
		if (isset($_REQUEST['catid'])) $this->priv_category($_REQUEST['catid']);
	}

	public function __call($method, $args)
	{
		if(in_array($method, array('delete', 'clear', 'remove', 'restore', 'restores', 'pass', 'reject', 'islock', 'lock', 'unlock', 'publish', 'unpublish'), true)) 
		{
			$var = in_array($method, array('clear', 'restores')) ? 'catid' : 'contentid';
			$result = $this->vote->$method($_REQUEST[$var]) ? array('state'=>true) : array('state'=>false, 'error'=>$this->vote->error());
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
			if ($contentid = $this->vote->add($_POST))
			{
				$result = array('state'=>true, 'contentid'=>$contentid,  'data'=>array('contentid'=>$contentid,'mode'=>$data['mode']));
				$article = $this->vote->get($contentid, 'url, status');
				$article['status'] == 6 && $result['url'] = $article['url'];
			}
			else
			{
				$result = array('state'=>false, 'error'=>$this->vote->error());
			}
			echo $this->json->encode($result);
		}
		else 
		{
			$myweight = array_shift($this->weight->get_by('userid', $this->_userid, 'weight'));
			$weight = $this->setting['weight'];
			$catid = $_GET['catid'];
			$data = array(	'weight' => $myweight ? (($myweight-$weight)>=0 ? $weight : $myweight) : 0,
							'status' => 6,
							'allowcomment'=>1,
							'type'=>'radio'
						);
			
			$this->view->assign($data);
			$this->view->assign('catname', $this->vote->category[$catid]['name']);
			$this->view->assign('head', array('title'=>'发布投票'));
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
			if ($this->vote->edit($_POST['contentid'], $_POST))
			{
				$result = array('state'=>true);
			}
			else 
			{
				$result = array('state'=>false, 'error'=>$this->vote->error());
			}
			echo $this->json->encode($result);
		}
		else 
		{
			$contentid = $_GET['contentid'];
			$myweight = array_shift($this->weight->get_by('userid', $this->_userid, 'weight'));
			$data = $this->vote->get($contentid, '*', 'get');
			if (!$data) $this->showmessage($this->vote->error());
			
			$this->priv_category($data['catid']);
			
			$this->vote->lock($contentid);
			$this->view->assign($data);
			$this->view->assign('myweight', $myweight);
			$this->view->assign('head', array('title'=>'编辑投票：'.$data['title']));
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
		$r = $this->vote->get($contentid, '*', 'view');
		if (!$r) $this->showmessage($this->vote->error());

		$this->priv_category($r['catid']);
		
		$head['title'] = $r['title'];
		$this->view->assign('head', $head);
		$this->view->assign($r);
		$this->view->display('view');
	}

    /**
     * 获取调用代码
     *
     * @aca 获取调用代码
     */
	function code()
	{
		$contentid = $_GET['contentid'];
		$r = $this->vote->get($contentid, '*', 'show');
		if (!$r) $this->showmessage($this->vote->error());

		$this->priv_category($r['catid']);
		
		$this->template->assign($r);
		$code = $this->template->fetch('vote/code.html');
		
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

		$data = $this->vote->content->related($catid, $modelid, $keywords, $page, $pagesize);
		$result = $page == 1 ? array('state'=>true, 'data'=>$data, 'total'=>$this->vote->content->related_total($catid, $modelid, $keywords)) : array('state'=>true, 'data'=>$data);
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
					$result = $this->vote->reference($contentid, $cid);
					if (!$result) break;
				}
			}
			else
			{
				$result = $this->vote->reference($contentid, $catid);
			}
			$result = $result ? array('state'=>true) : array('state'=>false, 'error'=>$this->vote->error());
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
			$result = $this->vote->move($contentid, $catid) ? array('state'=>true, 'contentid'=>$contentid) : array('state'=>false, 'error'=>$this->vote->error());
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
		
		$publishid = $this->vote->content->cron_publish($this->modelid);
		if ($publishid) array_map(array($this->vote, 'publish'),  $publishid);
		
		$unpublishid = $this->vote->content->cron_unpublish($this->modelid);
		if ($unpublishid) array_map(array($this->vote, 'unpublish'),  $unpublishid);
		
		exit ('{"state":true}');
	}
}