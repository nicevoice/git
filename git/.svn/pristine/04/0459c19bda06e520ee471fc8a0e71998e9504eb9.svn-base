<?php
/**
 * 专题管理
 *
 * @aca 专题管理
 */
final class controller_admin_special extends special_controller_abstract
{
	/**
	 * @var model_admin_special
	 */
	protected $special, $weight = null;
	function __construct(& $app)
	{
		parent::__construct($app);
		$this->special = loader::model('admin/special');
		$this->weight = loader::model('admin/admin_weight', 'system');
		if (isset($_REQUEST['catid'])) $this->priv_category($_REQUEST['catid']);
	}
	
	public function __call($method, $args)
	{
		if(in_array($method, array('delete', 'clear', 'remove', 'restore', 'restores', 'pass', 'reject', 'islock', 'lock', 'unlock', 'publish', 'unpublish'), true)) 
		{
			$var = in_array($method, array('clear', 'restores')) ? 'catid' : 'contentid';
			$result = $this->special->$method($_REQUEST[$var]) ? array('state'=>true) : array('state'=>false, 'error'=>$this->special->error());
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
        // 专题数量授权
        $specials = factory::db()->get("SELECT COUNT(*) AS `total` FROM `#table_special`");
        if ($specials && ! license('system', array('specials' => (intval($specials['total']) + 1))))
        {
            cmstop::licenseFailure('系统中的专题数超出了您的授权数量');
        }

		if ($this->is_post())
		{
			if (! ($contentid = $this->special->add($_POST)))
			{
				$json = array(
					'state'=>false,
					'error'=>$this->special->error()
				);
			}
			else
			{
				$json = array(
					'state'=>true,
					'redirect'=>"?app=special&controller=online&action=design&contentid={$contentid}"
				);
			}
			exit ($this->json->encode($json));
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
				'catid'=>$catid,
				'catname'=>$this->special->category[$catid]['name']
			);
			$this->view->assign($data);
			$this->view->assign('head', array('title'=>'发布专题'));
			$this->view->assign('repeatcheck', value(setting::get('system'), 'repeatcheck', 0));
			$this->view->display('add');
		}
	}

    /**
     * 查看
     *
     * @aca 查看
     */
	public function view()
	{
		if (!($contentid = intval($_REQUEST['contentid']))
			|| !($data = $this->special->get($_GET['contentid'], '*', 'view')))
		{
			$this->showmessage('专题不存在');
		}
		$this->priv_category($data['catid']);
		
		$this->view->assign($data);        
		$this->view->assign('head', array('title'=>$data['title']));
		$this->view->display('view');
	}

    /**
     * 编辑
     *
     * @aca 编辑
     */
	public function edit()
	{
		if (!($contentid = intval($_REQUEST['contentid'])))
		{
			$this->_errorOut('专题不存在');
		}
		if ($this->is_post())
		{
			if ($this->special->edit($contentid, $_POST))
			{
				exit ('{"state":true}');
			}
			else
			{
				exit ($this->json->encode(array(
					'state'=>false,
					'error'=>$this->special->error()
				)));
			}
		}
		else
		{
			$myweight = array_shift($this->weight->get_by('userid', $this->_userid, 'weight'));
			$data = $this->special->get($contentid, '*', 'get');
			if (!$data)
			{
				$this->_errorOut('专题不存在');
			}
			$this->priv_category($data['catid']);
			
			$this->special->lock($contentid);
			$this->view->assign($data);
			$this->view->assign('myweight', $myweight);
			$this->view->assign('head', array('title'=>'编辑专题：'.$data['title']));
			$this->view->display('edit');
		}
	}

    /**
     * 搜索
     *
     * @aca 搜索
     */
	public function search()
	{
		$page = intval($_GET['page']);
		if ($page < 1)
		{
			$page = 1;
		}
		$size = 10;
		$where = array('b.status=6');
		if ($_GET['range'])
		{
			// $j 月份中的第几天，没有前导零
			// $N 星期中的第几天
			// $n 数字表示的月份，没有前导零
			// $Y 4 位数字完整表示的年份
			list($j, $N, $n, $Y) = explode(',', date('j,N,n,Y'));
			// 今日 >= mktime(0, 0, 0, $n, $j, $Y)
			if ($_GET['range'] == 'today')
			{
				$where[] = 'b.published>='.mktime(0, 0, 0, $n, $j, $Y);
			}
			// mktime(0, 0, 0, $n, $j-1, $Y) =< 昨日 < mktime(0, 0, 0, $n, $j, $Y)
			elseif ($_GET['range'] == 'tomorrow')
			{
				$where[] = 'b.published>='.mktime(0, 0, 0, $n, $j-1, $Y).' AND b.published<'.mktime(0, 0, 0, $n, $j, $Y);
			}
			// 本周 >= mktime(0, 0, 0, $n, $j - $N + 1, $Y)
			elseif ($_GET['range'] == 'week')
			{
				$where[] = 'b.published>='.mktime(0, 0, 0, $n, $j - $N + 1, $Y);
			}
			// 本月 >= mktime(0, 0, 0, $n, 1, $Y)
			elseif ($_GET['range'] == 'month')
			{
				$where[] = 'b.published>='.mktime(0, 0, 0, $n, 1, $Y);
			}
		}
		if (trim($_GET['keywords']))
		{
			$where[] = where_keywords('b.title', $_GET['keywords']);
		}
		$where = $where ? ('WHERE '.implode(' AND ', $where)) : '';
		$db = factory::db();
		$data = $db->page("SELECT b.contentid, b.title, b.url FROM #table_special a LEFT JOIN #table_content b ON b.contentid=a.contentid $where", $page, $size);
		$r = $db->get("SELECT count(*) as total FROM #table_special a LEFT JOIN #table_content b ON b.contentid=a.contentid $where");
		$total = $r['total'];
		$json = array('state'=>true, 'data'=>$data, 'total'=>$total);
		exit ($this->json->encode($json));
	}
	
	protected function _errorOut($msg)
	{
		if (stristr($_SERVER['HTTP_ACCEPT'], 'json'))
		{
			exit ($this->json->encode(array('state'=>false,'error'=>$msg)));
		}
		else
		{
			$this->showmessage($msg);
		}
	}
	
	/**
     * 定时上下线
     *
     * @aca cron 定时上下线
     */
	function cron()
	{
		@set_time_limit(600);
		
		$publishid = $this->special->content->cron_publish($this->speical->modelid);
		if ($publishid) array_map(array($this->special, 'publish'),  $publishid);
		
		$unpublishid = $this->special->content->cron_unpublish($this->special->modelid);
		if ($unpublishid) array_map(array($this->special, 'unpublish'),  $unpublishid);
		
		exit ('{"state":true}');
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
			$result = $this->special->move($contentid, $catid) ? array('state'=>true, 'contentid'=>$contentid) : array('state'=>false, 'error'=>$this->special->error());
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
}
