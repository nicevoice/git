<?php
/**
 * 管理规则
 *
 * @aca 管理规则
 */
class controller_admin_manager extends spider_controller_abstract
{
	protected $spider, $spider_task, $spider_rule, $pagesize = 20;
	public function __construct(&$app)
	{
		parent::__construct($app);
		$this->spider = loader::model('admin/spider', 'spider');
		$this->spider_task = loader::model('admin/spider_task', 'spider');
		$this->spider_rule = loader::model('admin/spider_rules');
	}

    /**
     * 管理规则
     *
     * @aca 浏览
     */
	public function index()
	{
		$this->view->assign('head',array('title'=>'规则管理'));
		$this->view->assign('pagesize', $this->pagesize);
		$this->view->assign('sitedropdown', $this->_site_dropdown('siteid','siteid',null,' style="width:100px;"','所有'));
		$this->view->display('manager');
	}

    /**
     * 管理列表
     *
     * @aca 浏览
     */
	public function page()
	{
		$fields = '*';
		$order = 'ruleid DESC';
		$where = null;
		if ($siteid = intval($_REQUEST['siteid']))
		{
			$where = "siteid=$siteid";
		}
		$total = $this->spider_rule->count($where);
		$page = max((isset($_GET['page']) ? intval($_GET['page']) : 1), 1);
		$data = $this->spider_rule->page($where, $fields, $order, $page, $this->pagesize);
		$sites = table('spider_site');
		$users = table('admin');
		foreach ($data as &$val)
		{
			if ($s = $sites[$val['siteid']])
			{
				$val['sitename'] = $s['name'];
			}
			else
			{
				$val['sitename'] = '';
			}
			$val['created'] = date('Y-m-d H:i:s', $val['created']);
			if ($u = $users[$val['createdby']])
			{
				$val['username'] = $u['name'];
			}
			else
			{
				$val['username'] = '';
			}
		}
        
		exit ($this->json->encode(array('total'=>$total, 'data'=>$data)));
	}

    /**
     * 添加规则
     *
     * @aca 添加规则
     */
	public function addrule()
	{
		if ($this->is_post())
		{
			$list_rule = serialize(array(
				'listStart' => trim($_POST['listStart']),
				'listEnd' => trim($_POST['listEnd']),
				'listType' => trim($_POST['listType']),
				'listUrl' => $_POST['listUrl'],
				'urlPattern' => $_POST['urlPattern'],
				'listNextPage'=>trim($_POST['listNextPage']),
				'listLimitLength'=>intval($_POST['listLimitLength'])
			));
			$content_rule = serialize(array(
				'contentUrl' => $_POST['contentUrl'],
				'rangeStart' => trim($_POST['rangeStart']),
				'rangeEnd' => trim($_POST['rangeEnd']),
				'titleStart' => trim($_POST['titleStart']),
				'titleEnd' => trim($_POST['titleEnd']),
				'contentStart' => trim($_POST['contentStart']),
				'contentEnd' => trim($_POST['contentEnd']),
				'authorStart' => trim($_POST['authorStart']),
				'authorEnd' => trim($_POST['authorEnd']),
				'sourceStart' => trim($_POST['sourceStart']),
				'sourceEnd' => trim($_POST['sourceEnd']),
				'pubdateStart' => trim($_POST['pubdateStart']),
				'pubdateEnd' => trim($_POST['pubdateEnd']),
				'nextPage' => trim($_POST['nextPage']),
				'allowTags' => trim($_POST['allowTags']),
				'saveRemoteImg' => !empty($_POST['saveRemoteImg']),
				'replacement' => $_POST['replacement']
			));
			$data = array(
				'siteid'=> $_POST['siteid'],
				'name' => $_POST['name'],
				'charset' => $_POST['charset'],
				'enter_rule' => $_POST['enterPattern'],
				'description' => $_POST['description'],
				'list_rule' => $list_rule,
				'content_rule' => $content_rule
			);
			if ($ruleid = $this->spider_rule->insert($data))
			{
				$info = array('添加规则成功');
				if ($_POST['taskname']) {
					$taskdata = array(
						'ruleid' => $ruleid,
						'title' => $_POST['taskname'],
						'catid' => intval($_POST['catid']),
						'url' => $_POST['listUrl'],
						'frequency' => $_POST['frequency']
					);
					if ($this->spider_task->insert($taskdata))
					{
						$info[] = '创建采集任务成功';
					}
					else
					{
						$info[] = '创建采集任务失败';
					}
				}
				exit($this->json->encode(array(
					'state'=>true,
					'info'=>implode('，', $info)
				)));
			}
			else
			{
				exit($this->json->encode(array(
					'state'=>false,
					'error'=>$this->spider_rule->error()
				)));
			}
		}
		else
		{
			$this->view->assign('head',array('title'=>'添加规则'));
			$this->view->assign('sitedropdown', $this->_site_dropdown());
			$url = $_GET['url'];
			$this->view->assign('url', $url);
			if ($url)
			{
				$this->view->assign('savetaskchecked',true);
			}
			$this->view->display('addrule');
		}
	}

    /**
     * 编辑规则
     *
     * @aca 编辑规则
     */
	public function editrule()
	{
		$ruleid = intval($_REQUEST['ruleid']);
		if (! $ruleid)
		{
			$this->_error_out('无编辑对象');
		}
		if ($this->is_post())
		{
			$list_rule = serialize(array(
				'listStart' => trim($_POST['listStart']),
				'listEnd' => trim($_POST['listEnd']),
				'listType' => trim($_POST['listType']),
				'listUrl' => $_POST['listUrl'],
				'urlPattern' => $_POST['urlPattern'],
				'listNextPage'=>trim($_POST['listNextPage']),
				'listLimitLength'=>intval($_POST['listLimitLength'])
			));
			$content_rule = serialize(array(
				'contentUrl' => $_POST['contentUrl'],
				'rangeStart' => trim($_POST['rangeStart']),
				'rangeEnd' => trim($_POST['rangeEnd']),
				'titleStart' => trim($_POST['titleStart']),
				'titleEnd' => trim($_POST['titleEnd']),
				'contentStart' => trim($_POST['contentStart']),
				'contentEnd' => trim($_POST['contentEnd']),
				'authorStart' => trim($_POST['authorStart']),
				'authorEnd' => trim($_POST['authorEnd']),
				'sourceStart' => trim($_POST['sourceStart']),
				'sourceEnd' => trim($_POST['sourceEnd']),
				'pubdateStart' => trim($_POST['pubdateStart']),
				'pubdateEnd' => trim($_POST['pubdateEnd']),
				'nextPage' => trim($_POST['nextPage']),
				'allowTags' => trim($_POST['allowTags']),
				'saveRemoteImg' => !empty($_POST['saveRemoteImg']),
				'replacement' => $_POST['replacement']
			));
			$data = array(
				'siteid'=> $_POST['siteid'],
				'name' => $_POST['name'],
				'charset' => $_POST['charset'],
				'enter_rule' => $_POST['enterPattern'],
				'description' => $_POST['description'],
				'list_rule' => $list_rule,
				'content_rule' => $content_rule
			);
			if ($this->spider_rule->update($data, $ruleid))
			{
				exit($this->json->encode(array(
					'state'=>true,
					'info'=>'编辑规则成功'
				)));
			}
			else
			{
				exit($this->json->encode(array(
					'state'=>false,
					'error'=>$this->spider_rule->error()
				)));
			}
		}
		else
		{
			$rule = $this->spider_rule->get($ruleid);
			$rule['list_rule'] = unserialize($rule['list_rule']);
			$rule['content_rule'] = unserialize($rule['content_rule']);
			
			$this->view->assign('head',array('title'=>'编辑规则：'.$rule['name']));
			$this->view->assign($rule);
			$this->view->assign('sitedropdown', $this->_site_dropdown(null,'siteid',$rule['siteid']));
			$this->view->display('editrule');
		}
	}

    /**
     * 删除规则
     *
     * @aca 删除规则
     */
	public function delrule()
	{
		if (! $_POST['id'])
		{
			exit ('{"state":false,"error":"没有可删除的"}');
		}
		$ids = array_unique(array_filter(array_map('trim',explode(',',$_POST['id']))));
		foreach($ids as $ruleid)
        {
        	$this->spider_rule->delete($ruleid);
        }
        exit ('{"state":true,"info":"删除完毕"}');
	}

    /**
     * 网站管理
     *
     * @aca 浏览
     */
	public function sites()
	{
		$this->view->assign('head',array('title'=>'网站管理'));
		$this->view->display('sites');
	}

    /**
     * 网站列表
     *
     * @aca 网站列表
     */
	public function lsSite()
	{
		$site = loader::model('admin/spider_site');
		exit ($this->json->encode(array('total'=>0, 'data'=>$site->select())));
	}

    /**
     * 添加网站
     *
     * @aca 添加网站
     */
	public function addSite()
	{
		if ($this->is_post())
		{
			if (empty($_POST['name']))
			{
				exit ('{"state":false,"error":"请填写名称"}');
			}
			$spider_site = loader::model('admin/spider_site');
			if ($siteid = $spider_site->insert($_POST))
			{
				exit ($this->json->encode(array(
					'state'=>true,
					'data'=>$spider_site->get($siteid)
				)));
			}
			
			exit('{"state":false,"error":"添加失败"}');
		}
		else
		{
			$this->view->display('addsite');
		}
	}

    /**
     * 编辑网站
     *
     * @aca 编辑网站
     */
	public function editSite()
	{
		$siteid = intval($_REQUEST['siteid']);
		$spider_site = loader::model('admin/spider_site');
		if (!$siteid || !($site = $spider_site->get($siteid)))
		{
			$this->_error_out('无编辑对象');
		}
		if ($this->is_post())
		{
			if (empty($_POST['name']))
			{
				exit ('{"state":false,"error":"请填写名称"}');
			}
			if ($spider_site->update($_POST, $siteid))
			{
				exit ($this->json->encode(array(
					'state'=>true,
					'data'=>$spider_site->get($siteid)
				)));
			}
			
			exit('{"state":false,"error":"编辑失败"}');
		}
		else
		{
			$this->view->assign($site);
			$this->view->display('editsite');
		}
	}

    /**
     * 删除网站
     *
     * @aca 删除网站
     */
	public function delSite()
	{
		if (! $_POST['id'])
		{
			exit ('{"state":false,"error":"没有可删除的"}');
		}
		$site = loader::model('admin/spider_site');
		$ids = array_unique(array_filter(array_map('trim',explode(',',$_POST['id']))));
		foreach($ids as $ruleid)
        {
        	$site->delete($ruleid);
        }
        exit ('{"state":true,"info":"删除完毕"}');
	}

    /**
     * 添加任务
     *
     * @aca 添加任务
     */
	public function addTask(){
		if ($this->is_post())
		{
			if (empty($_POST['title']))
			{
				$ruleid = intval($_POST['ruleid']);
				if (!$ruleid || !($r = $this->spider_rule->get($ruleid)))
				{
					exit ('{"state":false,"error":"未选择规则"}');
				}
				
				$data = array(
					'catid' => 1,
					'ruleid' => $ruleid,
					'rule' => $r['list_rule'],
					'content_rule' => $r['content_rule'],
					'url' => $_POST['url'],
					'charset' => $r['charset']
				);
				$temptaskfile = 'temp_spider_task_'.$this->_userid;
				cache_write($temptaskfile, $data);
				
				$history = loader::model('admin/spider_history');
				$history->add($_POST['url'], $r['charset']);
				exit ('{"state":true}');
			}
			elseif ($taskid = $this->spider_task->insert($_POST))
			{
				$json = array('state'=>true, 'data'=>$this->spider_task->get($taskid));
			}
			else
			{
				$json = array('state'=>false, 'error'=>'添加失败');
			}
			exit ($this->json->encode($json));
		}
		else
		{
			$this->view->assign('rule_dropdown',$this->_rule_dropdown());
			$this->view->display('addtask');
		}
	}

    /**
     * 规则导出
     *
     * @aca 规则导出
     */
	public function export()
	{
		$setting = new setting();
		$author = $setting->get('system', 'sitename');
		$guidprefix = $setting->get('system', 'siteurl').'open-rule/';
		$charset = config::get('config', 'charset', 'utf-8');
		$spider_site = table('spider_site');
		
		$ruleid = array_filter(array_map('intval', explode(',', $_GET['ruleid'])));
		$where = empty($ruleid) ? null : 'ruleid IN ('.implode_ids($ruleid).')';
		$rules = $this->spider_rule->select($where);
		foreach ($rules as &$rule)
		{
			$rule['author'] = $author;
			$rule['guid'] = $guidprefix.$rule['ruleid'].'.xml';
			$rule['version'] = $rule['updated'];
			$rule['sitename'] = $spider_site[$rule['siteid']]['name'];
			$rule['list_rule'] = unserialize($rule['list_rule']);
			$rule['content_rule'] = unserialize($rule['content_rule']);
			$rule['task'] = $this->spider_task->select("ruleid=$rule[ruleid]");
		}
		$this->view->assign('rules', $rules);
		$content = $this->view->fetch('export');
		if (count($rules) > 1)
		{
			$fileName = '采集规则打包.xml';
		}
		else
		{
			$fileName = $rules[0]['name'].'.xml';
		}
		
		$content = '<?xml version="1.0" encoding="'.$charset.'"?>'."\n".$content;
		header('Content-Type:application/octet-stream');
		if (preg_match("/msie/i", $_SERVER["HTTP_USER_AGENT"]))
		{
			header('Content-Disposition: attachment; filename="'.rawurlencode($fileName).'"');
		}
		else
		{
			header('Content-Disposition: attachment; filename="'.$fileName.'"');
		}
		header('Content-Length:'.strlen($content));
        header('Content-Transfer-Encoding:binary');
        echo $content;
        exit;
	}

    /**
     * 规则导入
     *
     * @aca 规则导入
     */
	public function import()
	{
		$xmlfile = $_FILES['xmlfile']['tmp_name'];
		if (empty($xmlfile))
		{
			exit ('{"state":false, "error":"没有找到文件"}');
		}
		import('helper.xml');
		$xml = file_get_xmlarray($xmlfile, 'pack');
		if (empty($xml) || !($rules = $xml['rule']))
		{
			exit ('{"state":false, "error":"读取文件失败"}');
		}
		$spider_site = loader::model('admin/spider_site');
		if (array_keys($rules) !== range(0, count($rules)- 1))
		{
			$rules = array($rules);
		}
		$error = 0;
		$success = 0;
		foreach ($rules as &$rule)
		{
			$rule['sitename'] = trim($rule['sitename']);
			if (empty($rule['sitename']))
			{
				$rule['sitename'] = '默认';
			}
			if ($site = $spider_site->get("name='".addslashes($rule['sitename'])."'"))
			{
				$siteid = $site['siteid'];
			}
			else
			{
				$siteid = $spider_site->insert(array(
					'name'=>$rule['sitename']
				));
			}
			$rule['siteid'] = $siteid;
			$rule['list_rule'] = serialize($rule['list_rule']);
			$rule['content_rule'] = serialize($rule['content_rule']);
			if ($rule['guid'] && ($r = $this->spider_rule->get("guid='".addslashes($rule['guid'])."'")))
			{
				$ruleid = $r['ruleid'];
				$this->spider_rule->update($rule, $ruleid);
			}
			else
			{
				$ruleid = $this->spider_rule->insert($rule);
				if ($ruleid)
				{
					$success += 1;
				}
				else
				{
					$error += 1;
					continue;
				}
			}
			if (empty($rule['task'])) continue;
			if (array_keys($rule['task']) !== range(0, count($rule['task'])- 1))
			{
				$rule['task'] = array($rule['task']);
			}
			foreach ($rule['task'] as $task)
			{
				if ($t = $this->spider_task->get("url='".addslashes($task['url'])."'"))
				{
					$this->spider_task->update(array(
						'title'=>$task['title'],
						'ruleid'=>$ruleid
					));
				}
				else
				{
					$this->spider_task->insert(array(
						'catid'=>1,
						'title'=>$task['title'],
						'ruleid'=>$ruleid,
						'url'=>$task['url']
					));
				}
			}
		}
		exit($this->json->encode(array(
			'state'=>true,
			'info'=>'导入规则成功<b>'.$success.'<b>条,失败<b>'.$error.'</b>'
		)));
	}

    /**
     * 选择规则
     *
     * @aca 选择规则
     */
	public function selectRule()
	{
		$this->view->assign('head',array('title'=>'添加规则'));
		$this->view->assign('url',$_REQUEST['url']);
		$this->view->assign('rule_dropdown',$this->_rule_dropdown());
		$this->view->display('selectrule');
	}

    /**
     * 规则是否匹配
     *
     * @aca 选择规则
     */
	public function matchRule()
	{
		$url = $_GET['url'];
		$rules = $this->spider_rule->select();
		foreach ($rules as $r)
		{
			$pattern = preg_quote($r['enter_rule'], '#');
			$pattern = '#^'.str_replace('\(\*\)','.*?', $pattern).'$#is';
			if (preg_match($pattern, $url))
			{
				exit('{"state":true,"ruleid":'.$r['ruleid'].'}');
			}
		}
		exit('{"state":false}');
	}

    /**
     * 保存临时任务
     *
     * @aca 选择规则
     */
	public function saveTempTask()
	{
		$temptaskfile = 'temp_spider_task_'.$this->_userid;
		$task = cache_read($temptaskfile);
		if (! $task)
		{
			exit ("没有创建临时采集任务");
		}
		cache_delete($temptaskfile);
		$this->view->assign($task);
		$this->view->assign('rule_dropdown',$this->_rule_dropdown(null,'ruleid',$task['ruleid']));
		$this->view->display('savetemptask');
	}

    /**
     * 编辑任务
     *
     * @aca 编辑任务
     */
	public function editTask(){
		$taskid = intval($_REQUEST['taskid']);
		if (! $taskid)
		{
			exit('{"state":false,"error":"无操作对象"}');
		}
		$task = $this->spider_task->get($taskid);
		if ($this->is_post())
		{
			if ($this->spider_task->update($_POST,$taskid))
			{
				$json = array('state'=>true, 'data'=>$this->spider_task->get($taskid));
			}
			else
			{
				$json = array('state'=>false, 'error'=>'编辑失败');
			}
			exit ($this->json->encode($json));
		}
		else
		{
			$this->view->assign($task);
			$this->view->assign('rule_dropdown',$this->_rule_dropdown(null,'ruleid',$task['ruleid']));
			$this->view->display('edittask');
		}
	}

    /**
     * 删除任务
     *
     * @aca 删除任务
     */
	public function delTask(){
		$taskid = intval($_REQUEST['taskid']);
		if (!$taskid) {
			exit('{"state":false,"error":"无删除对象"}');
		}
		if (false !== $this->spider_task->delete($taskid))
		{
			exit('{"state":true}');
		}
		else
		{
			exit($this->json->encode(array(
				'state'=>false,
				'error'=>$this->spider_task->error()
			)));
		}
	}

    /**
     * 测试规则是否匹配
     *
     * @aca 测试规则
     */
	function testRule()
	{
		$url = $_POST['url'];
		$pattern = preg_quote($_POST['pattern'], '#');
		$pattern = '#^'.str_replace('\(\*\)','.*?', $pattern).'$#is';
		if (preg_match($pattern, $url))
		{
			exit ('{"state":true,"info":"规则匹配"}');
		}
		else
		{
			exit ('{"state":false,"error":"规则不匹配"}');
		}
	}

    /**
     * 测试获取内容列表
     *
     * @aca 测试规则
     */
	function testGetList()
	{
		$url = $_POST['url'];
		$rule = array(
			'listStart'=>trim($_POST['listStart']),
			'listEnd'=>trim($_POST['listEnd']),
			'listType' =>trim($_POST['listType']),
			'urlPattern'=>$_POST['urlPattern'],
			'listNextPage'=>trim($_POST['listNextPage']),
			'listLimitLength'=>intval($_POST['listLimitLength'])
		);
		$charset = $_POST['charset'];
		$engine = loader::lib('spider', 'spider');
		$list = $engine->getList($url, $rule, $charset);
		$this->view->assign('list', $list);
		$this->view->display('list');
	}

    /**
     * 测试获取详细内容
     *
     * @aca 测试规则
     */
	function testGetDetail()
	{
		$url = $_POST['url'];
		$rule = array(
			'rangeStart'=>trim($_POST['rangeStart']),
			'rangeEnd'=>trim($_POST['rangeEnd']),
			'titleStart'=>trim($_POST['titleStart']),
			'titleEnd'=>trim($_POST['titleEnd']),
			'contentStart'=>trim($_POST['contentStart']),
			'contentEnd'=>trim($_POST['contentEnd']),
			'authorStart'=>trim($_POST['authorStart']),
			'authorEnd'=>trim($_POST['authorEnd']),
			'sourceStart'=>trim($_POST['sourceStart']),
			'sourceEnd'=>trim($_POST['sourceEnd']),
			'pubdateStart'=>trim($_POST['pubdateStart']),
			'pubdateEnd'=>trim($_POST['pubdateEnd']),
			'nextPage' => trim($_POST['nextPage']),
			'allowTags'=>trim($_POST['allowTags']),
			'saveRemoteImg' => !empty($_POST['saveRemoteImg']),
			'replacement'=>$_POST['replacement']
		);
		$charset = $_POST['charset'];
		$engine = loader::lib('spider', 'spider');
		$details = $engine->getDetails($url, $rule, $charset, true);
		$this->view->assign($details);
		$this->view->display('detail');
	}
	
	protected function _rule_dropdown($id = null, $name = 'ruleid', $value = null)
	{
		$settings = array();
		$settings['id'] = $id;
		$settings['name'] = $name;
		$settings['value'] = $value;
		$settings['options'] = array();
		foreach ($this->spider_rule->select() as $r)
		{
			$settings['options'][$r['ruleid']] = $r['name'];
		}
		return form_element::select($settings);
	}
	protected function _site_dropdown($id = null, $name = 'siteid', $value = null, $attr = null, $tips = '请选择...')
	{
		$settings = array();
		$settings['id'] = $id;
		$settings['name'] = $name;
		$settings['value'] = $value;
		$settings['options'] = array(0=>$tips);
		$settings['attribute'] = $attr;
		$spider_site = loader::model('admin/spider_site');
		foreach ($spider_site->select() as $s)
		{
			$settings['options'][$s['siteid']] = $s['name'];
		}
		return form_element::select($settings);
	}
}