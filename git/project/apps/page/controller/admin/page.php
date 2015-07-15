<?php
/**
 * 页面管理
 *
 * @aca 页面管理
 */
class controller_admin_page extends page_controller_abstract
{
	private $pagesize = 10, $uri;

	function __construct(& $app)
	{
		parent::__construct($app);
		if (isset($_REQUEST['pageid']) && ($pageid = intval($_REQUEST['pageid'])) > 0)
		{
			$page = $this->page->get($pageid);
			if (!$page) $this->_error_out("页面不存在！");
			if (in_array($app->action, array('sections')))
			{
				return;
			}
			if (!priv::page($pageid))
			{
				if (in_array($app->action, array('delete','edit','publish','previewpage','cron'))
					|| !priv::section_page($pageid))
				{
					$this->_error_out("您没有<span style='color:red'>".$page['name']."($pageid)</span>管理权限！");
				}
			}
		}
		$this->uri = loader::lib('uri','system');
	}
	
	/**
     * 页面管理
     *
     * @aca public 浏览
     */
	function index()
	{
		$this->view->assign('head', array('title'=>'页面管理'));
		$this->view->display("page/index");
	}

    /**
     * 页面列表
     *
     * @aca public 浏览
     */
	function tree()
	{
		$data = $this->page->select(null, "*");
		$priv = loader::model('admin/page_priv');
		foreach ($data as $k=>&$page)
		{
			$page['class'] = 'edit';
			
			if (!priv::page($page['pageid']) && !priv::section_page($page['pageid']))
			{
				if (priv::page($page['pageid'], true) || priv::section_page($page['pageid'], true))
				{
					$page['class'] = 'no';
				}
				else 
				{
					unset($data[$k]);
					continue;
				}
			}
				
			$path = array("3");
			if ($page['parentids'])
			{
				foreach (explode(',',$page['parentids']) as $id)
				{
					$path[] = '000'.$id;
				}
			}
			$path[] = '000'.$page['pageid'];
			$page['clickpath'] = implode_ids($path);
			//$admin = $priv->ls_userid($page['pageid']);
			foreach ($admin as &$user)
			{
				$user = '<a href="javascript: url.member('.$user.');">'.username($user).'</a>';
			}
			$u = $this->_uri($page['path']);
			//$page['admin'] = implode('&nbsp;', $admin);
			$page['admin'] = '';
			$page['size'] = number_format(round(intval(filesize($u['path'])) / 1024 * 100) / 100, 2) . 'KB';
			$page['published'] = $page['published'] ? date('Y-m-d H:i:s', $page['published']) : '未生成';
		}
		exit ($this->json->encode(array_values($data)));
	}
	
	/**
     * 查看页面
     *
     * @aca 浏览
     */
	function view()
	{
		$pageid = intval($_GET['pageid']);
		if (!$pageid || !($page = $this->page->get($pageid)))
		{
			$this->showmessage('没有此页面，可能已经被删除');
		}
		else
		{
			$this->view->assign('head', array('title'=>$page['name'].'_页面'));
			$this->view->assign('page', $page);
			$this->view->assign('haspriv', priv::page($pageid));
			$this->view->display("page/view");
		}
	}

    /**
     * 区块列表
     *
     * @aca 区块
     */
	function sections()
	{
		$pageid = intval($_GET['pageid']);
		$return = array();
		$sections = $this->section->get_section($pageid, 'sectionid,pageid,type,name,nextupdate,published,locked,lockedby,updated,updatedby,created,createdby');
		foreach ($sections as $s)
		{
			if (!priv::section($s['sectionid'], $s['pageid'])) continue;

			$locked = $s['locked'] > TIME;
			$return[] = array(
				'sectionid'=>$s['sectionid'],
				'type'=>$s['type'],
				'name'=>$s['name'],
				'locked'=>$locked,
				'lockedby'=>($locked ? username($s['lockedby']) : '')
			);
		}
		exit($this->json->encode($return));
	}

    /**
     * 页面树形
     *
     * @aca 属性
     */
	function property()
	{
		$pageid = intval($_GET['pageid']);
		$page = $this->page->get($pageid);
		$path = $this->_uri($page['path'], 'path');
		$page['filesize'] = number_format(filesize($path) / 1024, 2);
		$this->view->assign('page',$page);

		if (!$_GET['from'])
		{
			$_GET['from'] = 1;
		}

		//days
		$days = array();
		for ($i = 1; $i <= 7; $i++)
		{
			$ct = TIME - (($i - 1)*86400);
			$name = date('m', $ct).'-'.date('d', $ct);
			switch ($i) {
				case 1:
					$name = '今天';
					break;
				case 2:
					$name = '昨天';
					break;
				case 3:
					$name = '前天';
					break;

				default:

					break;
			}
			$days[] = array('name'=>$name, 'subday'=>$i);
		}
		$this->view->assign('days', $days);

		//logs
		$logs = $this->log->getByPageid($pageid);
		$total = $this->log->getNum($pageid);
		$this->view->assign('total', $total);
		$this->view->assign('logs', $logs);

		$priv = loader::model('admin/page_priv');
		$this->view->assign('adminids', $priv->ls_userid($pageid));

		//page size
		$this->view->display('page/property');
	}

    /**
     * 区块日志
     *
     * @aca 区块日志
     */
	function sectionlog()
	{
		$page = $_GET['page'] ? $_GET['page'] : 0;
		$num = $_GET['num'] ? $_GET['num'] : 10;
		$pageid = intval($_GET['pageid']);
		$from = $_GET['from'] ? intval($_GET['from']) : 1;
		$return['pageid'] = $pageid;
		$logs = $this->log->getByPageid($pageid, $from, $page, $num);
		$total = $this->log->getNum($pageid, $from);
		$this->view->assign('logs', $logs);
		$return['logs'] = $logs;
		$return['total'] = $total;
		$return['state'] = true;
		$return['html'] = $this->view->fetch('page/log');

		echo $this->json->encode($return);
	}
	
	/**
     * 发布页面
     *
     * @aca 发布页面
     */
	function publish()
	{
		$priv = loader::model('admin/page_priv');

		$pageid = intval($_POST['pageid']);
		if (!$pageid || !($page = $this->page->get($pageid)))
		{
			exit('{"state":false,"error":"未知页面"}');
		}
		$json	= $this->_publish($page);

		$page['class'] = 'edit';
		if (!priv::page($page['pageid']) && !priv::section_page($page['pageid']))
		{
			if (priv::page($page['pageid'], true) || priv::section_page($page['pageid'], true))
			{
				$page['class'] = 'no';
			}
			else 
			{
				unset($data[$k]);
				return;
			}
		}
			
		$path = array("3");
		if ($page['parentids'])
		{
			foreach (explode(',',$page['parentids']) as $id)
			{
				$path[] = '000'.$id;
			}
		}
		$path[] = '000'.$page['pageid'];
		$page['clickpath'] = implode_ids($path);
		$admin = $priv->ls_userid($pageid);
		foreach ($admin as &$user)
		{
			$user = '<a href="javascript: url.member('.$user.');">'.username($user).'</a>';
		}
		$u = $this->_uri($page['path']);
		$page['admin'] = implode('&nbsp;', $admin);
		$page['size'] = number_format(round(intval(filesize($u['path'])) / 1024 * 100) / 100, 2) . 'KB';
		$page['published'] = $page['published'] ? date('Y-m-d H:i:s', $page['published']) : '未生成';

		$json['page']	= $page;
		exit($this->json->encode($json));
	}

	/**
     * 编辑页面
     *
     * @aca 编辑页面
     */
	function edit()
	{
		if (!($pageid = intval($_GET['pageid'])) || !($page = $this->page->get($pageid)))
		{
			$this->_error_out('无此页面');
		}
		if ($this->is_post())
		{
			$data = $_POST;
			$data['frequency'] = intval($data['frequency']);
			$data['nextpublish'] = strtotime(trim($data['nextpublish']));
			if ($data['nextpublish'] < TIME)
			{
				$data['nextpublish'] = TIME + $data['frequency'];
			}
			$tpl = $page['template'];
			if ($data['template'])
			{
				$this->_editTemplate($pageid, $data['template'], $data['clearsection']);
				$tpl = $data['template'];
			}
			else
			{
				unset($data['template']);
			}
			if (empty($data['path']))
			{
				unset($data['path']);
			}
			else
			{
				$data['url'] = $this->_uri($data['path'], 'url');
			}
			if ($this->page->edit($pageid, $data) !== false)
			{
				// 给模板加注释
				$this->_addTplComment($tpl, $data['name']);
	
				$json = array(
					'state' => true,
					'info' => '页面更新成功'
				);
			}
			else
			{
				$json = array('state'=>false, 'error'=>$this->page->error());
			}
			exit ($this->json->encode($json));
		}
		else
		{
			$page = $this->page->get($pageid);
			$this->view->assign('page', $page);
			$this->view->display('page/edit');
		}
	}

    /**
     * 可视编辑
     *
     * @aca 可视编辑
     */
	function visualedit()
	{
		$pageid = intval($_GET['pageid']);
		if (!$pageid)
		{
		    exit('not exists!!!');
		}
		$page = $this->page->get($pageid);

		$tpl_file = $this->_tplfile($page['template']);

		if(! file_exists($tpl_file))
		{
			exit('tpl file not exists!!!');
		}

		$html = $this->template->fetch($page['template']);

		$html = preg_replace('|<!--#include virtual=([\'"])[/]?section/(\d+)\.html\1-->|Uie','$this->_section_visual_wrapper(\2)', $html);
		$html = preg_replace('|<head>(.+)</head>|Uies', '$this->_section_visual_helper(\'\1\')', $html);
		$html = str_replace('</body>', $this->_section_visual_menu(), $html);

        // TODO FIXME!
        header('X-UA-Compatible: IE=EmulateIE7');
		echo $html;
	}

    /**
     * 预览
     *
     * @aca 预览
     */
	function preview()
	{
		$pageid = intval($_GET['pageid']);
		$tmp_template = 'page_'.$this->_userid.'_'.$pageid.'.tpl';
		$tpl = $this->_tmptplfile($tmp_template);
		if ($this->is_post())
		{
			if (!empty($_POST['data']))
			{
				$state = false !== write_file($tpl, $_POST['data']);
			}
			exit('{"state":'.($state ? 'true' : 'false').'}');
		}
		else
		{
			$orig_dir = $this->template->dir;
			$this->template->set_dir(CACHE_PATH);
			$html = $this->template->fetch($tmp_template);
			$this->template->set_dir($orig_dir);
			@unlink($tpl);

			$html = preg_replace('|<!--#include virtual=([\'"])[/]?section/(\d+)\.html\1-->|Uie','$this->_section_preview_wrapper(\2)', $html);
			$html = str_replace('</head>', $this->_section_preview_helper(), $html);
			echo $html;
		}
	}
	
	/**
     * 添加页面
     *
     * @aca 添加
     * @throws Exception
     */
	function add()
	{
		if ($this->is_post())
		{
			$data = $_POST;
			$data['frequency'] = intval($data['frequency']);
			$data['nextpublish'] = TIME + $data['frequency'];
			if (! $data['parentid'])
			{
				$data['parentid'] = null;
			}
			if (empty($data['name']))
			{
				exit('{"state":false, "error":"名称不能为空"}');
			}
			if (empty($data['template']))
			{
				exit('{"state":false, "error":"请选择模板"}');
			}
			if (empty($data['path']))
			{
				 exit('{"state":false, "error":"没有指定位置或文件名"}');
			}
			$data['url'] = $this->_uri($data['path'], 'url');
			$tpl = $data['template'];

			$db = factory::db();
			$db->beginTransaction();
			try {
				$pageid = $this->page->add($data);
				if (! $pageid)
				{
					throw new Exception('添加失败');
				}

				$json = array('state'=>true,'pageid'=>$pageid);
				$page = $this->page->get($pageid);
				$path = '3';
				if ($page['parentids'])
				{
					foreach (explode(',', $page['parentids']) as $id)
					{
						$path .= ',000'.$id;
					}
				}
				$path .= ',000'.$pageid;
				$page['clickpath'] = $path;
				$priv = loader::model('admin/page_priv');
				$admin = $priv->ls_userid($pageid);
				foreach ($admin as &$user)
				{
					$user = '<a href="javascript: url.member('.$user.');">'.username($user).'</a>';
				}
				$page['admin'] = implode('&nbsp;', $admin);
				$page['size'] = number_format(round(intval(filesize($this->_uri($page['path'],'path'))) / 1024 * 100) / 100, 2) . 'KB';
				$page['published'] = $page['published'] ? date('Y-m-d H:i:s', $page['published']) : '未生成';
				$json['path'] = $path;
				$json['data'] = $page;

				$db->commit();

				// 给模板加注释
				$this->_addTplComment($tpl, $data['name']);

				// 解析模板 创建多个section
				$this->_genTemplate($tpl, $pageid);

				exit ($this->json->encode($json));
			} catch (Exception $e) {
				$db->rollBack();
				exit($this->json->encode(array(
					'state'=>false,
					'error'=>$e->getMessage()
				)));
			}
		}
		else
		{
			$this->view->assign('parentid', intval($_GET['parentid']));
			$this->view->display('page/add');
		}
	}

	/**
     * 备份
     *
     * @aca 备份
     */
	function bakup()
	{
		if (!($pageid = intval($_REQUEST['pageid'])) || !($page = $this->page->get($pageid)))
		{
			$this->_error_out('无此页面');
		}
		if ($this->_bakup($page))
		{
			exit ('{"state":true,"info":"备份成功"}');
		}
		else
		{
			exit ('{"state":false,"error":"备份失败，请检查读写权限"}');
		}
	}

    /**
     * 备份文件列表
     *
     * @aca 备份
     */
	function bakSuggest()
	{
		$bakdir = ROOT_PATH.'data/bakup/page';
		$files = array();
		if ($handle = @opendir($bakdir))
		{
			while (false !== ($entry = readdir($handle)))
			{
				if ($entry == '.' || $entry == '..')
				{
					continue;
				}
				$f = $bakdir.'/'.$entry;
				if (is_file($f))
				{
					$files[] = $entry;
				}
			}
			closedir($handle);
		}
		
		$keyword = trim($_REQUEST['keyword']);
		$pageid = intval($_REQUEST['pageid']);
		if ($keyword)
		{
			$files = preg_grep('/'.preg_quote($keyword).'/', $files);
		}
		if ($pageid)
		{
			$files = preg_grep("/\($pageid\)/", $files);
		}
		$data = array();
		foreach ($files as $f)
		{
			$data[] = array(
				'text'=>$f,
				'time'=>filemtime($bakdir.'/'.$f)
			);
		}
		$sort_func = create_function('$a, $b', 'return $b[time]-$a[time];');
    	usort($data, $sort_func);
    	exit (json_encode($data));
	}

    /**
     * 恢复
     *
     * @aca 恢复
     */
	function recover()
	{
		$pageid = intval($_REQUEST['pageid']);
		if (isset($_REQUEST['pageid']))
		{
			if (!$pageid || !($page = $this->page->get($pageid)))
			{
				exit ('{"state":false, "error":"不存在此目标页"}');
			}
		}
		$bakfile = preg_replace('#\.+|[/\\:*?"\'<>|\n]#', '', $_POST['bakfile']);
		$bakfile = ROOT_PATH."data/bakup/page/$bakfile";
		if (!is_file($bakfile) ||
			FALSE === ($content = @file_get_contents($bakfile)))
		{
			exit ('{"state":false, "error":"读取备份失败"}');
		}
		// read head data
		$head = array();
		$data = null;
		if (! preg_match('/^<!---#(.*)#--->/Us', $content, $head))
		{
			exit ('{"state":false, "error":"备份文件头部信息丢失"}');
		}
		$content = substr($content, strlen($head[0]));
		$head = $this->_string_to_array(htmlspecialchars_decode($head[1]));
		if (! is_array($head))
		{
			exit ('{"state":false, "error":"备份文件头部信息丢失"}');
		}
		$data = array_intersect_key($head, array(
			'pageid'	=> null,
			'parentid'	=> null,
			'name'		=> null,
			'template'	=> null,
			'path'		=> null,
			'url'		=> null,
			'frequency'	=> null,
			'nextpublish'=> null
		));
		$update = true;
		if (!$page)
		{
			$pageid = intval($data['pageid']);
			if (!($page = $this->page->get($pageid)))
			{
				$update = false;
			}
		}
		$data['url'] = $this->_uri($data['path'], 'url');
		
		// 删除原有区块
		$this->section->delete("pageid=$pageid");
		if ($update)
		{
			// 先备份
			$this->_bakup($page);
			unset($data['pageid'], $data['parentid'], $data['frequency']);
			$this->page->edit($pageid, $data);
		}
		else
		{
			if (!($pageid = $this->page->add($data)))
			{
				exit ('{"state":false, "error":"添加入库失败"}');
			}
		}
		
		$tpl = $data['template'];
		$content = $this->_genHTMLData($content, $pageid);
		write_file($this->_tplfile($tpl), $content);
		// 给模板加注释
		$this->_addTplComment($tpl, $data['name']);
		$json = array('state'=>true, 'info'=>'恢复成功');
		$page = $this->page->get($pageid);
		$path = '3';
		if ($page['parentids'])
		{
			foreach (explode(',', $page['parentids']) as $id)
			{
				$path .= ',000'.$id;
			}
		}
		$path .= ',000'.$pageid;
		$page['clickpath'] = $path;
		$priv = loader::model('admin/page_priv');
		$admin = $priv->ls_userid($pageid);
		foreach ($admin as &$user)
		{
			$user = '<a href="javascript: url.member('.$user.');">'.username($user).'</a>';
		}
		$page['admin'] = implode('&nbsp;', $admin);
		$page['size'] = number_format(round(intval(filesize($this->_uri($page['path'],'path'))) / 1024 * 100) / 100, 2) . 'KB';
		$page['published'] = $page['published'] ? date('Y-m-d H:i:s', $page['published']) : '未生成';
		$json['path'] = $path;
		$json['data'] = $page;

		exit ($this->json->encode($json));
	}

    /**
     * 页面导出
     *
     * @aca 导出
     */
	function exportTemplate()
	{
		if (!($pageid = intval($_GET['pageid'])) || !($page = $this->page->get($pageid)))
		{
			$this->_error_out('无此页面');
		}
		$content = $this->_export($page);
		$fileName = htmlspecialchars(basename($tpl));
		header('Content-Type:text/plain');
        header('Content-Disposition:attachment; filename='.$fileName.'; charset=utf-8');
        header('Content-Length:'.strlen($content));
        header('Cache-Control:public, must-revalidate, max-age=0');
        header('Content-Transfer-Encoding:binary');
        echo $content;
        exit;
	}

	/**
     * 删除
     *
     * @aca 删除
     */
	function delete()
	{
		$pageid = intval($_POST['pageid']);
		do {
			$childs = $this->page->childpages($pageid);
			if ($childs)
			{
				$return = array(
					'state'=>false,
					'pageid'=>$pageid,
					'error'=>'要删除的页面还有'.count($childs).'个子页面，请先删除子页面后再删除当前页',
					'childs'=>$childs
				);
				break;
			}
			$page = $this->page->select($pageid);
			$page = reset($page);
			// 删除前先备份
			$this->_bakup($page);
			if ($this->page->delete($pageid))
			{
				$path = '3';
				if ($page['parentids'])
				{
					foreach (explode(',',$page['parentids']) as $id)
					{
						$path .= ',000'.$id;
					}
				}
				$return = array(
					'state'=>true,
					'info'=>'页面删除成功',
					'path'=>$path
				);
				break;
			}

			$return = array(
				'state'=>false,
				'error'=>$this->page->error()
			);
		} while(0);
		exit($this->json->encode($return));
	}
	
	/**
	 * 区块模版代码检测
	 *
	 * @aca public 区块代码检测
	 */
	public function test()
	{
		// task exists
		if ($this->_hasPid() || is_array($this->_readProc()))
		{
			exit('{"state":false}');
		}
		$this->_setPid();
		// create task
		$task = $this->_createTask();
		$total = count($task);
		$current = '';
		$percent = $total ? 0 : 1;
		$proceed = 0;
		$results = array();
		$proc = array(
			'current' => &$current,
			'percent' => &$percent,
			'proceed' => &$proceed,
			'results' => &$results
		);
		$this->_writeProc($proc);
		ignore_user_abort(true);
		set_time_limit(0);
		while (null !== ($entry = array_shift($task))) {
			// catch stop signal
			if (!$this->_hasPid())
			{
				$this->_delProc();
				break;
			}
			$current = $entry['name'];
			$error = $this->_testTemplate($entry['template']);
			$results[] = $error ? ($current.' -> <b style="color:red">'.$error.'</b> <a href="javascript:;" onclick="app.editTpl('.$entry['pageid'].','.$entry['sectionid'].')">[编辑]</a>') : null;
			$percent = ++$proceed / $total;
			$this->_writeProc($proc);
		}
		$this->_delPid();
		exit('{"state":true}');
	}
	
	/**
	 * 区块模版代码检测停止
	 *
	 * @aca public 区块代码检测
	 */
	public function stopTest()
	{
		$this->_delPid();
		if ($_GET['clear']) {
			$this->_delProc();
		}
	}
	
	/**
	 * 区块模版代码检测ping
	 *
	 * @aca public 区块代码检测
	 */
	public function pingTest()
	{
		if (!is_array($proc = $this->_readProc()))
		{
			exit('{"state":false}');
		}
		if (!isset($_GET['proceed'])) {
			exit('{"state":true}');
		}
		$json = array(
			'state'   => true,
			'current' => $proc['current'],
			'percent' => $proc['percent'],
			'proceed' => $proc['proceed']
		);
		$oproceed = intval($_GET['proceed']);
		if ($oproceed < $json['proceed'])
		{
			$json['results'] = array_values(array_filter(array_slice($proc['results'], $oproceed)));
		}
		exit($this->json->encode($json));
	}
	private function _writeProc($proc)
	{
		$this->cache->set('SECTION_CHECK_PROCID', $proc);
	}
	private function _readProc()
	{
		return $this->cache->get('SECTION_CHECK_PROCID');
	}
	private function _delProc()
	{
		$this->cache->rm('SECTION_CHECK_PROCID');
	}
	private function _setPid()
	{
		$this->cache->set('SECTION_CHECK_PID', '1');
	}
	private function _delPid()
	{
		$this->cache->rm('SECTION_CHECK_PID');
	}
	private function _hasPid()
	{
		return $this->cache->get('SECTION_CHECK_PID') === '1';
	}
	
	private function _createTask()
	{
		$where = "`type`<>'html' AND `output` LIKE '%html%'";
		$fields = '`sectionid`,`pageid`,`name`,`data`,`template`,`type`';
		$order = '`sectionid` DESC';
		$data = $this->section->select($where, $fields, $order);
		foreach ($data as &$d) {
			if ($d['type'] == 'auto') {
				$d['template'] = $d['data'];
			}
			unset($d['data']);
			$d['name'] = $this->_getPagePath($d['pageid'])." / {$d['name']}({$d['sectionid']})";
		}
		return $data;
	}
	private function _getPagePath($pageid)
	{
		static $pathCache = array();
		if (isset($pathCache[$pageid])) {
			return $pathCache[$pageid];
		}
		$page = $this->page->get("`pageid`=$pageid", '`name`,`parentid`');
		$path = "{$page['name']}($pageid)";
		if ($page['parentid'] != null) {
			$path = $this->_getPagePath($page['parentid']).' / '.$path;
		}
		return $pathCache[$pageid] = $path;
	}

	
	protected function _publish($page)
	{
		$pageid = $page['pageid'];
		$this->page->nextpublish($page);
		$u = $this->_uri($page['path']);
		$page_file = $u['path'];
		$page_url = $u['url'];
        $this->template->assign($page);
		$page_html = $this->template->fetch($page['template']);
		if (preg_match('|[\//]$|', $page_file))
		{
			$page_file .= 'index'.SHTML;
		}
		import('helper.folder');
		if (!folder::create(dirname($page_file)) ||
		    false === write_file($page_file, $page_html))
		{
			return array(
				'state'=>false,
				'error'=>'写入文件失败'
			);
		}
		$this->page->published($pageid, array(
			'url'=>$page_url
		));
		$s_ok = 0;
		$s_error = 0;
		$where = "pageid=$pageid AND (frequency=0 OR (nextupdate>published AND nextupdate<=".TIME."))";
		foreach ($this->section->select($where) as $section)
		{
			$type = strtolower($section['type']);
			if ($this->{'_publish'.ucfirst($type)}($section))
			{
				$s_ok += 1;
			}
			else
			{
				$s_error += 1;
			}
		}
		return array(
		    'state'=>true,
		    'info'=>"页面生成成功，更新区块:<b class=\"c_green\">{$s_ok}成功</b>，<b class=\"c_red\">{$s_error}失败</b>。<a href='".$page_url."' target='_blank'>点击查看</a>"
		);
	}
	protected function _section_preview_wrapper($sectionid)
	{
		$section =  $this->section->get($sectionid);
	    return $this->_section_view_html($section);
	}
	protected function _section_visual_wrapper($sectionid)
	{
	    $section =  $this->section->get($sectionid);
	    $html = $this->_section_view_html($section);
	    if (!priv::section($sectionid, $section['pageid']))
	    {
	    	return $html;
	    }
	    // 已发布，表示显示的是最新的
	    $class = ($section['published'] && $section['published'] >= $section['updated']) ? 'published' : 'updated';
	    return '<span class="section '.$class.'" type="'.$section['type'].'" title="'.$section['name'].'" id="'.$sectionid.'">'.$html.'</span>';
	}
	protected function _section_visual_helper($str)
	{
	    $str = str_replace('\\"', '"', $str);
	    $scripts = array(
	    	IMG_URL.'js/lib/jquery.js',
	    	IMG_URL.'js/lib/jquery.ui.js',
	    	IMG_URL.'js/lib/jquery.form.js',
	    	IMG_URL.'js/lib/cmstop.contextMenu.js',
	    	IMG_URL.'js/cmstop.js',
	    	IMG_URL.'js/config.js',
	    	IMG_URL.'js/lib/cmstop.dialog.js',
	    	IMG_URL.'js/lib/cmstop.tree.js',
            IMG_URL.'js/lib/cmstop.list.js',
	    	'uploader/cmstop.uploader.js',
            'imageEditor/cmstop.imageEditor.js',
	    	'js/cmstop.filemanager.js',
	    	IMG_URL.'js/lib/jquery.colorPicker.js',
	    	IMG_URL.'js/lib/cmstop.suggest.js',
	    	IMG_URL.'js/lib/cmstop.editplus.js',
	    	'js/cmstop.editplus_plugin.js',
	    	IMG_URL.'js/lib/cmstop.datepicker.js',
	    	'js/cmstop.tabnav.js',
	    	'apps/page/js/scrolltable.js',
	    	'apps/page/js/visual_edit.js'
	    );
	    if (preg_match_all('#<script[^>]+src\s*=\s*(["\'])?([^>"\']+)\1?[^>]*>#i', $str, $matches))
	    {
	    	$scripts = array_diff($scripts, $matches[2]);
	    }
	    foreach ($scripts as &$f)
	    {
	    	$f = '<script type="text/javascript" src="'.$f.'"></script>';
	    }
	    $scripts[] = '<script type="text/javascript">window.onload = init;</script>';
	    $scripts = implode("\n", $scripts);
	    $styles = array(
			'<link rel="stylesheet" type="text/css" href="'.IMG_URL.'js/cmstop/style.css" />',
			'<link rel="stylesheet" type="text/css" href="'.IMG_URL.'js/lib/jquery-ui/dialog.css" />',
			'<link rel="stylesheet" type="text/css" href="'.IMG_URL.'js/lib/suggest/style.css" />',
			'<link rel="stylesheet" type="text/css" href="'.IMG_URL.'js/lib/list/style.css" />',
            '<link rel="stylesheet" type="text/css" href="'.IMG_URL.'js/lib/editplus/style.css" />',
			'<link rel="stylesheet" type="text/css" href="'.IMG_URL.'js/lib/tree/style.css" />',
			'<link rel="stylesheet" type="text/css" href="'.IMG_URL.'js/lib/contextMenu/style.css" />',
			'<link rel="stylesheet" type="text/css" href="'.IMG_URL.'js/lib/datepicker/style.css" />',
            '<link rel="stylesheet" type="text/css" href="apps/page/css/visual_edit.css" />',
		);
		$styles = implode("\n", $styles);
		return "<head>\n{$str}\n{$styles}\n{$scripts}\n</head>";
	}
	protected function _section_visual_menu()
	{
	    return '
    <!--右键菜单，区块-->
    <ul id="section_menu_html" class="contextMenu">
       <li class="edit"><a href="#edit">编辑</a></li>
       <li class="edit"><a href="#property">设置</a></li>
       <li ><a href="#publish">发布</a></li>
       <li ><a href="#moveDown">下移层</a></li>
    </ul>
    <ul id="section_menu_grap" class="contextMenu">
       <li class="edit"><a href="#property">设置</a></li>
       <li><a href="#grap">抓取</a></li>
       <li ><a href="#moveDown">下移层</a></li>
    </ul>

    <!--手动区块菜单-->
    <ul id="section_item_menu" class="contextMenu">
       <li class="edit"><a href="#edititem">编辑</a></li>
       <li class="edit"><a href="#replaceitem">替换</a></li>
       <li class="delete"><a href="#delitem">删除</a></li>
       <li class=""><a href="#moveitemleft">左移</a></li>
       <li class=""><a href="#moveitemright">右移</a></li>
       <li class=""><a href="#viewitem">查看</a></li>
    </ul>
    <ul id="section_cell_menu" class="contextMenu">
       <li class="new"><a href="#additem">添加项</a></li>
       <li class="delete"><a href="#delrow">删除行</a></li>
       <li><a href="#uprow">上移行</a></li>
       <li><a href="#downrow">下移行</a></li>
       <li class="new"><a href="#addrowafter">添加行</a></li>
    </ul>
</body>';
	}
	protected function _editTemplate($pageid, $tpl, $clearSection = false)
	{
		if ($clearSection)
		{
			$this->section->delete("pageid=$pageid");
		}
		$this->_genTemplate($tpl, $pageid);
	}
	protected function _bakup($page)
	{
		$name = preg_replace('#\.+|[/\\:*?"\'<>|\n]#', '', $page['name']);
		$date = date('Y-m-d H;i;s');
		$bakdir = ROOT_PATH.'data/bakup/page';
		$bakfile = "$bakdir/$name($page[pageid])($date)";
		if (! is_dir($bakdir)) folder::create($bakdir);
		$content = $this->_export($page);
		return write_file($bakfile, $content);
	}
	protected function _export($page)
	{
		$tpl = $page['template'];
		$tplsrc = $this->_tplfile($tpl);
		if (!($content = @file_get_contents($tplsrc)))
		{
			return '页面设置的模板不存在';
		}
		$content = preg_replace(
			'|<!--#include virtual=([\'"])[/]?section/(\d+)\.html\1-->|Uie',
			'$this->_exportSection(\'\2\')',
			$content
		);
		return "<!---#".htmlspecialchars($this->_array_to_string($page))."#--->\n".$content;
	}
	protected function _exportSection($sectionid)
	{
		if (!($section = $this->section->get($sectionid)))
		{
			return '<!--#include virtual="/section/'.$sectionid.'.html"-->';
		}
		$html = array(
			'<!--{section sectionid="'.$sectionid.'"',
			'name="'.htmlspecialchars($section['name']).'"',
			'type="'.$section['type'].'"',
			'width="'.$section['width'].'"',
		);
		if ($section['type'] != 'html')
		{
			$html[] = 'frequency="'.$section['frequency'].'"';
		}
		if ($section['type'] == 'hand')
		{
			$html[] = 'rows="'.$section['rows'].'"';
			$html[] = 'origdata="'.htmlspecialchars($section['data']).'"';
			$html[] = 'output="'.$section['output'].'"';
		}
		if (in_array($section['type'], array('feed','json','rpc')))
		{
			$html[] = 'url="'.$section['url'].'"';
		}
		if ($section['type']=='rpc')
		{
			$html[] = 'method="'.$section['method'].'"';
			$html[] = 'args="'.$section['args'].'"';
		}
		$html[] = 'description="'.htmlspecialchars($section['description']).'"';
		$html = implode("\n\t", $html)."}-->\n";
		if (in_array($section['type'], array('html','auto')))
		{
			$html .= $section['data'];
		}
		else
		{
			$html .= $section['template'];
		}
		$html .= '<!--{/section}-->';
		return $html;
	}
	protected function _buildPageTips($page)
	{
		$tips = array();
		$tips[] = 'ID：'.$page['pageid'];
		$tips[] = '网址：'.$page['url'];
		$tips[] = '创建：'.username($page['createdby']).'（'.date('Y-m-d H:i:s',$page['created']).'）';
		$tips[] = '下次更新：'.date('Y-m-d H:i:s',$page['nextpublish']);
		if ($page['updated'])
		{
			$tips[] = '最后修改：'.username($page['updatedby']).'（'.date('Y-m-d H:i:s',$page['updated']).'）';
		}
		return implode('<br />', $tips);
	}
	protected function _addTplComment($tpl,$name)
	{
		$tplinfo = pathinfo($this->_tplfile($tpl));
		$realPath = str_replace('\\','/',$tplinfo['dirname']);
		$entry = $tplinfo['basename'];
		$comments = @include $tplinfo['dirname'] .'/notes.php';
        if (empty($comments))
        {
            $comments = array($entry=>$name);
        }
        else
        {
            $comments[$entry] = $name;
        }
        $notes = '<?php'.PHP_EOL.'return '.var_export($comments,true).';';
        write_file($realPath .'/notes.php', $notes);
	}
	protected function _genTemplate($tpl, $pageid)
	{
		$tplsrc = $this->_tplfile($tpl);
		if (!($content = @file_get_contents($tplsrc)))
		{
			return;
		}
		write_file($tplsrc, $this->_genHTMLData($content, $pageid));
	}
	protected function _genHTMLData($content, $pageid)
	{
		return preg_replace(
			'#<!--{section\s+(.*)}-->(.*)<!--{/section}-->#Uise',
			'$this->_genSection(\'\1\',\'\2\',$pageid)',
			$content
		);
	}
	protected function _genSection($args, $code, $pageid)
	{
		$args = str_replace('\\"', '"', $args);
		$code = str_replace('\\"', '"', $code);
		$data = array();
		preg_match_all(
			'/\b(\w+)\s*=\s*(["\'])?([^"\']*)\2/isU',
			 $args, $m);
		foreach ($m[1] as $i=>$tag)
		{
			$data[$tag] = htmlspecialchars_decode($m[3][$i]);
		}
		if (intval($data['pageid']) < 1)
		{
			$data['pageid'] = $pageid;
		}
		$type = empty($data['type']) ? 'html' : strtolower($data['type']);
		$data['data'] = $code;
		$data = $this->{'_add'.ucfirst($type).'Data'}($data);
		if (is_array($data))
		{
			if ($sectionid = $this->section->add($data))
			{
				$data = $this->section->get($sectionid);
				try {
					$this->{'_publish'.ucfirst($type)}($data);
				} catch (Exception $e) {}
				return '<!--#include virtual="/section/'.$sectionid.'.html"-->';
			}
		}
		return $code;
	}
	protected function _uri($path, $type = null)
	{
		$u = $this->uri->psn($path);
		if ($type == 'path')
		{
			$u = $u['path'];
		}
		elseif ($type == 'url')
		{
			$u = $u['url'];
		}
		return $u;
	}

	/**
	 * 计划任务
     *
     * @aca 计划任务
	 */
	function cron()
	{
		@set_time_limit(600);

		$topublish = $this->page->cron_publish();
		$info = array();
		if (!empty($topublish))
		{
			foreach ($topublish as $page)
			{
				$rs = $this->_publish($page);
				if (!$rs['state'])
				{
					$info[] = $rs['error'];
				}
			}
		}
		$json = array(
			'state'=>true
		);
		if ($info)
		{
			$json['info'] = implode('<br />', $info);
		}
		exit ($this->json->encode($json));
	}

    /**
     * 定时备份
     *
     * @aca 定时备份
     */
	function cron_bakup()
	{
		@set_time_limit(600);
		$bakdir = ROOT_PATH.'data/bakup/page';
		$date = date('Y-m-d H');
		if (!is_dir($bakdir))
		{
			folder::create($bakdir);
		}
		foreach ($this->page->select() as $page)
		{
			$content = $this->_export($page);
			$name = preg_replace('#\.+|[/\\:*?"\'<>|\n]#', '', $page['name']);
			$bakfile = "$bakdir/$name($page[pageid])($date)";
			write_file($bakfile, $content);
		}
		exit ('{"state":true}');
	}
}