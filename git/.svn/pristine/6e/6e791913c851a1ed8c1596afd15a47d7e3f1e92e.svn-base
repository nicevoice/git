<?php
/**
 * 文章推送
 *
 * @aca whole 文章推送
 */
class controller_admin_push extends push_controller_abstract
{
	protected $push, $push_task, $push_rule;
	public function __construct(&$app)
	{
		parent::__construct($app);
		$this->push = loader::model('admin/push');
		$this->push_task = loader::model('admin/push_task');
		$this->push_rule = loader::model('admin/push_rule');
	}
	
	function index()
	{
		$this->view->assign('head', array('title'=>'推送'));
		$status = $this->privar('push_memstatus');
		$this->view->assign('memstatus', empty($status) ? 3 : $status['status']);
		
		$catid = intval($_GET['catid']);
		if ($catid)
		{
			$this->view->assign('catid', $catid);
			$cat = table('category', $catid);
			$this->view->assign('catname', $cat ? $cat['name'] : '');
		}
		$this->view->display('index');
	}
	function manager()
	{
		$this->view->assign('head',array('title'=>'规则管理'));
		$this->view->display('manager');
	}
	function page()
	{
		$fields = '*';
		$order = 'ruleid DESC';
		$where = null;
		$data = $this->push_rule->select($where, $fields, $order);
		$dsns = table('dsn');
		$users = table('admin');
		foreach ($data as &$val)
		{
			if ($d = $dsns[$val['dsnid']])
			{
				$val['dsnname'] = $d['name'];
			}
			$val['created'] = date('Y-m-d H:i:s', $val['created']);
			if ($u = $users[$val['createdby']])
			{
				$val['username'] = $u['name'];
			}
		}
        
		exit ($this->json->encode(array('total'=>0, 'data'=>$data)));
	}
	function add()
	{
		if ($this->is_post())
		{
			$jointable = array();
	        if (!empty($_POST['jointable']))
	        {
	        	foreach ($_POST['jointable'] as $i=>$t)
	        	{
	        		if (empty($t) || empty($_POST['on'][$i])) continue;
	        		$jointable[$t] = $_POST['on'][$i];
	        	}
	        }
	        $data = array(
	        	'name' => $_POST['name'],
	        	'dsnid' => intval($_POST['dsnid']),
	        	'maintable' => $_POST['maintable'],
	        	'jointable' => serialize($jointable),
	        	'primary' => $_POST['primary'],
	        	'condition' => $_POST['condition'],
	        	'plugin' => $_POST['plugin'],
        		'linkrule' => $_POST['linkrule'],
	        	'fields' => serialize($_POST['fields']),
	        	'defaults' => serialize($_POST['defaults']),
	        	'description' => $_POST['description']
	        );
	        if ($ruleid = $this->push_rule->insert($data))
			{
				$info = array('添加规则成功');
				if ($_POST['taskname']) {
					$taskdata = array(
						'ruleid' => $ruleid,
						'title' => $_POST['taskname'],
						'catid' => intval($_POST['catid']),
						'extra_condition' => $_POST['extra_condition']
					);
					if ($this->push_task->insert($taskdata))
					{
						$info[] = '创建监听任务成功';
					}
					else
					{
						$info[] = '创建监听任务失败';
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
					'error'=>$this->push_rule->error()
				)));
			}
		}
		else
		{
			$this->view->assign('head',array('title'=>'添加推送规则'));
			$this->view->display('add');
		}
	}
	function edit()
	{
		$ruleid = intval($_REQUEST['ruleid']);
		if (! $ruleid)
		{
			exit ('{"state":false,"error":"无编辑对象"}');
		}
		if ($this->is_post())
		{
			$jointable = array();
	        if (!empty($_POST['jointable']))
	        {
	        	foreach ($_POST['jointable'] as $i=>$t)
	        	{
	        		if (empty($t) || empty($_POST['on'][$i])) continue;
	        		$jointable[$t] = $_POST['on'][$i];
	        	}
	        }
	        $data = array(
	        	'name' => $_POST['name'],
	        	'dsnid' => intval($_POST['dsnid']),
	        	'maintable' => $_POST['maintable'],
	        	'jointable' => serialize($jointable),
	        	'primary' => $_POST['primary'],
	        	'condition' => $_POST['condition'],
	        	'plugin' => $_POST['plugin'],
	        	'linkrule' => $_POST['linkrule'],
	        	'fields' => serialize($_POST['fields']),
	        	'defaults' => serialize($_POST['defaults']),
	        	'description' => $_POST['description']
	        );
			if ($this->push_rule->update($data, $ruleid))
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
					'error'=>$this->push_rule->error()
				)));
			}
		}
		else
		{
			$rule = $this->push_rule->get($ruleid);
			$rule['jointable'] = unserialize($rule['jointable']);
			$rule['fields'] = unserialize($rule['fields']);
			$rule['defaults'] = unserialize($rule['defaults']);
			
			$this->view->assign('head',array('title'=>'编辑推送规则：'.$rule['name']));
			$this->view->assign($rule);
			$this->view->display('edit');
		}
	}
	function del()
	{
		if (! $_POST['id'])
		{
			exit ('{"state":false,"error":"没有可删除的"}');
		}
		$ids = array_unique(array_filter(array_map('trim',explode(',',$_POST['id']))));
		foreach($ids as $ruleid)
        {
        	$this->push_rule->delete($ruleid);
        }
        exit ('{"state":true,"info":"删除完毕"}');
	}
	function addTask()
	{
		if ($this->is_post())
		{
			if ($taskid = $this->push_task->insert($_POST))
			{
				$json = array('state'=>true, 'data'=>$this->push_task->get($taskid));
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
	function editTask()
	{
		$taskid = intval($_REQUEST['taskid']);
		if (! $taskid)
		{
			exit('{"state":false,"error":"无操作对象"}');
		}
		$task = $this->push_task->get($taskid);
		if ($this->is_post())
		{
			if ($this->push_task->update($_POST,$taskid))
			{
				$json = array('state'=>true, 'data'=>$this->push_task->get($taskid));
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
	function delTask()
	{
		$taskid = intval($_REQUEST['taskid']);
		if (!$taskid) {
			exit('{"state":false,"error":"无删除对象"}');
		}
		if (false !== $this->push_task->delete($taskid))
		{
			exit('{"state":true}');
		}
		else
		{
			exit($this->json->encode(array(
				'state'=>false,
				'error'=>$this->push_task->error()
			)));
		}
	}
	protected function _rule_dropdown($id = null, $name = 'ruleid', $value = null)
	{
		$settings = array();
		$settings['id'] = $id;
		$settings['name'] = $name;
		$settings['value'] = $value;
		$settings['options'] = array();
		foreach ($this->push_rule->select() as $r)
		{
			$settings['options'][$r['ruleid']] = $r['name'];
		}
		return form_element::select($settings);
	}
	function tables()
	{
		import('db.db');
		$dsnid = intval($_REQUEST['dsnid']);
		$dsn = loader::model('admin/dsn','system');
		if (! $dsnid || !($d = $dsn->get($dsnid)))
		{
			exit ('{"state":false}');
		}
        try {
		    $db = & db::get_instance($d);
        } catch (PDOException $e) {
            exit ('{"state":false}');
        }
        $tableset = $db->list_tables();
        $html = '<select ><option value="">请选择</option>';
		if(!empty($tableset))
		{
			foreach($tableset as $v)
			{
				$html .= "<option value='".$v."'>".$v."</option>";
			}
		}
		$html .= "</select>";
		$json = array('state'=>true,'html'=>$html);
		exit ($this->json->encode($json));
	}
	function primary()
	{
		import('db.db');
		$dsnid = intval($_REQUEST['dsnid']);
		$dsn = loader::model('admin/dsn','system');
		if (! $dsnid || !($d = $dsn->get($dsnid)))
		{
			exit ('{"state":false}');
		}
        try {
		    $db = & db::get_instance($d);
        } catch (PDOException $e) {
            exit ('{"state":false}');
        }
        
        $tables = array_unique(array_filter($_GET['tables']));
        $html = '<select><option value="">请选择</option>';
        foreach ($tables as $t)
        {
        	$fieldset = $db->list_fields($t);
        	foreach ($fieldset as $f)
        	{
        		$html .= '<option value="'.$t.'.'.$f['Field'].'">'.$t.'.'.$f['Field'].'</option>';
        	}
        }
        $html .= '</select>';
        $json = array('state'=>true, 'html'=>$html);
		exit ($this->json->encode($json));
	}
	function fields()
	{
		import('db.db');
		$dsnid = intval($_REQUEST['dsnid']);
		$dsn = loader::model('admin/dsn','system');
		if (! $dsnid || !($d = $dsn->get($dsnid)))
		{
			exit ('{"state":false}');
		}
        try {
		    $db = & db::get_instance($d);
        } catch (PDOException $e) {
            exit ('{"state":false}');
        }
        
        $tables = array_unique(array_filter($_GET['tables']));
        $html = '<select><option value="">请选择</option>';
        foreach ($tables as $t)
        {
        	$fieldset = $db->list_fields($t);
        	foreach ($fieldset as $f)
        	{
        		$html .= '<option value="'.$f['Field'].'">'.$t.'.'.$f['Field'].'</option>';
        	}
        }
        $html .= '</select>';
        $json = array('state'=>true,'html'=>$html);
		exit ($this->json->encode($json));
	}
	function testGetList()
	{
		import('db.db');
		$dsnid = intval($_POST['dsnid']);
		$dsn = loader::model('admin/dsn','system');
		if (! $dsnid || !($d = $dsn->get($dsnid)))
		{
			exit ('{"state":false}');
		}
        try {
		    $db = & db::get_instance($d);
        } catch (PDOException $e) {
            exit ('{"state":false}');
        }
        
        $charset = strtr($d['charset'],array(
        	'utf8'=>'utf-8',
        	'UTF8'=>'utf-8'
        ));
        $jointable = array();
        if (!empty($_POST['jointable']))
        {
        	foreach ($_POST['jointable'] as $i=>$t)
        	{
        		if (empty($t) || empty($_POST['on'][$i])) continue;
        		$jointable[$t] = $_POST['on'][$i];
        	}
        }
        $rule = array(
        	'maintable' => $_POST['maintable'],
        	'jointable' => $jointable,
        	'primary' => $_POST['primary'],
        	'condition' => $_POST['condition'],
        	'plugin' => $_POST['plugin'],
        	'linkrule' => $_POST['linkrule'],
        	'fields' => $_POST['fields'],
	        'defaults' => $_POST['defaults']
        );
		
		$engine = loader::lib('push', 'push');
		try {
			$list = $engine->getList($db, $rule, $charset);
		} catch (Exception $e) {
			exit ('{"state":false}');
		}
		
		$this->view->assign('list', $list);
		$html = $this->view->fetch('list');
		exit ($this->json->encode(array(
			'state'=>true,
			'html'=>$html
		)));
	}
	
	function tasklist()
	{
		exit ($this->json->encode($this->push_task->select()));
	}
	function loadlist()
	{
		// taskid
		$taskid = intval($_REQUEST['taskid']);
		$page = intval($_GET['page']);
		if ($page < 1)
		{
			$page = 1;
		}
		// get 20
		if ($_GET['status'])
		{
			$data = $this->push->page_status($taskid, $_GET['status'], $page);
		}
		else
		{
			$data = $this->push->page_all($taskid, $page);
		}
		$total = $this->push->total;
		if ($page * 20 > $total && empty($data))
		{
			exit ("{\"state\":false,\"total\":$total,\"data\":[]}");
		}
		else
		{
			exit ($this->json->encode(array('state'=>true,'total'=>$total,'data'=>$data)));
		}
	}
	function loaddetail()
	{
		$pushid = intval($_GET['pushid']);
		if (!$pushid) exit;
		// model
		$this->push->setViewed($pushid);
		$details = $this->push->get_detail($pushid);
		$this->view->assign($details);
		$this->view->display('detail');
	}
	function viewedone()
	{
		$pushid = intval($_GET['pushid']);
		if ($pushid) {
			$this->push->setViewed($pushid);
		}
	}
	function getcat()
	{
		echo element::category('', '', null, 1, ' style="width:90px"');
	}
	function memstatus()
	{
		$this->privar('push_memstatus', $_POST['status']);
	}
	function push()
	{
		$pushid = intval($_POST['pushid']);
		if (!$pushid || !($push = $this->push->get($pushid)))
		{
			exit ('{"state":false}');
		}
		$details = $this->push->get_detail($pushid);
		$status = intval($_POST['status']);
		$catid = intval($_POST['catid']);
		$data = array(
			'catid'=>$catid,
			'modelid'=>1,
			'title'=>$details['title'],
			'content'=>$details['content'],
			'status'=>$status,
			'weight'=>60,
			'author'=>$details['author'],
			'source'=>$details['source'],
			'editor'=>$this->_username,
			'published'=>date('Y-m-d H:i:s', $details['pubdate']),
			'description'=>$details['description'],
			'tags'=>$details['tags']
		);
		$article = loader::model('admin/article', 'article');
		$json = array('state' => false,'info' => '推送失败');
		if ($push['contentid'])
		{
			if ($article->edit($push['contentid'], $data))
			{
				$json = array('state'=>true);
			}
		}
		else
		{
			if ($contentid = $article->add($data))
			{
				$this->push->setPushed($pushid, array(
					'contentid'=>$contentid
				));
				
				$json = array('state'=>true);
			}
		}
		exit ($this->json->encode($json));
	}
}