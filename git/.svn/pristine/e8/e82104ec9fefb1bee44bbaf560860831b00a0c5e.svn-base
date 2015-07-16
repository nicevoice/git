<?php
/**
 * 文章管理
 *
 * @aca 文章
 */
class controller_admin_article extends article_controller_abstract
{
	private $article, $pagesize = 15, $modelid, $weight = null;

	function __construct(& $app)
	{
		parent::__construct($app);
		$this->article = loader::model('admin/article');
		$this->weight = loader::model('admin/admin_weight', 'system');
		$this->modelid = $this->article->modelid;
		
		if (isset($_REQUEST['catid'])) $this->priv_category($_REQUEST['catid']);
	}

	public function __call($method, $args)
	{
		if(in_array($method, array('delete', 'clear', 'remove', 'restore', 'restores', 'approve', 'pass', 'reject', 'islock', 'lock', 'unlock', 'publish', 'unpublish'), true)) 
		{
			$var = in_array($method, array('clear', 'restores')) ? 'catid' : 'contentid';
			$result = $this->article->$method($_REQUEST[$var]) ? array('state'=>true) : array('state'=>false, 'error'=>$this->article->error());
			$r	= $this->json->encode($result);
			echo $_GET['jsoncallback'] ? $_GET['jsoncallback']."($r)" : $r;
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
			if ($contentid = $this->article->add($_POST))
			{
				$result = array('state'=>true, 'contentid' => $contentid);
				$article = $this->article->get($contentid, 'url, status');
				$article['status'] == 6 && $result['url'] = $article['url'];
			}
			else
			{
				$result = array('state'=>false, 'error'=>$this->article->error());
				if($this->article->filterword)
				{
					$result['filterword'] = $this->article->filterword;
				}
			}
			echo $this->json->encode($result);
		}
		else
		{
			$myweight = array_shift($this->weight->get_by('userid', $this->_userid, 'weight'));
			$weight = $this->setting['weight'];
			$catid = $_GET['catid'];

			$data = array('status'=>6,
                'weight' => $myweight ? (($myweight-$weight)>=0 ? $weight : $myweight) : 0,
                'source'=>$this->setting['source'],
                'editor'=>table('admin', $this->_userid, 'name'),
                'allowcomment'=>1,
                'saveremoteimage'=>1,
            );

			if (! empty($_GET['source']))
			{
				// 加载智能采集模型
				$smarter = loader::model("admin/smarter", "spider");
				// 获取该URL的信息
				$info = $smarter->getInfo($_GET['source']);
				$title = $content = '';
				if($info['state'])
				{
					// 循环分页，组合出最终的内容
					foreach ($info['data'] as $k=>$v) 
					{
						if ($k == 0)
						{
							$title = $v['title'];
						}
						if (count($info['data']) > 1)
						{
							$content = $content .'<p class="mcePageBreak">&nbsp;</p>' .$v['content'];
						}
						else
						{
							$content = $v['content'];
						}
					}
				}
				// 并数据放入data，并释放掉刚才的临时变量
				$data['title'] = $title;
				$data['content'] = $content;
				unset($info, $title, $content);
			}

			$this->view->assign($data);
			$this->view->assign('catname', $this->article->category[$catid]['name']);
			$this->view->assign('head', array('title'=>'发布文章'));
			$this->view->assign('repeatcheck', value(setting::get('system'), 'repeatcheck', 0));
			$this->view->display('add');
		}
	}

    /**
     * 编辑工具箱里的添加
     *
     * @aca 添加
     */
	function miniadd()
	{
		$source = loader::model('admin/source', 'system');
		$myweight = array_shift($this->weight->get_by('userid', $this->_userid, 'weight'));
		$data = array('status'=>6,
		  'weight'=>$myweight ? $myweight : $this->setting['weight'],
		  'source'=>$this->setting['source'],
		  'editor'=>table('admin', $this->_userid, 'name'),
		  'allowcomment'=>1,
		  'saveremoteimage'=>1,
		);
		if (! empty($_GET['source']))
		{
			$url = $_GET['source'];
			$parse_url	= parse_url($url);
			$domain		= explode('.', $parse_url['host']);
			array_shift($domain);
			$domain		= implode('.', $domain);
			$from = $source->get("`url` LIKE '%$domain%'", 'name');
			!$from && $from['name']	= $domain;
			$smarter = loader::model('admin/smarter', 'spider');

			$info = $smarter->getInfo($_GET['source']);
			$title = $content = '';
			if($info['state'])
			{
				// 循环分页，组合出最终的内容
				foreach ($info['data'] as $k=>$v) 
				{
					if ($k == 0)
					{
						$title = $v['title'];
					}
					if (count($info['data']) > 1)
					{
						$content = $content .'<p class="mcePageBreak">&nbsp;</p>' .$v['content'];
					}
					else
					{
						$content = $v['content'];
					}
				}
			}
			// 并数据放入data，并释放掉刚才的临时变量
			$data['content']	= $content;
			$data['title']		= $title;
			unset($info, $title, $content);
			$data['source']		= $from['name'];
			$this->view->assign($data);
			$this->view->assign('catname', $this->article->category[$catid]['name']);
			$this->view->assign('head', array('title'=>'发布文章:'.$title));
			$this->view->assign('repeatcheck', value(setting::get('system'), 'repeatcheck', 0));
			$this->view->display('miniadd');
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
			if ($this->article->edit($_POST['contentid'], $_POST))
			{
				$url	= $this->article->content->get_field('url', $_POST['contentid']);
				$result = array('state'=>true,'contentid'=>$_POST[contentid], 'url'=>$url);
			}
			else
			{
				$result = array('state'=>false, 'error'=>$this->article->error());
				if($this->article->filterword)
				{
					$result['filterword'] = $this->article->filterword;
				}
			}
			echo $this->json->encode($result);
		}
		else
		{
			$contentid = intval($_GET['contentid']);
			if (!$contentid && !empty($_GET['url']))
			{
				$source = parse_url($_GET['url']);
				$source['path'] = preg_replace('/^(.*)(?:_\d{1,3})(\.\w{3,6})/','\1\2', $source['path']);
				$key = $source['scheme'].'://'.$source['host'].$source['path'];
				$data = $this->article->content->select("url='$key'",'contentid', null, 1);
				if (! $data)
				{
					$this->showmessage('不是本站文章，无法编辑');
				}
				$contentid = $data[0]['contentid'];
			}
			$myweight = array_shift($this->weight->get_by('userid', $this->_userid, 'weight'));
			$data = $this->article->get($contentid, '*', 'get');
			if (! $data)
			{
				$this->showmessage('不存在此文章');
			}
			
			$this->priv_category($data['catid']);
			
			$this->article->lock($contentid);
			$this->view->assign($data);
			$this->view->assign('myweight', $myweight);
			$this->view->assign('head', array('title'=>'编辑文章：'.$data['title']));
			$this->view->display('edit');
		}
	}

    /**
     * 网编工具箱里的编辑
     *
     * @aca 编辑
     */
	function miniedit()
	{
		$contentid = intval($_GET['contentid']);
		if (!$contentid && !empty($_GET['url']))
		{
			$source = parse_url($_GET['url']);
			$source['path'] = preg_replace('/^(.*)(?:_\d{1,3})(\.\w{3,6})/','\1\2', $source['path']);
			$key = $source['scheme'].'://'.$source['host'].$source['path'];
			$data = $this->article->content->select("url='$key'",'contentid', null, 1);
			if (! $data)
			{
				$this->showmessage('不是本站文章，无法编辑');
			}
			$contentid = $data[0]['contentid'];
		}
		$myweight = array_shift($this->weight->get_by('userid', $this->_userid, 'weight'));
		$data = $this->article->get($contentid, '*', 'get');
		if (! $data)
		{
			$this->showmessage('不存在此文章');
		}
		
		$this->priv_category($data['catid']);
		
		$this->article->lock($contentid);
		
		$this->view->assign($data);
		$this->view->assign('myweight', $myweight);
		$this->view->assign('head', array('title'=>'编辑文章：'.$data['title']));
		$this->view->display('miniedit');
	}

    /**
     * 查看
     *
     * @aca 查看
     */
	function view()
	{
		$r = $this->article->get($_GET['contentid'], '*', 'view');
		if (!$r) $this->showmessage($this->article->error());

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

		$data = $this->article->content->related($catid, $modelid, $keywords, $page, $pagesize);
		$result = $page == 1 ? array('state'=>true, 'data'=>$data, 'total'=>$this->article->content->related_total($catid, $modelid, $keywords)) : array('state'=>true, 'data'=>$data);
		echo $this->json->encode($result);
	}

    /**
     * 复制
     *
     * @aca 复制
     */
	function copy()
	{
		if ($this->is_post())
		{
			$contentid = $_REQUEST['contentid'];
			$catid = $_REQUEST['catid'];
			if (is_array($catid))
			{
				foreach ($catid as $cid)
				{					
					$result = $this->article->copy($contentid, $cid);
					if (!$result) break;
				}
			}
			else
			{			
				$result = $this->article->copy($contentid, $catid);
			}
			$result = $result ? array('state'=>true) : array('state'=>false, 'error'=>$this->article->error());
			echo $this->json->encode($result);
		}
		else
		{
			$this->view->display('content/copy', 'system');
		}
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
			$data = $treeview->get(null, 'category_tree', '<li><span id="{$catid}"><label>{$checkbox}{$name}</label></span>{$child}</li>');
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
			$result = $this->article->move($contentid, $catid) ? array('state'=>true, 'contentid'=>$contentid) : array('state'=>false, 'error'=>$this->article->error());
			echo $this->json->encode($result);
		}
		else 
		{
			$category = table('category');
			foreach ($category as $k=>$c)
			{
				$category[$k]['radio'] = '<input type="radio" name="catid" value="'.$c['catid'].'" class="radio_style" />';
				if (!priv::category($c['catid']))
				{
					if (priv::category($c['catid'], true))
					{
						$category[$k]['radio'] = '';
					}
					else 
					{
						unset($category[$k]);
						continue;
					}
				}
				elseif ($c['childids'])
				{
					$category[$k]['radio'] = '';
				}
			}
			import('helper.treeview');
			$treeview = new treeview($category);
			$data = $treeview->get(null, 'category_tree', '<li><span id="{$catid}"><label>{$radio}{$name}</label></span>{$child}</li>');
			$this->view->assign('data', $data);
			$this->view->display('content/move', 'system');
		}
	}

    /**
     * 缩略图
     *
     * @aca 缩略图
     */
	function thumb()
	{
		$uri = $_POST['url'];
		if(preg_match("#^".UPLOAD_URL."#", $uri))
		{
			$pathinfo = parse_url($uri);
			$originfile = substr($pathinfo['path'],1);
			$file = date('Y/md/').TIME.mt_rand(100, 999).'.'.pathinfo($uri, PATHINFO_EXTENSION);
			@copy(UPLOAD_PATH.$originfile, UPLOAD_PATH.$file);
		}
		else 
		{
			$attachment = loader::model('admin/attachment', 'system');
			$file = $attachment->download_by_file($uri, null, null, null, true);
		}
		echo $file;
	}

    /**
     * 定时上下线
     *
     * @aca 定时上下线
     */
	function cron()
	{
		@set_time_limit(600);
		
		$publishid = $this->article->content->cron_publish($this->modelid);
		if ($publishid) array_map(array($this->article, 'publish'),  $publishid);
		
		$unpublishid = $this->article->content->cron_unpublish($this->modelid);
		if ($unpublishid) array_map(array($this->article, 'unpublish'),  $unpublishid);
		
		exit ('{"state":true}');
	}
}