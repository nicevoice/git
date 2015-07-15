<?php
/**
 * 在线设计
 *
 * @aca 在线设计
 */
final class controller_admin_online extends special_controller_abstract
{
	protected $indexer;
	/**
	 * @var model_admin_psn
	 */
	protected $psn;
	/**
	 * @var model_admin_special
	 */
	protected $special;
	/**
	 * @var model_admin_special_page
	 */
	protected $specialPage;
	/**
	 * @var model_admin_widget
	 */
	protected $widget;
	
	protected $env, $themeDef, $themeUsed, $usedDir;

	const DIY_CLASS_ROOT	= 'diy-root';
	const DIY_CLASS_AREA	= 'diy-area';
	const DIY_CLASS_FRAME	= 'diy-frame';
	const DIY_CLASS_WIDGET	= 'diy-widget';
	const DIY_CLASS_INNER   = 'diy-inner';
	const DIY_CLASS_CONTENT = 'diy-content';
	const DIY_CLASS_TITLE	= 'diy-title';
	const DIY_CLASS_MODIFIED = 'modified';

	function __construct(& $app)
	{
		parent::__construct($app);
		define('SPEC_PATH', IMG_PATH.'apps/special/');
		define('SPEC_URL', IMG_URL.'apps/special/');
        define('SPEC_BACKEND_URL', ADMIN_URL.'apps/special/');
		$this->special = loader::model('admin/special', 'special');

        // 专题数量授权
        $specials = factory::db()->get("SELECT COUNT(*) AS `total` FROM `#table_special`");
		if ($specials && ! license('system', array('specials' => intval($specials['total']))))
        {
            cmstop::licenseFailure('系统中的专题数超出了您的授权数量');
        }

        $this->psn = loader::model('admin/psn', 'system');
		
		$this->specialPage = loader::model('admin/special_page', 'special');
		
		$this->widget = loader::model('admin/widget', 'special');
		
		import('helper.folder');
		import('helper.xml');
		
		import('helper.resource');
		resource::init();
		resource::setMacro('SPEC_URL', SPEC_URL);
        resource::setMacro('SPEC_BACKEND_URL', SPEC_BACKEND_URL);
		resource::setMacro('WIDGET_URL', SPEC_URL.'widget/');
		
		loader::import('lib.widgetEngine');
		$env = get_object_vars($this);
		$env['controller'] = $this;
		widgetEngine::setEnv($env);
	}

    /**
     * 浏览
     *
     * @aca 浏览
     */
	function index()
	{
		$this->view->display('index');
	}

    /**
     * 锁定
     *
     * @aca 锁定
     */
	function lock()
	{
		if ($pageid = intval($_REQUEST['pageid']))
		{
			$this->specialPage->lock($pageid);
		}
	}

    /**
     * 解锁
     *
     * @aca 解锁
     */
	function unlock()
	{
		if ($pageid = intval($_REQUEST['pageid']))
		{
			$this->specialPage->unlock($pageid);
		}
	}

    /**
     * 推送
     *
     * @aca 推送
     */
	function recommend()
	{
		$rowset = array();
		$db = factory::db();
		// contentid
		if ($_GET['contentid'])
		{
			$rowset = $db->select("SELECT c.contentid, b.placeid FROM #table_place_data a
				 LEFT JOIN #table_place b ON b.placeid=a.placeid
				 LEFT JOIN #table_special_page c ON c.pageid=b.pageid
				 WHERE a.contentid=" . intval($_GET['contentid']));
		}
		// placeid
		elseif ($_GET['placeid'])
		{
			$placeid = explode(',', $_GET['placeid']);
			$placeid = array_map('intval', array_unique(array_filter($placeid)));
			if ($placeid)
			{
				$rowset = $db->select("SELECT c.contentid, b.placeid FROM #table_place b
					LEFT JOIN #table_special_page c ON c.pageid=b.pageid
					WHERE b.placeid IN (".implode_ids($placeid).")");
			}
		}
		$data = array();
		foreach ($rowset as $r)
		{
			if ($r['contentid'])
			{
				$data[$r['contentid']][] = $r['placeid'];
			}
		}
		$this->view->assign('checkedPlace', $this->json->encode($data));
		if ($contentids = array_keys($data))
		{
			$contentset = $db->select("SELECT contentid, title, url FROM #table_content WHERE contentid IN (".implode_ids($contentids).")");
			$this->view->assign('checkedContent', $this->json->encode($contentset));
		}
		$this->view->display('recommend');
	}

    /**
     * 推送列表
     *
     * @aca 推送
     */
    function search()
	{
		$page = intval($_GET['page']);
		if ($page < 1)
		{
			$page = 1;
		}
		$size = 10;
        $range_sql = "";
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
                $range_sql = "c.`published` >= " . mktime(0, 0, 0, $n, $j, $Y);
			}
			// mktime(0, 0, 0, $n, $j-1, $Y) =< 昨日 < mktime(0, 0, 0, $n, $j, $Y)
			elseif ($_GET['range'] == 'tomorrow')
			{
				$range_sql = "c.`published` >= " . mktime(0, 0, 0, $n, $j-1, $Y) . " AND c.`published` < " . mktime(0, 0, 0, $n, $j, $Y);
			}
			// 本周 >= mktime(0, 0, 0, $n, $j - $N + 1, $Y)
			elseif ($_GET['range'] == 'week')
			{
				$range_sql = "c.`published` >= " . mktime(0, 0, 0, $n, $j - $N + 1, $Y);
			}
			// 本月 >= mktime(0, 0, 0, $n, 1, $Y)
			elseif ($_GET['range'] == 'month')
			{
				$range_sql = "c.`published` >= " . mktime(0, 0, 0, $n, 1, $Y);
			}
		}

        $keywords_sql = "";
		if (trim($_GET['keywords']))
		{
			$keywords_sql = where_keywords("c.`title`", $_GET['keywords']);
		}

        $where = "WHERE c.`contentid` IN (
                    SELECT DISTINCT c.`contentid`
                    FROM `#table_content` c
                    RIGHT JOIN `#table_special_page` sp ON sp.`contentid` = c.`contentid`
                    WHERE sp.`pageid` IN (
                        SELECT DISTINCT p.`pageid` FROM `#table_place` p
                        LEFT JOIN `#table_widget` w ON w.`widgetid` = p.`placeid`
                        WHERE w.`status` >= 0
                    )
                    AND c.`status` = 6";
        if ($range_sql) $where .= " AND $range_sql";
        if ($keywords_sql) $where .= " AND $keywords_sql";
        $where .= ")";

		$db = factory::db();
		$data = $db->page("SELECT c.`contentid`, c.`title`, c.`url` FROM `#table_content` c $where", $page, $size);
		$r = $db->get("SELECT COUNT(*) as `total` FROM `#table_content` c $where");
		$total = $r['total'];
		$json = array('state'=>true, 'data'=>$data, 'total'=>$total);
		exit ($this->json->encode($json));
	}

    /**
     * 选择位置
     *
     * @aca 选择位置
     */
	function getPlace()
	{
		$contentid = intval($_GET['contentid']);
		$db = factory::db();
		$rowset = $db->select("SELECT b.contentid, b.name as pname, a.*
			FROM #table_place a
			LEFT JOIN #table_widget d ON d.widgetid=a.placeid
			LEFT JOIN #table_special_page b ON b.pageid=a.pageid
			WHERE b.contentid=$contentid AND d.status>-1");
		$data = array();
		foreach ($rowset as &$r)
		{
			if (!isset($data[$r['pageid']]))
			{
				$data[$r['pageid']] = array(
					'name'=>$r['pname'],
					'places'=>array()
				);
			}
			$data[$r['pageid']]['places'][] = $r;
		}
		exit ($this->json->encode(array_values($data)));
	}

    /**
     * 增加页面
     *
     * @aca 增加页面
     */
	function addPage()
	{
		if (! ($contentid = intval($_REQUEST['contentid'])))
		{
			$this->_errorOut('缺少参数:contentid');
		}
		if (!($special = $this->special->get($contentid, 'path')))
		{
			$this->_errorOut('不存在该专题');
		}
		if ($this->is_post())
		{
			$file = preg_replace('/[^\w\-\.]/', '', $_POST['file']);
			if (! strlen($file))
			{
				exit ('{"state":false,"error":"文件名不能为空"}');
			}
			$pos = $this->psn->parse($special['path'].'/'.$file.SHTML);
			if (file_exists($pos['path']))
			{
			    exit ('{"state":false,"error":"文件已存在"}');
			}
			$file = $pos['psn'];
			if ($this->specialPage->count("contentid=$contentid AND file='$file'") > 0)
			{
				exit ('{"state":false,"error":"文件名重复"}');
			}
			$url = $pos['url'];
			$name = strip_tags($_POST['name']);
			$pageid = $this->specialPage->add(array(
				'contentid'=>$contentid,
				'name'=>$name,
				'file'=>$file,
				'url'=>$url,
				'frequency'=>intval($_POST['frequency'])
			));
			if (!$pageid)
			{
				exit ('{"state":false, "error":"添加失败"}');
			}
			$template = null;
			$data = array(
				'head'=>array(
					'meta' => array(
						'Content-Type' => 'text/html; charset=UTF-8',
					)
				)
			);
			if ($_POST['scheme']
				&& preg_match('/^[\w\-]+$/', $_POST['scheme'])
				&& ($scheme = file_get_xmlarray(SPEC_PATH.'scheme/'.$_POST['scheme'].'/notes.xml', 'root')) )
			{
				$_REQUEST['pageid'] = $pageid;
				$data = decodeData($scheme['data']);
				$this->_parseData($data);
				$template = $scheme['template'];
                if (is_array($data['head']['resource']))
                {
                    $data['head']['resource'] = $this->_useRes($_POST['scheme'], $data['head']['resource'], $pos, $pageid);
                }
			}
			elseif (!empty($_POST['template']))
			{
				$template = $_POST['template'];
			}
			$data['head']['title'] = $name;
			$data['head']['meta']['Keywords'] = strip_tags($_POST['keywords']);
			$data['head']['meta']['Description'] = strip_tags($_POST['description']);
			if (!empty($data))
			{
				$this->specialPage->update(array(
					'data'=>encodeData($data),
					'template'=>$template
				), $pageid);
			}
			exit ($this->json->encode(array(
				'state'=>true,
				'data'=>array(
					'pageid'=>$pageid,
					'name'=>$name,
					'url'=>$url
				)
			)));
		}
		else
		{
			$hasIndex = $this->specialPage->count("contentid=$contentid AND file LIKE '%/index".SHTML."'") > 0;
			$template = $this->_readNotes(SPEC_PATH.'templates', SPEC_URL.'templates');
			$pubpos = $this->psn->parse($special['path'].'/resource/templates');
			$addtemplate = $this->_readNotes($pubpos['path'], $pubpos['url'], $pubpos['psn']);
			$template = array_merge($template, $addtemplate);
			$this->view->assign(array(
				'isajax'=>$this->is_ajax(),
				'name'=>($hasIndex ? '新页面' : '首页'),
				'file'=>($hasIndex ? '' : 'index'),
				'scheme'=>$this->_readNotes(SPEC_PATH.'scheme', SPEC_URL.'scheme'),
				'template'=>$template
			));
			$this->view->display('addPage');
		}
	}
	protected function _readNotes($basePath, $baseUrl, $baseEntry = null)
	{
		$data = array();
		$baseEntry = $baseEntry ? ($baseEntry . '/') : '';
		if ($h = opendir($basePath))
		{
			while (false !== ($entry = readdir($h)))
			{
				if ($entry == '.' || $entry == '..') continue;
				$path = $basePath .'/'.$entry;
				$notes = $path .'/notes.xml'; 
				if (is_dir($path) && is_file($notes))
				{
					$detail = file_get_xmlarray($notes, 'root');
					$name = $detail['name'] ? $detail['name'] : $entry;
					$thumb = $detail['thumb'] ? ($baseUrl .'/'. $entry .'/'.$detail['thumb']) : '';
					$data[] = array(
						'entry' => $baseEntry . $entry,
						'thumb' => $thumb,
						'name' => $name
					);
				}
			}
			closedir($h);
		}
		return $data;
	}

    /**
     * 设置页面
     *
     * @aca 设置页面
     */
	function setPage()
	{
		if (! ($pageid = intval($_REQUEST['pageid']))
			|| !($page = $this->specialPage->get($pageid, 'name, file, frequency')))
		{
			$this->_errorOut('页面不存在');
		}
		if ($this->is_post())
		{
			$file = preg_replace('/[^\w\-\.]/', '', $_POST['file']);
			if (! strlen($file))
			{
				exit ('{"state":false,"error":"文件名不能为空"}');
			}
			$pos = $this->psn->parse(dirname($page['file']).'/'.$file.SHTML);
			$file = $pos['psn'];
			$url = $pos['url'];
			$name = strip_tags($_POST['name']);
			if ($this->specialPage->update(array(
				'name'=>$name,
				'file'=>$file,
				'url'=>$url,
				'frequency'=>intval($_POST['frequency'])
			), $pageid))
			{
				exit($this->json->encode(array(
					'state'=>true,
					'name'=>$name,
					'url'=>$url
				)));
			}
			else
			{
				exit('{"state":false,"error":"更新失败"}');
			}
		}
		else
		{
			$page['file'] = pathinfo($page['file'], PATHINFO_FILENAME);
			$this->view->assign($page);
			$this->view->display('setPage');
		}
	}

    /**
     * 拷贝页面
     *
     * @aca 拷贝页面
     */
	function copyPage()
	{
		if (! ($pageid = intval($_REQUEST['pageid']))
			|| !($page = $this->specialPage->get($pageid, 'contentid, data, name, file, template, frequency')))
		{
			$this->_errorOut('页面不存在');
		}
		if ($this->is_post())
		{
			$file = preg_replace('/[^\w\-\.]/', '', $_POST['file']);
			if (! strlen($file))
			{
				exit ('{"state":false,"error":"文件名不能为空"}');
			}
			$pos = $this->psn->parse(dirname($page['file']).'/'.$file.SHTML);
			$file = $pos['psn'];
			$url = $pos['url'];
			$name = strip_tags($_POST['name']);
			
			$pageid = $this->specialPage->add(array(
				'contentid'=>$page['contentid'],
				'data'=>$page['data'],
				'name'=>$name,
				'file'=>$file,
				'url'=>$url,
				'template'=>$page['template'],
				'frequency'=>intval($_POST['frequency'])
			));
			if ($pageid)
			{
				exit ($this->json->encode(array(
					'state'=>true,
					'data'=>array(
						'pageid'=>$pageid,
						'name'=>$name,
						'url'=>$url
					)
				)));
			}
			else
			{
				exit('{"state":false,"error":"拷贝失败"}');
			}
		}
		else
		{
			$page['name'] = $page['name'].'-拷贝';
			$page['file'] = pathinfo($page['file'], PATHINFO_FILENAME).'-copy';
			$this->view->assign($page);
			$this->view->display('setPage');
		}
	}

    /**
     * 删除页面
     *
     * @aca 删除页面
     */
	function delPage()
	{
		$pageid = $_REQUEST['pageid'];
		$pageid = array_unique(array_map('intval', array_filter(explode(',', $pageid))));
		if ($pageid)
		{
			$pages = $this->specialPage->select('pageid IN ('.implode_ids($pageid).')', '`file`, `pageid`, `data`');
			foreach ($pages as $p)
			{
                if ($this->specialPage->delete($p['pageid']))
                {
                    // 删除页面
                    $this->psn->rm($p['file']);

                    // 删除资源文件
                    $data = decodeData($p['data']);
                    if (is_array($data['head']['resource']))
                    {
                        foreach ($data['head']['resource'] as $resource)
                        {
                            if ($pos = $this->psn->parse($resource))
                            {
                                @unlink($pos['path']);
                            }
                        }
                    }
                    unset($data);
                }
			}
		}
		exit('{"state":true}');
	}

    /**
     * 添加资源文件
     *
     * @aca 添加资源文件
     */
	function addRes()
	{
		if (! ($pageid = intval($_REQUEST['pageid'])))
		{
			exit ('{"state":false,"error":"未知上传"}');
		}
		if (! ($page = $this->specialPage->get($pageid, 'file')))
		{
			exit ('{"state":false, "error":"不存在此页面，可能已经被删除"}');
		}
		$base = ( preg_match('|[\\/]$|', $page['file'])
					? $page['file']
					: dirname($page['file']) ) . '/resource';
		if (!($pos = $this->psn->parse($base)))
		{
			exit ('{"state":false,"error":"未知上传未知"}');
		}
		$base = $pos['psn'];
		$basepath = $pos['path'];
		$baseurl = $pos['url'];
		
		import('attachment.upload');
		$maxfilesize = intval(ini_get('upload_max_filesize')) * 1024 * 1024;
		$name = 'Filedata';
		$fileExt = '*';
		$upload = new upload($basepath, $fileExt, $maxfilesize);
		if (! $upload->execute($name, true))
		{
			exit ($this->json->encode(array(
				'state'=>false,
				'error'=>$upload->error()
			)));
		}
		$data = array();
		foreach ($upload->get_files() as $f)
		{
			$filename = pathinfo($f['alias'], PATHINFO_FILENAME);
			$ext = $f['fileext'];
			$file = "$filename.$ext";
			$filepos = "$basepath/$file";
			$i = 0;
			while (file_exists($filepos))
			{
				$file = $filename.'('.(++$i).').'.$ext;
				$filepos = "$basepath/$file";
			}
			$oldpos = $f['filepath'].$f['filename'];
			if (rename($oldpos, $filepos))
			{
				$psn = $base .'/'. $file;
				$url = $baseurl .'/'. $file;
			}
			else
			{
				$filepos = $oldpos;
				$psn = $base .'/'. $f['filename'];
				$url = $baseurl .'/'. $f['filename'];
			}
			$mtime = date('Y/m/d,H:i:s', filemtime($filepos));
			if (preg_match('/^(js|css|txt|html|htm|xml)|(png|jpeg|jpg)$/', $ext, $m))
			{
				$editor = $m[1] == $ext ? 'code' : 'image';
			}
			else
			{
				$eidtor = false;
			}
			
			$data[] = array(
				'psn' => $psn,
				'url' => $url,
				'ext' => $ext,
				'size' => size_format($f['filesize']),
				'updated' => $mtime,
				'editor' => $editor
			);
		}
		exit ($this->json->encode(array(
			'state'=>true,
			'data'=>$data
		)));
	}

    /**
     * 编辑资源文件
     *
     * @aca 编辑资源文件
     */
	function editRes()
	{
		$psn = $_REQUEST['psn'];
		if (! ($pageid = intval($_REQUEST['pageid'])))
		{
			$this->_errorOut('无效编辑位置');
		}
		if (! ($page = $this->specialPage->get($pageid, 'file')))
		{
			exit ('{"state":false, "error":"不存在此页面，可能已经被删除"}');
		}
		if ($psn == 'reserved.js' || $psn == 'reserved.css')
		{
			$psn = (preg_match('|[\\/]$|', $page['file'])
						? $page['file']
						: dirname($page['file'])
					) . "/resource/page-$pageid-$psn";
		}
		if ($this->is_post())
		{
			if ($url = $this->psn->write($psn, $_POST['contents']))
			{
				exit ($this->json->encode(array(
					'state'=>true,
					'url'=>$url
				)));
			}
			else
			{
				exit ('{"state":false,"error":"写入失败"}');
			}
		}
		else
		{
			$contents = $this->psn->read($psn);
			if ($contents === false)
			{
				$contents = '';
			}
			$this->view->assign('contents', $contents);
			$this->view->display('editCode');
		}
	}

    /**
     * 保存方案中的资源文件
     *
     * @param $resources 要保存的资源文件
     * @param $dir 保存到的方案目录
     * @return array 返回数组形式的文件名
     */
    protected function _saveRes($resources, $dir)
    {
        $result = array();
        foreach ($resources as $resource)
        {
            $ext = strtolower(fileext($resource));
            if ($ext != 'js' && $ext != 'css')
            {
                continue;
            }
            if (! ($pos = $this->psn->parse($resource)))
            {
                continue;
            }
            $file = preg_replace('/^page-[\d]+-/', '', basename($pos['path']));
            folder::create($dir.'/resource/');
            @copy($pos['path'], $dir.'/resource/'.$file);
            $result[] = $file;
        }
        return $result;
    }

    /**
     * 使用方案中的资源文件
     *
     * @param $scheme 方案名称
     * @param $resources 资源文件
     * @param $pos 专题页面的发布点
     * @param $pageid 专题页面 ID
     * @return array 数组形式的资源文件的发布点
     */
    protected function _useRes($scheme, $resources, $pos, $pageid)
    {
        $result = array();
        foreach ($resources as $resource)
        {
            $ext = strtolower(fileext($resource));
            if ($ext != 'js' && $ext != 'css')
            {
                continue;
            }
            $source = SPEC_PATH.'scheme/'.$scheme.'/resource/'.$resource;
            if (! is_file($source))
            {
                continue;
            }
            $path = dirname($pos['path']).'/resource/';
            $file = 'page-'.$pageid.'-'.$resource;
            folder::create($path);
            @copy($source, $path.$file);
            $base = substr($pos['psn'], 0, strrpos($pos['psn'], '/'));
            $result[] = $base.'/resource/'.$file;
        }
        return $result;
    }

    /**
     * 风格
     *
     * @aca 风格
     */
	function css()
	{
		if ($_POST['theme'])
		{
			$this->themeDef = array();
			$this->themeUsed = array();
			$used = $this->_theme($_POST['theme']);
		}
		elseif ($_POST['used'])
		{
			$used = explode(',', $_POST['used']);
		}
		else
		{
			exit;
		}
		exit($this->json->encode(array(
			'url'=>$this->_usedUrl($used)
		)));
	}

    /**
     * 上传模板
     *
     * @aca 上传模板
     */
	function addTemplate()
	{
		if (! ($contentid = intval($_REQUEST['contentid'])))
		{
			$this->_errorOut('缺少参数:contentid');
		}
		if (!($special = $this->special->get($contentid, 'path')))
		{
			$this->_errorOut('不存在该专题');
		}
		$ERROR = array(
		   0=>'找不到上传的文件',
	       1=>'上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值',
           2=>'上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值',
           3=>'文件只有部分被上传',
           4=>'没有文件被上传',
           6=>'找不到临时文件夹',
           7=>'文件写入失败',
           8=>'模板无效，zip文件读取错误',
           9=>'模板无效，缺少index.html文件',
           10=>'模板无效，缺少head结束标记，请编辑后上传',
           11=>'模板无效，缺少body结束标记，请编辑后上传'
        );
        if (empty($_FILES['Filedata']))
        {
            exit('{"state":false, "error":"'.$ERROR[0].'"}');
        }
        
        $file = $_FILES['Filedata'];
        if ($file['error'])
        {
            exit('{"state":false, "error":"'.$ERROR[$file['error']].'"}');
        }
        if (!is_uploaded_file($file['tmp_name']))
        {
            exit('{"state":false, "error":"'.$ERROR[0].'"}');
        }
        $filename = pathinfo($file['name'], PATHINFO_FILENAME);
        $zip = new ZipArchive();
		$zip->open($file['tmp_name']);
		$destination = dirname($file['tmp_name']).'/'.date('YmdHis');
		if (! $zip->extractTo($destination))
		{
			exit('{"state":false, "error":"'.$ERROR[8].'"}');
		}
		
		$tplfile = $destination.'/index.html';
		if (! is_file($tplfile))
		{
        	exit('{"state":false, "error":"'.$ERROR[9].'"}');
		}
		
		$tplstr = file_get_contents($tplfile);
        
		if (false === stripos($tplstr, '</head>'))
		{
			exit('{"state":false, "error":"'.$ERROR[10].'"}');
		}
		if (false === stripos($tplstr, '</body>'))
		{
			exit('{"state":false, "error":"'.$ERROR[11].'"}');
		}
		$tplstr = str_ireplace('</head>', '{$head}</head>', $tplstr);
		$tplstr = str_ireplace('<body', '<body {$bodyattr}', $tplstr);
		$thumb = '';
		if (is_file($destination.'/thumb.gif'))
		{
			$thumb = 'thumb.gif';
		}
		elseif (is_file($destination.'/thumb.jpg'))
		{
			$thumb = 'thumb.jpg';
		}
		elseif (is_file($destination.'/thumb.png'))
		{
			$thumb = 'thumb.png';
		}
		$notes = $destination.'/notes.xml';
		write_file($notes,
			array(
				'<?xml version="1.0" encoding="UTF-8"?>',
				'<root>',
					'<name>'.$filename.'</name>',
					'<thumb>'.$thumb.'</thumb>',
				'</root>'
			)
		);
		$pubpos = $this->psn->parse($special['path'].'/resource/templates');
		$tplpath = $pubpos['path'].'/';
		$name = trim(preg_replace('/[^\w\-]/', '', $filename));
		if (! $name)
		{
			$name = date('Ymd');
		}
		$_name = $name;
		$tpldes = $tplpath.$name;
		$i = 0;
		while (file_exists($tpldes))
		{
			$name = $_name.'-'.(++$i);
			$tpldes = $tplpath.$name;
		}
		
		$tplurl = $pubpos['url'].'/'.$name;
		
		// 替换 相对url->绝对url
		$tplstr = preg_replace(
			'/((?:(?:href|src)\s*=|url\s*\()\s*["\']?)([^>\)"\']+)(["\' >\)])/Uise',
			'stripslashes("\1").$this->_absUrl(stripslashes("\2"), $tplurl).stripslashes("\3")',
			$tplstr
		);
		
		write_file($tplfile, $tplstr);
		
		if (folder::copy($destination, $tpldes))
		{
			exit($this->json->encode(array(
				'state'=>true,
				'data'=>array(
					'thumb'=>$thumb ? ($tplurl.'/'.$thumb) : '',
					'entry'=>$pubpos['psn'].'/'.$name,
					'name'=>$filename
				)
			)));
		}
		else
		{
			exit('{"state":false,"error":"安装模板失败"}');
		}
	}

    /**
     * 删除模板
     *
     * @aca 删除模板
     */
	function delTemplate()
	{
		$tpl = $_REQUEST['template'];
		if ($ret = $this->psn->parse($tpl))
		{
			$tpl = $ret['path'];
		}
		else
		{
			if ($tpl == 'default') {
				$this->_errorOut('默认模板不能删除');
			}
			$tpl = SPEC_PATH.'templates/'.$tpl;
		}
		if (!folder::delete($tpl))
		{
			$error = folder::errormsgs();
			$this->_errorOut(array_pop($error));
		}
		exit('{"state":true}');
	}

    /**
     * 编辑模板
     *
     * @aca 编辑模板
     */
	function editTemplate()
	{
		$ret = $this->_tplCanEdit();
		if (!$ret['state'])
		{
			return $this->_errorOut($ret['error']);
		}
		$tpl = $ret['file'];
		if ($this->is_post())
		{
			if (write_file($tpl, $_POST['contents']))
			{
				exit ($this->json->encode(array(
					'state'=>true
				)));
			}
			else
			{
				exit ('{"state":false, "error":"写入失败"}');
			}
		}
		else
		{
			$contents = file_get_contents($tpl);
			if ($contents === false)
			{
				$contents = '';
			}
			$this->view->assign('contents', $contents);
			$this->view->display('editCode');
		}
	}

    /**
     * 模板可编辑
     *
     * @aca 模板可编辑
     */
	function tplCanEdit()
	{
		$ret = $this->_tplCanEdit();
		unset($ret['file']);
		exit ($this->json->encode($ret));
	}
	
	protected function _tplCanEdit()
	{
		$pageid = intval($_REQUEST['pageid']);
		if (!($page = $this->specialPage->get($pageid, 'template')))
		{
			return array('state'=>false, 'error'=>'不存在此页面，可能已经被删除');
		}
		$tpl = $page['template'];
		$name = $tpl;
		if (empty($tpl) || $tpl == 'default')
		{
			return array('state'=>false, 'error'=>'默认模板不能编辑');
		}
		if ($ret = $this->psn->parse($tpl))
		{
			$tpl = $ret['path'].'/index.html';
		}
		else
		{
			$tpl = SPEC_PATH.'templates/'.$tpl.'/index.html';
		}
		if (is_file($tpl))
		{
			return array(
				'state'=>true,
				'file'=>$tpl,
				'name'=>$name
			);
		}
		else
		{
			return array(
				'state'=>false,
				'error'=>"模板{$name}丢失"
			);
		}
	}

    /**
     * 获取模版
     *
     * @aca 获取模版
     */
	function getTemplate()
	{
		$engine = $_GET['engine'];
		$template = '';
		if ( ($widgetid = intval($_GET['widgetid']))
			&& ($widget = $this->widget->get($widgetid)) )
		{
			$engine = $widget['engine'];
			$data = decodeData($widget['data']);
			$template = $data['template'];
		}
		if (trim($template) == '')
		{
			if (!$engine) exit;
			$template = file_get_contents(app_dir('special').'view/widgets/'.$engine.'/template.html');
		}
		exit ($template);
	}

    /**
     * 获取风格中所有元素样式
     *
     * @aca 获取风格中所有元素样式
     */
	function getTheme()
	{
		if (empty($_GET['cat']))
		{
			exit('{}');
		}
		$p = 'ui/'.$_GET['cat'];
		$themePath = SPEC_PATH.$p;
		$themeUrl  = SPEC_URL.$p;
		$data = array();
		if ($h = opendir($themePath))
		{
			while (false !== ($entry = readdir($h)))
			{
				if ($entry{0} == '.') continue;
				$path = $themePath .'/'.$entry;
				if (is_dir($path))
				{
					$thumb = $themeUrl .'/'. $entry .'/thumb.jpg';
					$data[$entry] = array(
						'name' => $entry,
						'thumb' => $thumb
					);
				}
			}
			closedir($h);
		}
		exit ($data ? $this->json->encode($data) : '{}');
	}

    /**
     * 设置风格
     *
     * @aca 设置风格
     */
	function setUI()
	{
		$theme = $_GET['theme'];
		$flag = $_REQUEST['flag'];
		$def = array(
			'define'=>$this->json->decode($_POST['data'])
		);
		$pageid = intval($_REQUEST['pageid']);
		$page = $this->specialPage->get($pageid, 'file');
		$pos = $this->psn->parse(str_replace(
			'\\', '/',
			preg_match('|[\\/]$|', $page['file'])
				? $page['file']
				: dirname($page['file'])
			).'/resource');
		if (!file_exists($pos['path']))
		{
			folder::create($pos['path']);
		}
		if ($flag == 'preview')
		{
			$name = 'preview';
			$target = $pos['path'].'/theme-preview-'.$pageid.'.xml';
			file_put_xmlarray($target, array(
				'theme'=>$def
			));
		}
		elseif ($flag == 'saveas' || (!$theme && $flag == 'ok'))
		{
			$def['name'] = $_POST['name'];
			$name = date('Ymd').rand(10, 99);
			if (empty($def['name']))
			{
				$def['name'] = $name;
			}
			$target = SPEC_PATH.'ui/themes/'.$name.'.xml';
			$thumbTarget = SPEC_PATH.'ui/themes/'.$name.'.jpg';
			$thumb = $_POST['thumb'];
			if ($thumb)
			{
				@copy(preg_match('|^http[s]?\://|i', $thumb)
						? $thumb
						: (UPLOAD_URL.$thumb),
					$thumbTarget
				);
			}
			elseif ($theme != 'custom')
			{
				@copy(SPEC_PATH.'ui/themes/'.$theme.'.jpg', $thumbTarget);
			}
			file_put_xmlarray($target, array(
				'theme'=>$def
			));
			
			$def['thumb'] = SPEC_URL.'ui/themes/'.$name.'.jpg';
		}
		elseif ($flag == 'ok')
		{
			$name = $theme;
			if ($theme == 'custom')
			{
				$def['name'] = '自定义';
				$target = $pos['path'].'/theme-custom-'.$pageid.'.xml';
			}
			else
			{
				$target = SPEC_PATH.'ui/themes/'.$theme.'.xml';
				$odef = file_get_xmlarray($target, 'theme');
				$def['name'] = $odef['name'];
			}
			file_put_xmlarray($target, array(
				'theme'=>$def
			));
			if ($theme == 'custom')
			{
				$def['thumb'] = 'apps/special/images/custom-theme.jpg';
			}
			else
			{
				$def['thumb'] = SPEC_URL.'ui/themes/'.$theme.'.jpg';
			}
		}
		else
		{
			exit ('{"state":false}');
		}
		if (empty($def['define']['content']))
		{
			$def['define']['content'] = array(''=>null);
		}
		exit ($this->json->encode(array(
			'state'=>true,
			'name'=>$name,
			'theme'=>$def
		)));
	}

    /**
     * 删除风格
     *
     * @aca 删除风格
     */
	function delTheme()
	{
		$theme = $_GET['theme'];
		if ($theme != 'custom')
		{
			@unlink(SPEC_PATH.'ui/themes/'.$theme.'.xml');
			@unlink(SPEC_PATH.'ui/themes/'.$theme.'.jpg');
			exit ('{"state":true}');
		}
	}

    /**
     * 获取模块
     *
     * @aca 获取模块
     */
	function getWidget()
	{
		$where = array("TRIM(name)<>'' AND status>0");
		$keyword = $_REQUEST['keyword'];
		if (trim($keyword) != '')
    	{
    		$keyword = str_replace('_', '\_', addcslashes($keyword, '%_'));
    		$where[] = "name LIKE '%$keyword%'";
    	}
		if ($_REQUEST['engine'])
		{
			$engine = addslashes($_REQUEST['engine']);
			$where[] = "engine='$engine'";
		}
		$where = implode(' AND ', $where);
		$fields = 'widgetid, name as text, engine, description';
		$order = 'created desc';
		$page = intval($_REQUEST['page']);
		if ($page < 1)
		{
			$page = 1;
		}
		$size = 10;
		$total = $this->widget->count($where);
		$data = $total
			? $this->widget->page($where, $fields, $order, $page)
			: array();
		$widgetUrl = SPEC_URL.'widget';
		foreach ($data as &$d)
		{
			$d['icon'] = $widgetUrl.'/'.$d['engine'].'/icon.gif';
		}
		exit ($this->json->encode(array(
			'total'=>$total,
			'data'=>$data
		)));
	}

    /**
     * 获取单个模块
     *
     * @aca 获取模块
     */
    function getOneWidget()
    {
        if (! ($widgetid = intval($_REQUEST['widgetid']))
			|| ! ($widget = $this->widget->get($widgetid)))
		{
			exit ('{"state":false,"error":"不存在此模块"}');
		}

        $widget['data'] = decodeData($widget['data']);
        $widget['setting'] = decodeData($widget['setting']);

		exit($this->json->encode(array('state' => true, 'widget' => $widget)));
    }

    /**
     * 编辑模块
     *
     * @aca 编辑模块
     */
	function editWidget()
	{
		if (!($widgetid = intval($_REQUEST['widgetid']))
			|| !($widget = $this->widget->get($widgetid)))
		{
			exit ('{"state":false,"error":"不存在此模块"}');
		}
		$engine = $_REQUEST['engine'];
		if (! loader::model('admin/widgetEngine')->get(array('name'=>$engine)))
		{
			$this->_errorOut("[Error: Widget engine \"$engine\" not exists.]");
		}
		if ($this->is_post())
		{
			if (!empty($_POST['template'])) {
				if (false !== ($error = $this->_testTemplate($_POST['template']))) {
					exit ($this->json->encode(array(
						'state'=>false,
						'error'=>$error
					)));
				}
			}
			if ($_REQUEST['preview'])
			{
				$widget = array(
					'widgetid'=>$widgetid,
					'engine'=>$engine,
					'data'=>widgetEngine::genData($engine, $_POST, $widget)
				);
				try {
					$html = widgetEngine::render($engine, $widget);
				} catch (Exception $e) {
					$html = '[Render error: '.$e->getMessage().']';
				}
				exit ($this->json->encode(array(
					'state'=>true,
					'html'=>$html
				)));
			}
			
			$json = null;
			if (widgetEngine::editPost($engine, $widget, $_POST))
			{
				try {
					$html = widgetEngine::render($engine, $this->widget->get($widgetid));
				} catch (Exception $e) {
					$html = '[Render error: '.$e->getMessage().']';
				}
				$json = array(
					'state'=>true,
					'html'=>$html
				);
			}
			else
			{
				$json = array(
					'state'=>false,
					'error'=>'编辑失败'
				);
			}
			exit ($this->json->encode($json));
		}
		else
		{
			widgetEngine::editView($engine, $widget);
		}
	}

    /**
     * 编辑模块设置
     *
     * @aca 编辑模块设置
     */
    function editWidgetSetting()
    {
        if (! ($widgetid = intval($_REQUEST['widgetid']))
			|| ! ($widget = $this->widget->get($widgetid)))
		{
			exit ('{"state":false,"error":"不存在此模块"}');
		}

        $originSetting = decodeData($widget['setting']);
        $newSetting = array_merge($originSetting ? $originSetting : array(), (array) value($_REQUEST, 'setting'));

        if ($this->widget->update(array(
            'setting' => encodeData($newSetting)
        ), $widgetid))
        {
            $rs = array('state' => true, 'setting' => $newSetting);
        }
        else
        {
            $rs = array('state' => false, 'error' => $this->widget->error());
        }

		exit($this->json->encode($rs));
    }

    /**
     * 发布模块
     *
     * @aca 发布模块
     */
	function pubWidget()
	{
		$widgetid = $_REQUEST['widgetid'];
		$widgetid = array_unique(array_map('intval', array_filter(explode(',', $widgetid))));
		foreach ($widgetid as $id)
		{
			if ($widget = $this->widget->get($id))
			{
				$this->_publishWidget($widget);
			}
		}
		exit ('{"state":true, "info":"已发布"}');
	}

    /**
     * 添加模块
     *
     * @aca 添加模块
     */
	function addWidget()
	{
		$engine = $_REQUEST['engine'];
		if (! loader::model('admin/widgetEngine')->get(array('name'=>$engine)))
		{
			$this->_errorOut("[Error: Widget engine \"$engine\" not exists.]");
		}
		if ($this->is_post())
		{
			if (!empty($_POST['template'])) {
				if (false !== ($error = $this->_testTemplate($_POST['template']))) {
					exit ($this->json->encode(array(
						'state'=>false,
						'error'=>$error
					)));
				}
			}
			// preview
			if ($_REQUEST['preview'])
			{
				$html = '';
				try {
					$html = widgetEngine::render($engine, array(
						'widgetid'=>'preview',
						'engine'=>$engine,
						'data'=>widgetEngine::genData($engine, $_POST)
					));
				} catch (Exception $e) {
					$html = '[Render error: '.$e->getMessage().']';
				}
				exit ($this->json->encode(array(
					'state'=>true,
					'html'=>$html
				)));
			}
			
			$widgetid = widgetEngine::addPost($engine, $_POST);
			if ($widgetid)
			{
				try {
					$html = widgetEngine::render($engine, $this->widget->get($widgetid));
				} catch (Exception $e) {
					$html = '[Render error: '.$e->getMessage().']';
				}
				exit ($this->json->encode(array(
					'state'=>true,
					'widgetid'=>$widgetid,
					'html'=>$html
				)));
			}
			else
			{
				exit ('{"state":false, "error":"添加失败"}');
			}
		}
		else
		{
			widgetEngine::addView($engine);
		}
	}

    /**
     * 使用模块
     *
     * @aca 使用模块
     */
	function useWidget()
	{
		if (!($widgetid = intval($_REQUEST['widgetid']))
		|| !($widget = $this->widget->get($widgetid)))
		{
			$this->_errorOut('不存在此模块');
		}
		if ($this->is_post())
		{
			$modified = false;
			$skin = $widget['skin'];
			if (empty($_REQUEST['preview']) && $_POST['method'] == 'copy')
			{
				$widgetid = widgetEngine::copy($widget);
				if (! $widgetid)
				{
					exit ('{"state":false, "error":"添加失败"}');
				}
				$widget = $this->widget->get($widgetid);
			}
			else
			{
				$modified = $this->_isModified($widget);
			}
			
			exit ($this->json->encode(array(
				'state'=>true,
				'widgetid'=>$widgetid,
				'modified'=>$modified,
				'html'=>widgetEngine::render($widget['engine'], $widget),
				'skin'=>$skin
			)));
		}
		else
		{
			$this->view->display('useWidget');
		}
	}

    /**
     * 共享模块
     *
     * @aca 共享模块
     */
	function shareWidget()
	{
		if (!($widgetid = intval($_REQUEST['widgetid']))
		|| !($widget = $this->widget->get($widgetid)))
		{
			$this->_errorOut('不存在此模块');
		}
		if ($this->is_post())
		{
			if (false !== $this->widget->shared($widgetid, $_POST))
			{
				exit ('{"state":true, "info":"已共享"}');
			}
			else
			{
				exit ('{"state":false, "error":"共享失败"}');
			}
		}
		else
		{
			$this->view->assign($widget);
			$this->view->display('shareWidget');
		}
	}

    /**
     * 取消共享模块
     *
     * @aca 取消共享模块
     */
	public function unshareWidget()
	{
		if (false !== $this->widget->unshare(intval($_REQUEST['widgetid'])))
		{
			exit ('{"state":true}');
		}
		else
		{
			exit ('{"state":false, "error":"取消共享失败"}');
		}
	}

    /**
     * 设置框架
     *
     * @aca 设置框架
     */
	function setFrame()
	{
		if (! ($pageid = intval($_REQUEST['pageid'])))
		{
			$this->_errorOut("缺少参数");
		}
		$indexer = $this->_readCachedIndex($pageid);
		$id = $_REQUEST['id'];
		if (! is_array($indexer[$id]))
		{
			$indexer[$id] = array(
				'id'=>$id
			);
		}
		$item = & $indexer[$id];
		if ($this->is_post())
		{
			$item['class'] = $_POST['class'];
			
			$item['style'] = array();
			$style = & $item['style'];
			foreach ((array) $_POST['style'] as $key=>$val)
			{
				switch ($key)
				{
				case 'font-size': 
					$style[$key] = $val === '' ? '' : intval($val);
					break;
				case 'color':case 'border-all':case 'margin-all':
				case 'background-color':case 'background-image':
					$style[$key] = $val;
					break;
				case 'border-width':case 'border-style':
				case 'border-color':case 'margin':
					$style[$key] = $val ? ((array) $val) : array();
					break;
				}
			}
			if (empty($_REQUEST['preview']))
			{
				$this->_cacheIndex($pageid, $indexer);
			}
			exit ($this->json->encode(array(
				'state'=>true,
				'class'=>$item['class'],
				'cssText'=>$this->_frameCSS($item['style'])
			)));
		}
		else
		{
			$this->view->assign('style', (array) $item['style']);
			$this->view->assign('class', $item['class']);
			$this->view->display('setFrame');
		}
	}

    /**
     * 保存
     *
     * @aca 保存
     */
	function save()
	{
		if (! ($pageid = intval($_REQUEST['pageid'])))
		{
			exit ('{"state":false, "error":"缺少参数"}');
		}
		if ($error = $this->_save($pageid))
		{
			exit ($this->json->encode($error));
		}
		else
		{
			exit ('{"state":true, "info":"保存成功"}');
		}
	}

    /**
     * 发布
     *
     * @aca 发布
     */
	function publish()
	{
		if (! ($pageid = intval($_REQUEST['pageid'])))
		{
			exit ('{"state":false, "error":"缺少参数"}');
		}
		if (isset($_POST['jsondata']) && ($error = $this->_save($pageid)))
		{
			exit ($this->json->encode($error));
		}
		if (! ($page = $this->specialPage->get($pageid)))
		{
			exit ('{"state":false, "error":"不存在此页面，可能已经被删除"}');
		}
		exit ($this->json->encode($this->_publish($page)));
	}

    /**
     * 下线
     *
     * @aca 下线
     */
	function offline()
	{
		$pageid = $_REQUEST['pageid'];
		$pageid = array_unique(array_map('intval', array_filter(explode(',', $pageid))));
		if ($pageid)
		{
			$pages = $this->specialPage->select('pageid IN ('.implode_ids($pageid).')', 'file, pageid');
			foreach ($pages as $p)
			{
				$this->psn->rm($p['file']);
				$this->specialPage->unpublish($p['pageid']);
			}
		}
		exit ('{"state":true}');
	}

    /**
     * 设计
     *
     * @aca 设计
     * @return mixed
     */
	function design()
	{
		if (!($contentid = intval($_REQUEST['contentid'])))
		{
			$this->showmessage('缺少参数:contentid，无法访问');
		}
		$pageid = intval($_REQUEST['pageid']);
		if (!$pageid)
		{
			$page = $this->specialPage->get("contentid=$contentid");
			if (! $page)
			{
				$url = '?app=special&controller=online&action=addPage&contentid='.$contentid;
			}
			else
			{
				$url = '?app=special&controller=online&action=design&contentid='.$contentid.'&pageid='.$page['pageid'];
			}
			header("Location:$url");
			exit;
		}
		elseif (!($page = $this->specialPage->get($pageid)))
		{
			$this->showmessage(
				'不存在此页面，可能已经被删除',
				'?app=special&controller=online&action=design&contentid='.$contentid
			);
			return;
		}
		if ($page['contentid'] != $contentid)
		{
			$url = '?app=special&controller=online&action=design&contentid='.$page['contentid'].'&pageid='.$pageid;
			header("Location:$url");
			exit;
		}
		
		$tpl = (empty($page['template']) ? 'default' : $page['template']) .'/index.html';
		if ($ret = $this->psn->parse($tpl))
		{
			$tpl = $ret['path'];
			$tplfile = $tpl;
		}
		else
		{
			$this->template->set_dir(SPEC_PATH.'templates/');
			$tplfile = SPEC_PATH.'templates/'.$tpl;
		}
		if (!is_file($tplfile))
		{
			$this->showmessage("模板{$tpl}丢失");
			return;
		}
		$this->_render($page, true);
		$this->specialPage->lock($pageid);
		$this->template->set_rule('|\{diyarea +(\w+?)\}|i', '<?php echo isset(\$\1) ? \$\1 : \'<div class="'.self::DIY_CLASS_AREA.' '.self::DIY_CLASS_ROOT.'" id="\1"></div>\';?>');
		$this->template->display($tpl);
	}

    /**
     * 查看和编辑方案
     *
     * @aca 查看和编辑方案
     */
	function scheme()
	{
		if (! ($pageid = intval($_REQUEST['pageid'])))
		{
			$this->_errorOut('缺少参数');
		}
		if (! ($page = $this->specialPage->get($pageid)))
		{
			exit ('{"state":false, "error":"不存在此页面，可能已经被删除"}');
		}
		if ($this->is_post())
		{
			if (! ($data = $this->json->decode($_POST['jsondata'])) || !is_array($data))
			{
				exit ('{"state":false, "error":"无效提交数据，客户端脚本错误"}');
			}
			$head = $data['head'];
            $resources = $head['resource'];
			unset($data['head']);
			$this->_extractData($data);
			unset($head['resource'], $head['title']);
			$data['head'] = $head;
			$date = date('ymd');
			$entry = $date.rand(1000, 9999);
			$base = SPEC_PATH.'scheme';
			$dir = $base.'/'.$entry;
			while (file_exists($dir))
			{
				$entry = $date.rand(1000, 9999);
				$dir = $base.'/'.$entry;
			}
			folder::create($dir);
			$thumb = $_POST['thumb'];
			$thumbTarget = null;
			if ($thumb)
			{
				$ext = pathinfo($thumb, PATHINFO_EXTENSION);
				if (!$ext)
				{
					$ext = 'jpg';
				}
				$thumbTarget = 'thumb.'.$ext;
				
				@copy(preg_match('|^http[s]?\://|i', $thumb)
						? $thumb
						: (UPLOAD_URL.$thumb),
					$dir.'/'.$thumbTarget
				);
			}
            if (is_array($resources))
            {
                $data['head']['resource'] = $this->_saveRes($resources, $dir);
            }
			$scheme = array(
				'name'=>$_POST['name'],
				'data'=>encodeData($data),
				'template'=>$page['template'],
				'thumb'=>$thumbTarget
			);
			if (file_put_xmlarray($dir.'/notes.xml', array(
				'root'=>$scheme
			)))
			{
				exit ('{"state":true, "info":"保存方案成功"}');
			}
			else
			{
				folder::delete($dir);
				exit ('{"state":false, "error":"保存方案失败"}');
			}
		}
		else
		{
			$this->view->assign($this->specialPage->get($pageid, 'name'));
			$this->view->display('scheme');
		}
	}

    /**
     * 删除方案
     *
     * @aca 删除方案
     */
    function delScheme()
    {
        $scheme = isset($_REQUEST['scheme']) ? trim($_REQUEST['scheme']) : NULL;
        if (! $scheme || ! ctype_alnum($scheme))
		{
			$this->_errorOut('缺少方案名称');
		}
        $dir = SPEC_PATH.'scheme'.DS.$scheme.DS;
        if (! is_dir($dir))
        {
            $this->_errorOut('要删除的方案 '.$scheme.' 不存在');
        }
        if (! folder::delete($dir))
		{
			$error = folder::errormsgs();
			$this->_errorOut(array_pop($error));
		}
		exit('{"state":true}');
    }
	
	function __call($action, $args)
	{
		if (preg_match('/(\w+)_(\w+)/', $action, $m))
		{
			return widgetEngine::dispath($m[1], $m[2]);
		}
		$this->_action_not_defined($action);
	}

    /**
     * 定时更新
     *
     * @aca cron 定时更新
     */
	function cron()
	{
		@set_time_limit(600);
		$msg = array();
		foreach ($this->specialPage->select('frequency>0 AND frequency+published<='.TIME) as $page)
		{
			$rs = $this->_publish($page);
			$msg[] = $page['name'].($rs['state'] ? '已更新' : '未更新');

            // 创建或更新时间超过 3 个月的页面停止自动更新
            if ((max($page['created'], $page['updated']) + 90 * 24 * 3600) <= TIME)
            {
                $this->specialPage->update(array('frequency' => 0), intval($page['pageid']), 1);
            }
		}
		$json = array(
			'state'=>true,
			'info'=>implode('<br />', $msg)
		);
		exit ($this->json->encode($json));
	}
	
	protected function _parseData(&$data)
	{
		foreach ($data as $key=>&$item)
		{
			if (isset($item['widget']))
			{
				try {
					$item['widgetid'] = widgetEngine::copy($item['widget']);
				} catch (Exception $e){}
				unset($item['widget']);
			}
			elseif (is_array($item['items']))
			{
				$this->_parseData($item['items']);
			}
		}
	}
	protected function _extractData(&$data)
	{
		foreach ($data as &$item) {
			if (isset($item['widgetid']))
			{
				$v = intval($item['widgetid']);
				unset($item['widgetid']);
				$item['widget'] = $v
					? $this->widget->get($v, 'engine,data,description')
					: array();
			}
			elseif (is_array($item['items']))
			{
				$this->_extractData($item['items']);
			}
		}
	}
	protected function _save($pageid)
	{
		if (! ($data = $this->json->decode($_POST['jsondata'])) || !is_array($data))
		{
			return array(
				'state'=>false,
				'error'=>'无效提交数据，客户端脚本错误'
			);
		}

		if (! $this->specialPage->saveData($pageid, $data))
		{
			return array(
				'state'=>false,
				'error'=>'保存到数据库失败'
			);
		}
		return 0;
	}
	protected function _publish($page)
	{
		$tpl = (empty($page['template']) ? 'default' : $page['template']).'/index.html';
		if ($ret = $this->psn->parse($tpl))
		{
			$tpl = $ret['path'];
			$tplfile = $tpl;
		}
		else
		{
			$this->template->set_dir(SPEC_PATH.'templates/');
			$tplfile = SPEC_PATH.'templates/'.$tpl;
		}
		if (!is_file($tplfile))
		{
			return array(
				'state'=>false,
				'error'=>"模板{$tpl}丢失"
			);
		}
		$this->_render($page);
		$this->template->set_rule('|\{diyarea +(.+?)\}|i', '<?php echo \$\1;?>');
		try {
			$contents = $this->template->fetch($tpl);
		} catch (Exception $e) {
			return array(
				'state'=>false,
				'error'=>"生成失败"
			);
		}
		
		if ($url = $this->psn->write($page['file'], $contents))
		{
			$this->specialPage->publish($page['pageid']);
			return array(
				'state'=>true,
				'url'=>$url
			);
		}
		else
		{
			return array(
				'state'=>false,
				'error'=>"写入文件失败"
			);
		}
	}
	protected function _publishWidget($widget)
	{
		$widgetid = $widget['widgetid'];
		// 渲染内容
		try {
			$contents = widgetEngine::render($widget['engine'], $widget);
		} catch (Exception $e) {
			$contents = '[Render error: '.$e->getMessage().']';
		}
		
		// 生成区块
		$f = $this->_widgetFile($widgetid);
		if (false === write_file($f, $contents))
		{
			return array(
				'state'=>false,
				'info'=>"写文件失败"
			);
		}
		$this->widget->published($widgetid);
		return array(
			'state'=>true,
			'info'=>"发布成功"
		);
	}
	
	protected function _render($page, $designMode = false)
	{
		$data = $this->_preRender($page, $designMode);
		$head = $data['head'];
		unset($data['head']);
		$this->_renderBody(array(
			'body'=>($head['body-attr'] ? $head['body-attr'] : $head['body-style']),
			'a'=>$head['a-style']
		), $designMode);
		foreach ($data as $key=>&$val)
		{
			$this->template->assign($key, $this->_renderArea($val, $designMode, true));
		}
		$this->template->assign('head', $this->_renderHead($head, $designMode));
	}
	protected function _renderBody($attr, $designMode)
	{
		$this->template->assign('bodyattr', $designMode ? ('body-style="'.$attr['body'].'" a-style="'.$attr['a'].'"') : '');
		$this->_cssRule('body', $attr['body']);
		$this->_cssRule('a', $attr['a']);
	}
	protected function _theme($theme)
	{
		if (empty($theme))
		{
			return null;
		}
		if ($theme == 'custom' || $theme == 'preview')
		{
			$pageid = intval($_REQUEST['pageid']);
			$page = $this->specialPage->get($pageid, 'file');
			$pos = $this->psn->parse(str_replace(
				'\\', '/',
				preg_match('|[\\/]$|', $page['file'])
					? $page['file']
					: dirname($page['file']
				)
			).'/resource');
			folder::create($pos['path']);
			$themefile = $pos['path'].'/theme-'.$theme.'-'.$pageid.'.xml';
		}
		else
		{
			$themefile = SPEC_PATH.'ui/themes/'.$theme.'.xml';
		}
		if (!is_file($themefile)
			|| !($def = file_get_xmlarray($themefile, 'theme'))
			|| !is_array($def['define']) )
		{
			return null;
		}
		$def = $def['define'];
		$tdef = & $this->themeDef;
		$tused = & $this->themeUsed;
		if ($def['page'])
		{
			$tused[] = 'page/'.$def['page'];
			$tdef['page'] = $def['page'];
		}
		if ($def['frame'])
		{
			$tused[] = 'frame/'.$def['frame'];
			$tdef['frame'] = $def['frame'];
		}
		if ($def['widget'])
		{
			$tused[] = 'widget/'.$def['widget'];
			$tdef['widget'] = $def['widget'];
		}
		if ($def['title'])
		{
			$tused[] = 'title/'.$def['title'];
			$tdef['title'] = $def['title'];
		}
		if (is_array($def['content']))
		{
			foreach($def['content'] as $k=>$v)
			{
				$tused[] = 'content/'.$k.'/'.$v;
				$tdef['content/'.$k] = $v;
			}
		}
		return $tused;
	}
	protected function _preRender($page, $designMode)
	{
		if (! ($data = decodeData($page['data'])))
		{
			$data = array(
				'head' => array (
					'title' => $page['name'],
					'meta' => array (
						'Content-Type' => 'text/html; charset=UTF-8',
					)
				)
			);
		}
		$this->themeDef = array();
		$this->themeUsed = array();
		$this->usedDir = array();
		
		$this->_theme($data['head']['theme']);
		$pageid = $page['pageid'];
		$base = str_replace(
			'\\', '/',
			preg_match('|[\\/]$|', $page['file'])
				? $page['file']
				: dirname($page['file']
			)
		).'/resource';
		if ($designMode)
		{
			$resources = (array) $data['head']['resource'];
			foreach (array(
					"$base/page-{$pageid}-reserved.js",
					"$base/page-{$pageid}-reserved.css"
			) as $k)
			{
				if (!in_array($k, $resources))
				{
					array_unshift($resources, $k);
				}
			}
			
			foreach ($resources as &$v)
			{
				$ext = strtolower(fileext($v));
				if (preg_match('/^(js|css|txt|html|htm|xml)|(png|jpeg|jpg)$/', $ext, $m))
				{
					$editor = $m[1] == $ext ? 'code' : 'image';
				}
				else
				{
					$eidtor = false;
				}
				if (! ($pos = $this->psn->parse($v)))
				{
					continue;
				}
				$path = $pos['path'];
				$url = $pos['url'];
				$exists = is_file($path);
				$size = size_format($exists ? filesize($path) : 0);
				$mtime = $exists ? date('Y/m/d,H:i:s', filemtime($path)) : '';
				$v = array(
					'psn' => $v,
					'url' => $url,
					'ext' => $ext,
					'size' => $size,
					'updated' => $mtime,
					'editor' => $editor
				);
			}
			$engines = loader::model('admin/widgetEngine')->select('disabled=0', 'engineid, name as engine, description as text');
			$widgetUrl = SPEC_URL.'widget';
			foreach ($engines as &$d)
			{
				$d['icon'] = $widgetUrl.'/'.$d['engine'].'/icon.gif';
			}
			$p = 'ui/themes';
			$themeUrl  = SPEC_URL.$p;
			$themes = array();
			$basepos = $this->psn->parse($base);
			$customxml = $basepos['path'].'/theme-custom-'.$pageid.'.xml';
			if (is_file($customxml))
			{
				$info = file_get_xmlarray($customxml, 'theme');
				if (empty($info['define']['content']))
				{
					$info['define']['content'] = array(''=>null);
				}
				$info['reserved'] = true;
				$info['thumb'] = 'apps/special/images/custom-theme.jpg';
				$themes['custom'] = $info;
			}
			else
			{
				$themes['custom'] = array(
					'name'=>'自定义',
					'reserved'=>true,
					'thumb'=>'apps/special/images/custom-theme.jpg',
					'define'=>array(
						'content'=>array(''=>null)
					)
				);
			}
			$origdir = getcwd();
			chdir(SPEC_PATH.$p);
			foreach(glob('*.xml') as $entry)
			{
				$info = file_get_xmlarray($entry, 'theme');
				$name = pathinfo($entry, PATHINFO_FILENAME);
				if (empty($info['define']['content']))
				{
					$info['define']['content'] = array(''=>null);
				}
				$info['thumb'] = $themeUrl .'/'.$name.'.jpg';
				$themes[$name] = $info;
			}
			chdir($origdir);
			$pages = $this->specialPage->select("contentid=$page[contentid]", 'pageid, name, url, locked, lockedby');
			foreach ($pages as &$p) {
				if ($this->specialPage->islock($p))
				{
					$p['locked'] = true;
					$p['lockedby'] = username($p['lockedby']);
				} else {
					$p['locked'] = false;
				}
			}
			$this->env = array(
				'pageid' => $pageid,
				'contentid' => $page['contentid'],
				'theme' => $data['head']['theme'],
				'metas' => (array) $data['head']['meta'],
				'resources' => (array) $resources,
				'engines' => $engines,
				'themes' => $themes,
				'pages' => $pages
			);
		}
		else
		{
			$this->env = array(
				'pageid'=>$page['pageid'],
				'contentid'=>$page['contentid'],
				'psn'=>$this->psn->parse($base)
			);
		}
		return $data;
	}
	protected function _renderMeta($meta)
	{
		$html = array();
		$meta = (array) $meta;
		if (empty($meta['Content-Type']))
		{
			$html[] = '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
		}
		else
		{
			$html[] = '<meta http-equiv="Content-Type" content="'.$meta['Content-Type'].'" />';
			unset($meta['Content-Type']);
		}
		$meta['Generator'] = 'CmsTop Special Engine v1.0';
        $html[] = '<meta http-equiv="X-Ua-Compatible" content="IE=EmulateIE7" />';
		foreach ($meta as $key=>$val)
		{
			switch ($key)
			{
			case 'Content-Language': case 'Content-Script-Type': case 'Charset':
			case 'Expires': case 'Refresh': case 'Pragma': case 'Cache-Control': case 'Set-Cookie':
			case 'Window-Target':
				$html[] = '<meta http-equiv="'.$key.'" content="'.$val.'" />';
				break;
			// keywords description author copyright robots date revisit-after generator
            case 'X-Ua-Compatible':
                break;
			default:
				$html[] = '<meta name="'.$key.'" content="'.$val.'" />';
				break;
			}
		}
		return implode("\n", $html);
	}
	protected function _renderHead($head, $designMode)
	{
		$html = array();
		$html[] = $this->_renderMeta($head['meta']);
		if ($head['title'])
		{
			$html[] = "<title>{$head[title]}</title>";
		}
		if ($designMode)
		{
			$this->env['usedDir'] = $this->usedDir;
		}
		$psn = $this->env['psn'];
		unset($this->env['psn']);
		$html[] = $this->_scriptEnv();
		
		if ($designMode)
		{
			resource::import('diy-base', array(
				'resource'=>array(
					SPEC_URL.'css/diy.css',
					'apps/special/css/diy-design.css',
					'apps/special/js/diy.js'
				),
				'depends'=>array(
					'fet', 'admin.repos', 'uidialog', 'cmstop', 'uploader',
					'contextMenu', 'colorInput', 'editplus'
				)
			));
		}
		else
		{
			resource::import('diy-base', array(
				'resource'=>array(
					SPEC_URL.'css/diy.css'
				),
				'depends'=>array('fet', 'repos')
			));
		}
		$needs = array('diy-base');
		
		$helperDir = SPEC_PATH.'widget';
		$helperUrl = SPEC_URL.'widget';
		if ($designMode)
		{
			$data = loader::model('admin/widgetEngine')->select('disabled=0', 'name');
			foreach ($data as $d)
			{
				$baseUrl = $helperUrl.'/'.$d['name'].'/';
				$file = $helperDir.'/'.$d['name'].'/backend.xml';
				if (is_file($file))
				{
					$package = file_get_xmlarray($file, 'package');
					$package['base'] = $baseUrl;
					$name = 'diy-widget-'.$d['name'];
					$needs[] = $name;
					resource::import($name, $package);
				}
			}
		}
		else
		{
			foreach ($this->_usedEngine() as $e)
			{
				$baseUrl = $helperUrl.'/'.$e.'/';
				$file = $helperDir.'/'.$e.'/frontend.xml';
				if (is_file($file))
				{
					$package = file_get_xmlarray($file, 'package');
					$package['base'] = $baseUrl;
					$name = 'diy-widget-'.$e;
					$needs[] = $name;
					resource::import($name, $package);
				}
			}
		}
		resource::needs($needs);
		$html[] = resource::toHtml($psn);
		$html[] = '<script type="text/javascript">fet.setAlias({IMG_URL:"'.IMG_URL.'",SPEC_URL:"'.SPEC_URL.'"})</script>';
		
		if ($designMode)
		{
			$url = $this->_usedUrl($this->themeUsed);
			$html[] = '<link rel="stylesheet" theme="'.$head['theme'].'" type="text/css" href="'.$url.'" />';
			$url = $this->_usedUrl($this->usedDir);
			$html[] = '<link rel="stylesheet" used="'.implode(',', $this->usedDir).'" type="text/css" href="'.$url.'" />';
		}
		else
		{
			$used = array_merge($this->themeUsed, $this->usedDir);
			if (!empty($used))
			{
				$url = $this->_usedUrl($used, $psn);
				$html[] = '<link rel="stylesheet" type="text/css" href="'.$url.'" />';
			}
		}
		
		if (!empty($head['resource']))
		{
			$html[] = $this->_renderRes((array)$head['resource'], $psn);
		}
		$html[] = $this->_cssRule();
		return implode('', $html);
	}
	protected function _renderArea($attr, $designMode, $root = false)
	{
		$class = self::DIY_CLASS_AREA;
		if ($root) $class .= ' '.self::DIY_CLASS_ROOT;
		$html = array();
		if ($designMode)
		{
			$html[] = '<div class="'.$class.'" style="'.$attr['style'].'" id="'.$attr['id'].'">';
		}
		else
		{
			$css = $this->_cssRule('#'.$attr['id'], $attr['style']);
			$html[] = '<div class="'.$class.'"  id="'.$attr['id'].'">';
		}
		
		foreach ($attr['items'] as $key=>&$val)
		{
			// widget
			if (isset($val['widgetid']))
			{
				$html[] = $this->_renderWidget($val, $designMode);
			}
			// frame
			else
			{
				$html[] = $this->_renderFrame($val, $designMode);
			}
		}
		$html[] = '</div>';
		return implode('', $html);
	}
	protected function _renderFrame($attr, $designMode)
	{
		$html = array();
		$fClass = self::DIY_CLASS_FRAME;
		if ($r = $this->_usedDir('frame', $attr['theme']['frame']))
		{
			$fClass .= ' '.$r;
		}
		$this->_cssRule('#'.$attr['id'], $attr['style']['frame']);
		$this->_cssRule('#'.$attr['id'].'-t', $attr['style']['title']);
		$this->_cssRule('#'.$attr['id'].'-t *', $attr['style']['title-w']);
		$this->_cssRule('#'.$attr['id'].'-t a', $attr['style']['title-a']);
		if ($designMode)
		{
			$html[] = implode('', array(
			'<div id="',$attr['id'],'" class="',$fClass,
				'" frame-theme="',$attr['theme']['frame'],
				'" title-theme="',$attr['theme']['title'],
				'" frame-style="',$attr['style']['frame'],
				'" title-style="',$attr['style']['title'],
				'" title-w-style="',$attr['style']['title-w'],
				'" title-a-style="',$attr['style']['title-a'],
			'">'));
		}
		else
		{
			$html[] = '<div id="'.$attr['id'].'" class="'.$fClass.'">';
		}
		if ($title = $this->_renderTitle(array(
			'id'=>$attr['id'].'-t',
			'theme'=>$attr['theme']['title'],
			'items'=>$attr['title']
		), $designMode))
		{
			$html[] = $title;
		}
		// area
		foreach ($attr['items'] as $item)
		{
			$html[] = $this->_renderArea($item, $designMode);
		}
		$html[] = '</div>';
		return implode('', $html);
	}
	protected function _renderWidget($attr, $designMode)
	{
		$widgetid = intval($attr['widgetid']);
		$widget = $attr['widget'] ? $attr['widget'] : $this->widget->get($widgetid);
		$engine = $widget ? $widget['engine'] : '';
		$wClass = self::DIY_CLASS_WIDGET;
		if ($r = $this->_usedDir('widget', $attr['theme']['widget']))
		{
			$wClass .= ' '.$r;
		}
		if ($designMode && (!$widget || $this->_isModified($widget)))
		{
			$wClass .= ' '.self::DIY_CLASS_MODIFIED;
		}
		$cClass = self::DIY_CLASS_CONTENT.' diy-content-'.$engine;
		if ($r = $this->_usedDir('content/'.$engine, $attr['theme']['content']))
		{
			$cClass .= ' '.$r;
		}
		$this->_cssRule('#'.$attr['id'], $attr['style']['widget']);
		$this->_cssRule('#'.$attr['id'].'-i', $attr['style']['inner']);
		$this->_cssRule('#'.$attr['id'].'-t', $attr['style']['title']);
		$this->_cssRule('#'.$attr['id'].'-c', $attr['style']['content']);
		$this->_cssRule('#'.$attr['id'].'-i *', $attr['style']['inner-w']);
		$this->_cssRule('#'.$attr['id'].'-t *', $attr['style']['title-w']);
		$this->_cssRule('#'.$attr['id'].'-c *', $attr['style']['content-w']);
		$this->_cssRule('#'.$attr['id'].'-i a', $attr['style']['inner-a']);
		$this->_cssRule('#'.$attr['id'].'-t a', $attr['style']['title-a']);
		$this->_cssRule('#'.$attr['id'].'-c a', $attr['style']['content-a']);
		$html = array();
		if ($designMode)
		{
			$html[] = implode('', array(
			'<div id="',$attr['id'],'" widgetid="',$widgetid,'" engine="',$engine,
				'" class="',$wClass,
				'" widget-theme="',$attr['theme']['widget'],
				'" title-theme="',$attr['theme']['title'],
				'" content-theme="',$attr['theme']['content'],
				'" widget-style="',$attr['style']['widget'],
				'" inner-style="',$attr['style']['inner'],
				'" title-style="',$attr['style']['title'],
				'" content-style="',$attr['style']['content'],
				'" inner-w-style="',$attr['style']['inner-w'],
				'" title-w-style="',$attr['style']['title-w'],
				'" content-w-style="',$attr['style']['content-w'],
				'" inner-a-style="',$attr['style']['inner-a'],
				'" title-a-style="',$attr['style']['title-a'],
				'" content-a-style="',$attr['style']['content-a'],
			'">'));
		}
		else
		{
			$html[] = '<div id="'.$attr['id'].'" class="'.$wClass.'">';
		}
		$html[] = '<div id="'.$attr['id'].'-i" class="'.self::DIY_CLASS_INNER.'">';
		
		if ($title = $this->_renderTitle(array(
			'id'=>$attr['id'].'-t',
			'theme'=>$attr['theme']['title'],
			'items'=>$attr['title']
		), $designMode))
		{
			$html[] = $title;
		}
		$html[] = '<div id="'.$attr['id'].'-c" class="'.$cClass.'">';
		if ($designMode)
		{
			if (! $widget)
			{
				$html[] = '[Render error: mission widget id="'.$widgetid.'"]';
			}
			else
			{
				try {
					$html[] = widgetEngine::render($engine, $widget);
				} catch (Exception $e) {
					$html[] = '[Render error: '.$e->getMessage().']';
				}
			}
		}
		else
		{
			$html[] = '<!--#include virtual="/widget/'.$widgetid.'.html"-->';
			if ($widget)
			{
				$this->_publishWidget($widget);
				$this->_usedEngine($engine);
			}
		}
		$html[] = '</div></div></div>';
		return implode('', $html);
	}
	protected function _renderTitle($attr, $designMode)
	{
		$tClass = self::DIY_CLASS_TITLE;
		if ($r = $this->_usedDir('title', $attr['theme']))
		{
			$tClass .= ' '.$r;
		}
		if (empty($attr['items']) || !is_array($attr['items']))
		{
			return '';
		}
		$html = array();
		$html[] = '<div id="'.$attr['id'].'" class="'.$tClass.'">';
		$title = array();
		foreach ($attr['items'] as $item)
		{
			$text = ($item['img'] ? ('<img src="'.$item['img'].'" />') : '').$item['text'];
			if (!strlen($text))
			{
				continue;
			}
			$text = 'style="'.$item['style'].'" item-style="'.$item['style'].'">'.$text;
			$title[] = $item['href']
				? ('<a href="'.$item['href'].'" '.$text.'</a>')
				: ('<span '.$text.'</span>');
		}
		$html[] = implode('', $title);
		$html[] = '</div>';
		return implode('', $html);
	}
	protected function _renderRes($res, $psn)
	{
		$needs = array();
		foreach ($res as $v)
		{
			$ext = strtolower(fileext($v));
			if ($ext != 'js' && $ext != 'css')
			{
				continue;
			}
			if (! ($pos = $this->psn->parse($v)))
			{
				continue;
			}
			$file = $pos['path'];
			$url = $pos['url'];
			if (!is_file($file) || !filesize($file))
			{
				continue;
			}
			$needs[] = $url;
		}
		return resource::toHtml($psn, $needs);
	}
	protected function _usedEngine($engine = null)
	{
		static $used = array();
		if (is_null($engine))
		{
			return array_unique($used);
		}
		else
		{
			$used[] = $engine;
		}
	}
	protected function _usedDir($key, $val = null)
	{
		$r = '';
		if ($val = trim($val))
		{
			if ($val != '(empty)')
			{
				$this->usedDir[] = $key.'/'.$val;
				$r = str_replace('/', '-', $key).'-'.$val;
			}
		}
		else if (!empty($this->themeDef[$key]))
		{
			$r = str_replace('/', '-', $key).'-'.$this->themeDef[$key];
		}
		return $r;
	}
	protected function _usedUrl($used, $pos = null)
	{
		$uiurl = SPEC_URL.'ui';
		$uipath = SPEC_PATH.'ui';
		$css = array();
		foreach (array_unique($used) as $t)
		{
			$baseurl = $uiurl.'/'.$t;
			$file = $uipath.'/'.$t.'/style.css';
			if ($c = resource::cssText($file, $baseurl))
			{
				$css[] = $c;
			}
		}
		return resource::toOne($css, $pos, 'css');
	}
	protected function _cssRule($selector = null, $rule = null)
	{
		static $cssrule = array();
		if (is_null($selector))
		{
			$css = implode('', $cssrule);
			return implode('', array(
				'<style id="ostyle" type="text/css">',
					$css,
				'</style>'
			));
		}
		else if ($rule)
		{
			$cssrule[] = "$selector{{$rule}}";
		}
	}
	
	protected function _scriptEnv()
	{
		$defaultname = setting('comment', 'defaultname');
		return '<script type="text/javascript">window.defaultname="'.$defaultname.'";window.ENV='.$this->json->encode($this->env).';</script>';
	}
	protected function _widgetFile($widgetid)
	{
		return WWW_PATH."widget/$widgetid.html";
	}
	protected function _isModified($widget)
	{
		return !$widget['published'] || $widget['published'] <= $widget['updated']
				|| !is_file($this->_widgetFile($widget['widgetid']));
	}
	
	protected function _testTemplate($data)
	{
		return ($err = $this->template->test($data))
			? "模板代码语法错误[{$err[0]}]，大概位置:{$err[1]}行" : false;
	}
	
	protected function _hasset($v)
	{
		if (is_array($v))
		{
			foreach ($v as $i)
			{
				if (trim($i) !== '')
				{
					return true;
				}
			}
			return false;
		}
		return trim($v) !== '';
	}
	protected function _cssvalue($v)
	{
		if (preg_match('/[\d\.]+px|[\d\.]+%|auto|inherit/', $v, $m))
		{
			return $m[0];
		}
		$v = floatval($v);
		return $v == 0 ? $v : floatval($v).'px';
	}
	protected function _multivalue($v)
	{
		$v = preg_spLit('/ +/', trim($v));
		return implode(' ', array_map(array($this, '_cssvalue'), $v));
	}
	protected function _absUrl($url, $baseurl)
	{
		if (preg_match('#^(?:[a-z]{3,10}):#i', $url))
		{
			return $url;
		}
		$url = trim($url);
		if ($url == '' || $url == '#' || $url{0} == '/')
		{
			return $url;
		}
	    return $baseurl . ($url{0} == '?' ? '' : '/'). $url;
	}
	protected function _errorOut($msg)
	{
		if (stristr($_SERVER['HTTP_ACCEPT'], 'json'))
		{
			exit ($this->json->encode(array('state'=>false,'error'=>$msg)));
		}
		else
		{
			if ($this->is_ajax())
			{
				exit ($msg);
			}
			else
			{
				$this->showmessage($msg);
			}
		}
	}
}
