<?php
/**
 * 采集
 *
 * @aca whole 采集
 */
class controller_admin_spider extends spider_controller_abstract
{
	protected $spider, $spider_task, $weight;
	public function __construct(&$app)
	{
		parent::__construct($app);
		$this->spider = loader::model('admin/spider', 'spider');
		$this->spider_task = loader::model('admin/spider_task', 'spider');

        $weight_max = intval(table('admin_weight', $this->_userid, 'weight'));
        if (! ($weight = setting('article', 'weight')) && ! ($weight = setting('system', 'defaultwt')))
        {
            $weight = 60;
        }
        $this->weight = min($weight, $weight_max ? $weight_max : 100);
	}
	
	function index()
	{
		$this->view->assign('head', array('title'=>'采集'));
		$status = $this->privar('spider_memstatus');
		$this->view->assign('memstatus', empty($status) ? 3 : $status['status']);
		$catid = intval($_GET['catid']);
		if ($catid)
		{
			$this->view->assign('catid', $catid);
			$cat = $category = table('category', $catid);
			$this->view->assign('catname', $cat ? $cat['name'] : '');
		}
		$this->view->display('index');
	}
	
	function tempGrap()
	{
		$url = $_POST['url'];
		$spider_rule = loader::model('admin/spider_rules');
		$rules = $spider_rule->select();
		$state = false;
		$category = table('category');
		$catid = key($category);
		foreach ($rules as $r)
		{
			$pattern = preg_quote($r['enter_rule'], '#');
			$pattern = '#^'.str_replace('\(\*\)','.*?', $pattern).'$#is';
			if (preg_match($pattern, $url))
			{
				$data = array(
					'catid' => $catid,
					'ruleid' => $r['ruleid'],
					'rule' => $r['list_rule'],
					'content_rule' => $r['content_rule'],
					'url' => $url,
					'charset' => $r['charset']
				);
				$temptaskfile = 'temp_spider_task_'.$this->_userid;
				cache_write($temptaskfile, $data);
				$state = true;
				break;
			}
		}
		if ($state)
		{
			$history = loader::model('admin/spider_history');
			$history->add($url, $data['charset']);
			if ($has = $this->spider_task->get("url='$url'"))
			{
				exit ('{"state":true,"taskid":'.$has['taskid'].'}');
			}
			else
			{
				exit ('{"state":true}');
			}
		}
		else
		{
			exit('{"state":false}');
		}
	}
	
	function loadlist()
	{
		if ($_REQUEST['taskid'] == 'temp')
		{
			if ($_GET['status'] == 'new')
			{
				$temptaskfile = 'temp_spider_task_'.$this->_userid;
				$task = cache_read($temptaskfile);
				$engine = loader::lib('spider', 'spider');
				$catid = $task['catid'];
				$cat = table('category',$catid);
				$catname = $cat ? $cat['name'] : '';
				$rule = unserialize($task['rule']);
				$list = $engine->getList($task['url'], $rule, $task['charset']);
				$data = array();
				if (! empty($list))
				{
					foreach ($list as $i => &$row)
					{
						$row['spiderid'] = 'temp_'.$i;
						$row['catid'] = $catid;
						$row['catname'] = $catname;
						
						$item = $row;
						$item['charset'] = $task['charset'];
						$item['rule'] = $task['content_rule'];
						$data['temp_'.$i] = $item;
					}
					$tempfile = 'temp_spider_'.$this->_userid;
					cache_write($tempfile, $data);
				}
				$json = $this->json->encode(array('total'=>count($list), 'data'=>$list));
			}
			else
			{
				$json = '{total:0,data:[]}';
			}
			exit ($json);
		}
		else
		{
			// taskid
			$taskid = intval($_REQUEST['taskid']);
			$page = isset($_GET['page']) ? intval($_GET['page']) : 0;
			
			// task
			$status = $_GET['status'];
			if ($status=='new' && !$page)
			{
				// 爬一次 保存到数据表 spider 中
				$this->spider_task->spider($taskid);
			}
			$where = "taskid=$taskid";
			if ($status) $where .= " AND status='$status'";
			$total = $this->spider->count($where);
			$order = 'spiderid desc';
			$pagesize = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : $this->pagesize;
			$data = $this->spider->page($where, '*', $order, $page, $pagesize);
			$task = $this->spider_task->get($taskid);
			$catid = $task['catid'];
			$cat = table('category', $catid);
			$catname = $cat ? $cat['name'] : '';
			foreach ($data as &$d)
			{
				$d['catid'] = $catid;
				$d['catname'] = $catname;
			}
			exit ($this->json->encode(array('total'=>$total,'data'=>$data)));
		}
	}
	function loaddetail()
	{
		if (substr($_GET['spiderid'], 0, 5) == 'temp_')
		{
			$tempfile = 'temp_spider_'.$this->_userid;
			$data = cache_read($tempfile);
			$item = $data[$_GET['spiderid']];
			if (!$item) {
				exit;
			}
			$rule = unserialize($item['rule']);
			$engine = loader::lib('spider', 'spider');
			$details = $engine->getDetails($item['url'], $rule, $item['charset']);
		}
		else
		{
			$spiderid = intval($_GET['spiderid']);
			if (!$spiderid) exit;
			// model
			$this->spider->setViewed($spiderid);
			$details = $this->spider->spider($spiderid);
		}
		$this->view->assign($details);
		$this->view->display('detail');
	}
	
	function viewedone()
	{
		$spiderid = intval($_GET['spiderid']);
		if ($spiderid) {
			$this->spider->setViewed($spiderid);
		}
	}
	function getcat()
	{
		// 随机数解决id冲突的bug
		echo element::category('catid'.time().rand(10000), 'catid');
	}
	
	function tasklist()
	{
		exit ($this->json->encode($this->spider_task->ls()));
	}
	
	function spider()
	{
		if ($this->_userid)
		{
			$editor = table('admin', $this->_userid, 'name');
		}
		else
		{
			$editor = $this->_username;
		}
		if (substr($_POST['spiderid'], 0, 5) == 'temp_')
		{
			$tempfile = 'temp_spider_'.$this->_userid;
			$tempdata = cache_read($tempfile);
			if (!$tempdata || !($spider = &$tempdata[$_POST['spiderid']]))
			{
				exit ('{"state":false}');
			}
			$rule = unserialize($spider['rule']);
			$engine = loader::lib('spider', 'spider');
			$details = $engine->getDetails($spider['url'], $rule, $spider['charset']);
			$status = intval($_POST['status']);
			$catid = intval($_POST['catid']);
			$data = array(
				'catid'=>$catid,
				'modelid'=>1,
				'title'=>str_cutword($details['title'], 80),
				'content'=>$details['content'],
				'status'=>$status,
				'weight'=>$this->weight,
				'author'=>$details['author'],
				'editor'=>$editor,
				'source'=>$details['source'],
				'published'=>$details['pubdate'],
				'saveremoteimage'=>($details['saveremoteimage'] ? 1 : 0),
				'allowcomment'=>1
			);
			$article = loader::model('admin/article', 'article');
			$json = array('state' => false,'info' => '采集失败');
			if ($spider['contentid'])
			{
				if ($article->edit($spider['contentid'], $data))
				{
					unset($spider['rule']);
					$json = array('state'=>true, 'data'=>$spider);
				}
			}
			else
			{
				if ($contentid = $article->add($data))
				{
					$spider['contentid'] = $contentid;
					$spider['status'] = 'spiden';
					cache_write($tempfile, $tempdata);
					unset($spider['rule']);
					$json = array('state'=>true, 'data'=>$spider);
				}
			}
			exit($this->json->encode($json));
		}
		else
		{
			$spiderid = intval($_POST['spiderid']);
			if (!$spiderid || !($spider = $this->spider->get($spiderid)))
			{
				exit ('{"state":false}');
			}
			$details = $this->spider->spider($spiderid);
			$status = intval($_POST['status']);
			$catid = intval($_POST['catid']);
			$data = array(
				'catid'=>$catid,
				'modelid'=>1,
				'title'=>$details['title'],
				'content'=>$details['content'],
				'status'=>$status,
				'weight'=>$this->weight,
				'author'=>$details['author'],
				'editor'=>$editor,
				'source'=>$details['source'],
				'published'=>$details['pubdate'],
				'saveremoteimage'=>($details['saveremoteimage'] ? 1 : 0),
				'allowcomment'=>1
			);
			
			$article = loader::model('admin/article', 'article');
			$json = array('state' => false,'info' => '采集失败');
			if ($spider['contentid'])
			{
				if ($article->edit($spider['contentid'], $data))
				{
					$data = $this->spider->get($spiderid);
					$cat = table('category', $catid);
					$data['catid'] = $catid;
					$data['catname'] = $cat ? $cat['name'] : '';
					$json = array('state'=>true, 'data'=>$data);
				}
			}
			else
			{
				if ($contentid = $article->add($data))
				{
					$this->spider->setSpiden($spiderid, array(
						'contentid'=>$contentid
					));
					$data = $this->spider->get($spiderid);
					$cat = table('category', $catid);
					$data['catid'] = $catid;
					$data['catname'] = $cat ? $cat['name'] : '';
					$json = array('state'=>true, 'data'=>$data);
				}
			}
			exit ($this->json->encode($json));
		}
	}
	
	function memstatus()
	{
		$this->privar('spider_memstatus', $_POST['status']);
	}
	
	function suggest()
	{
		$keyword = $_REQUEST['keyword'];
		if (trim($keyword) == '') {
			$keyword = '';
		} else {
			$keyword = str_replace('_', '\_', addcslashes($keyword, '%_'));
		}
		$history = loader::model('admin/spider_history');
		$data = $history->suggest($keyword);
		foreach ($data as &$r)
		{
			$r['text'] = $r['url'];
		}
		exit ($this->json->encode($data));
	}
	
	function cron()
	{
		@set_time_limit(0);
		foreach ($this->spider_task->cron_publish() as $task)
		{
			$this->spider_task->spider($task['taskid']);
		}
		exit ('{"state":true}');
	}

	function contentapi()
	{
		$url = isset($_REQUEST['url']) ? trim($_REQUEST['url']) : '';
		if (!$url) 
		{
			$result = array('state'=>FALSE, 'error'=>'采集URL为空');
		}
		else
		{
			$engine = loader::model('admin/smarter', 'spider');
			$result = $engine->getInfo($url);
		}
		$result = json_encode($result);
		if (isset($_REQUEST['jsoncallback']))
		{
			$result = $_REQUEST['jsoncallback'] ."(" .$result .");";
		}
		exit($result);
	}

	function quickspider()
	{
		$url = isset($_REQUEST['url']) ? $_REQUEST['url'] : '';
		$catid = isset($_REQUEST['catid']) ? intval($_REQUEST['catid']) : 0;
		if (empty($url)) 
		{
			$result = array('state'=>FALSE, 'error'=>'采集URL为空');
		}
		if (!$catid) {
			$result = array('state'=>FALSE, 'error'=>'入库分类为空');
		}
		if (!isset($result['state']))
		{
			if (!is_array($url))
			{
				$url = array($url);
			}
			$engine = loader::model('admin/smarter', 'spider');
			$result = $engine->quickSpider($url,$catid);
		}
		$result = json_encode($result);
		if (isset($_REQUEST['jsoncallback']))
		{
			$result = $_REQUEST['jsoncallback'] ."(" .$result .");";
		}
		exit($result);
	}
}