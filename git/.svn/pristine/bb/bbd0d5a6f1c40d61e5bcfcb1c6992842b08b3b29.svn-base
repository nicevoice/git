<?php
/**
 * 链接
 *
 * @aca 链接
 */
class controller_admin_link extends link_controller_abstract
{
	private $link, $content, $pagesize = 15, $modelid, $weight = null;

	function __construct(& $app)
	{
		parent::__construct($app);
		$this->link		= loader::model('admin/link');
		$this->content = loader::model('admin/content', 'system');
		$this->weight = loader::model('admin/admin_weight', 'system');
		$this->modelid = modelid('link');
		
		if (isset($_REQUEST['catid'])) $this->priv_category($_REQUEST['catid']);
	}
	
	public function __call($method, $args)
	{
		if(!priv::aca('link', 'link', $method)) return true;
		if(in_array($method, array('clear', 'remove', 'restore', 'restores', 'approve', 'pass', 'reject', 'islock', 'lock', 'unlock', 'publish', 'unpublish'), true))
		{
			$var = in_array($method, array('clear', 'restores')) ? 'catid' : 'contentid';
			$result = $this->content->$method($_REQUEST[$var]) ? array('state'=>true) : array('state'=>false, 'error'=>$this->content->error());
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
			if ($contentid = $this->link->add($_POST))
			{
				if($_POST['options']['catid']) //同时发到其他栏目
				{
					$catids = explode(',', $_POST['options']['catid']);
					foreach ($catids as $catid)
					{
						$this->content->reference($contentid, $catid);
					}
				}
				$result = array('state'=>true, 'contentid' => $contentid);
			}
			else
			{
				$result = array('state'=>false, 'error'=>$this->content->error());
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
			              'editor'=>$this->_username,
			              'allowcomment'=>1,
			              'saveremoteimage'=>1,
			             );

			$this->view->assign($data);
			$this->view->assign('catname', $this->content->category[$catid]['name']);
			$this->view->assign('related_apis', table('related_api'));
			$this->view->assign('head', array('title'=>'发布链接'));
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
			if ($this->link->edit($_POST['contentid'], $_POST))
			{
				$result = array('state'=>true,'info' =>'success');
			}
			else
			{
				$result = array('state'=>false, 'error'=>$this->content->error());
			}
			echo $this->json->encode($result);
		}
		else
		{
			$myweight = array_shift($this->weight->get_by('userid', $this->_userid, 'weight'));
			$contentid = $_GET['contentid'];
			$data = $this->link->get($contentid);
			if (!$data) $this->showmessage($this->content->error());
			
			$this->priv_category($data['catid']);
			
			$this->content->lock($contentid);
			
			$this->view->assign($data);
			$this->view->assign('myweight', $myweight);
			$this->view->assign('head', array('title'=>'编辑链接：'.$data['title']));
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
		$r = $this->content->get($_GET['contentid'], '*', 'view');
		if (!$r) $this->showmessage($this->content->error());
		
		$this->priv_category($r['catid']);
		
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

		$data = $this->content->content->related($catid, $modelid, $keywords, $page, $pagesize);
		$result = $page == 1 ? array('state'=>true, 'data'=>$data, 'total'=>$this->content->content->related_total($catid, $modelid, $keywords)) : array('state'=>true, 'data'=>$data);
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
					$result = $this->content->reference($contentid, $cid);
					if (!$result) break;
				}
			}
			else
			{
				$result = $this->content->reference($contentid, $catid);
			}
			$result = $result ? array('state'=>true) : array('state'=>false, 'error'=>$this->content->error());
			echo $this->json->encode($result);
		}
		else 
		{
			$category = table('category');
			import('helper.treeview');
			$treeview = new treeview($category);
			$data = $treeview->get(null, 'category_tree', '<li><span id="{$catid}"><input type="checkbox" name="catid[]" value="{$catid}" class="checkbox_style" />{$name}</span>{$child}</li>');
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
			$result = $this->link->move($contentid, $catid) ? array('state'=>true, 'contentid'=>$contentid) : array('state'=>false, 'error'=>$this->link->error());
			echo $this->json->encode($result);
		}
		else 
		{
			$category = table('category');
			import('helper.treeview');
			$treeview = new treeview($category);
			$data = $treeview->get(null, 'category_tree', '<li><span id="{$catid}"><input type="radio" name="catid" value="{$catid}" class="radio_style" />{$name}</span>{$child}</li>');
			$this->view->assign('data', $data);
			$this->view->display('content/move', 'system');
		}
	}

    /**
     * 删除
     *
     * @aca 删除
     */
	public function delete()
	{
		$contentid = $_REQUEST['contentid'];
		echo $this->json->encode(array('state'=>$this->link->delete($contentid)));
	}

    /**
     * 定时上下线
     *
     * @aca 定时上下线
     */
	function cron()
	{
		@set_time_limit(600);
		
		$publishid = $this->content->cron_publish($this->modelid);
		$unpublishid = $this->content->cron_unpublish($this->modelid);
		
		exit ('{"state":true}');
	}
}