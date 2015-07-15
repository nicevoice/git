<?php
abstract class page_controller_abstract extends controller
{
	protected $app, $cache, $json, $template, $view, $config, $setting, $system, $_userid, $_username, $_groupid, $_roleid;
	
	protected $page, $section, $log, $history, $template_dir;

    function __construct(& $app)
    {
        parent::__construct();
		$this->app = $app;
		$this->_userid = & $app->userid;
		$this->_username = & $app->username;
		$this->_groupid = & $app->groupid;
		$this->_roleid = & $app->roleid;

		$this->config = config::get('config');
		$this->setting = setting::get($app->app);
		$this->system = setting::get('system');
		
		$this->json = & factory::json();
		$this->cache = & factory::cache();
		
		$array = array('_userid'=>$this->_userid, '_username'=>$this->_username, '_groupid'=>$this->_groupid, '_roleid'=>$this->_roleid);
		
		if ($app->client === 'admin')
		{
			$this->view = & factory::view($app->app);
                        $this->view->assign('CONFIG',  $this->config);
			$this->view->assign('SETTING',  $this->setting);
			$this->view->assign('SYSTEM',  $this->system);
			$this->view->assign($array);
		}

		$this->template = & factory::template($app->app);
		$this->template->assign('CONFIG',  $this->config);
		$this->template->assign('SETTING',  $this->setting);
		$this->template->assign('SYSTEM',  $this->system);
		$this->template->assign($array);
		$this->section = loader::model('admin/section','page');
		$this->page = loader::model('admin/page','page');
		$this->log = loader::model('admin/section_log','page');
		$this->history = loader::model('admin/section_history','page');
		$config = config('template');
		$this->template_dir = ROOT_PATH.'templates/'.$config['name'].'/';
		
	    import('helper.xml');
    }
    
    public function execute()
    {
    	if ($this->action_exists($this->app->action))
    	{
    		$response = call_user_func_array(array($this, $this->app->action), $this->app->args);
    	}
    	else 
    	{
    		$this->_action_not_defined($this->app->action);
    	}
        return $response;
    }
    
    protected function _action_not_defined($action)
    {
    	$this->showmessage("<font color='red'>$action</font> 动作不存在");
    }
    
    protected function _section_view_html($section){
		if ($section['type']=='html')
		{
			return $section['data'];
		}
		switch ($section['type'])
		{
			case 'auto':
				$tpldata = $section['data'];
				$data = null;
				break;
			case 'hand':case 'feed': case 'rpc': case 'json':
				$data = $this->_string_to_array($section['data']);
				$tpldata = $section['template'];
				break;
			default:
				return '';
		}
		$orig_dir = $this->template->dir;
        $this->template->set_dir(CACHE_PATH);
		$tmp_template = 'section_'.$this->_userid.'_'.$section['sectionid'].'.tpl';
		$tpl = $this->_tmptplfile($tmp_template);
		write_file($tpl, $tpldata);
		$this->template->assign('data', $data);
		try {
			$data = $this->template->fetch($tmp_template);
		} catch (Exception $e) {
			$data = '';
		}
		@unlink($tpl);
		$this->template->set_dir($orig_dir);
		return $data;
	}
	protected function _section_preview_helper()
	{
		return '<link rel="stylesheet" type="text/css" href="apps/page/css/visual_edit.css" />
				</head>';
	}
	
    protected function _addHtmlData($data)
	{
		if (empty($data['name']))
		{
			return '缺少名称';
		}
        return array(
	       'pageid'=>$data['pageid'],
	       'type'=>'html',
	       'name'=>$data['name'],
	       'data'=>$data['data'],
	       'origdata'=>$data['data'],
	       'template'=>'',
	       'output'=>'html',
	       'width'=>$data['width'],
	       'description'=>$data['description']
	    );
	}
	protected function _addAutoData($data)
	{
		if (empty($data['name']))
		{
			return '缺少名称';
		}
		// check template syntax
		if ($data['data']) {
		    if (false !== ($error = $this->_testTemplate($data['data'])))
			{
				return $error;
			}
		}
	    $data = array(
	       'pageid'=>$data['pageid'],
	       'type'=>'auto',
	       'name'=>$data['name'],
	       'data'=>$data['data'],
	       'origdata'=>$data['data'],
	       'template'=>'',
	       'output'=>'html',
	       'width'=>$data['width'],
	       'description'=>$data['description'],
	       'frequency'=>intval($data['frequency'])
	    );
	    if ($data['frequency'] > 0)
		{
			$data['nextupdate'] = TIME + $data['frequency'];
		}
		return $data;
	}
	protected function _addHandData($data)
	{
		if (empty($data['name']))
		{
			return '缺少名称';
		}
		$outputs = is_array($data['output']) ? $data['output'] : explode(',', $data['output']);
		if (empty($outputs))
		{
			$outputs = array('html');
		}
        if (in_array('html', $outputs))
		{
			if (empty($data['data'])) {
				return '模板代码不能为空';
			}
			$data['template'] = $data['data'];
			if (false !== ($error = $this->_testTemplate($data['data'])))
			{
				return $error;
			}
		}
		$rows = empty($data['rows']) ? 6 : intval($data['rows']);
		if (empty($data['origdata']) || $this->_string_to_array($data['origdata']) === false)
		{
		    $origdata = $this->_array_to_string(array_fill(0, $rows, array()));
		}
		else
		{
			$origdata = $data['origdata'];
		}
	    $data = array(
	       'pageid'=>$data['pageid'],
	       'type'=>'hand',
	       'name'=>$data['name'],
	       'rows'=>$rows,
	       'data'=>$origdata,
	       'origdata'=>$origdata,
	       'template'=>$data['template'],
	       'output'=>implode_ids($outputs),
	       'width'=>$data['width'],
	       'description'=>$data['description']
	    );
		return $data;
	}
	protected function _addFeedData($data)
	{
		if (empty($data['name']))
		{
			return '缺少名称';
		}
		if (empty($data['url']))
		{
			return '缺少源地址';
		}
		$data['template'] = $data['data'];
        if (empty($data['template']))
		{
			return '模板代码不能为空';
		}
		if (false !== ($error = $this->_testTemplate($data['template'])))
		{
			return $error;
		}
	    $data = array(
	       'pageid'=>$data['pageid'],
	       'type'=>'feed',
	       'name'=>$data['name'],
	       'url'=>$data['url'],
	       'template'=>$data['template'],
	       'output'=>'html',
	       'width'=>$data['width'],
	       'description'=>$data['description'],
	       'frequency'=>intval($data['frequency'])
	    );
	    
		if ($data['frequency'] > 0)
		{
			$data['nextupdate'] = TIME + $data['frequency'];
		}
		return $data;
	}
	protected function _addRpcData($data)
	{
		if (empty($data['name']))
		{
			return '缺少名称';
		}
		if (empty($data['url']))
		{
			return '缺少源地址';
		}
		if (empty($data['method']))
		{
			return '缺少调用接口';
		}
	    $data['template'] = $data['data'];
        if (empty($data['template']))
		{
			return '模板代码不能为空';
		}
		if (false !== ($error = $this->_testTemplate($data['template'])))
		{
			return $error;
		}
	    $data = array(
	       'pageid'=>$data['pageid'],
	       'type'=>'rpc',
	       'name'=>$data['name'],
	       'url'=>$data['url'],
	       'method'=>$data['method'],
	       'args'=>$data['args'],
	       'template'=>$data['template'],
	       'output'=>'html',
	       'width'=>$data['width'],
	       'description'=>$data['description'],
	       'frequency'=>intval($data['frequency'])
	    );
		if ($data['frequency'] > 0)
		{
			$data['nextupdate'] = TIME + $data['frequency'];
		}
		return $data;
	}
	protected function _addJsonData($data)
	{
		if (empty($data['name']))
		{
			return '缺少名称';
		}
		if (empty($data['url']))
		{
			return '缺少源地址';
		}
		$data['template'] = $data['data'];
        if (empty($data['template']))
		{
			return '模板代码不能为空';
		}
		if (false !== ($error = $this->_testTemplate($data['template'])))
		{
			return $error;
		}
	    $data = array(
	       'pageid'=>$data['pageid'],
	       'type'=>'json',
	       'name'=>$data['name'],
	       'url'=>$data['url'],
	       'template'=>$data['template'],
	       'output'=>'html',
	       'width'=>$data['width'],
	       'description'=>$data['description'],
	       'frequency'=>intval($data['frequency'])
	    );
	    
		if ($data['frequency'] > 0)
		{
			$data['nextupdate'] = TIME + $data['frequency'];
		}
		return $data;
	}
    
    protected function _publishHtml($section)
	{
		$sectionid = $section['sectionid'];
	    $filepath = $this->_sectionPath($sectionid);
        $arr = array(
        	'nextupdate' => TIME + $section['frequency']
        );
	    if (false !== write_file($filepath, $section['data']))
		{
			$this->log->add(array('action'=>'publish', 'sectionid'=>$sectionid));
		    $this->section->published($sectionid, $arr);
		    return array(
		        'state'=>true,
		        'info'=>'区块发布成功'
		    );
		}
		else
		{
			$this->section->nextupdate($sectionid,$arr);
		    return array(
		        'state'=>false,
		        'error'=>'区块发布失败'
		    );
		}
	}
	protected function _publishAuto($section)
	{
		$sectionid = $section['sectionid'];
	    $filepath = $this->_sectionPath($sectionid);
	    
	    $orig_dir = $this->template->dir;
        $this->template->set_dir(CACHE_PATH);
	    $tmp_template = 'section_'.$this->_userid.'_'.$section['sectionid'].'.tpl';
        $tpl = $this->_tmptplfile($tmp_template);
		write_file($tpl, $section['data']);
		$html = $this->template->fetch($tmp_template);
		@unlink($tpl);
		$this->template->set_dir($orig_dir);
        $arr = array(
        	'nextupdate' => TIME + $section['frequency']
        );
		if (false !== write_file($filepath, $html))
		{
			$this->log->add(array('action'=>'publish', 'sectionid'=>$sectionid));
		    $this->section->published($sectionid, $arr);
		    return array(
		        'state'=>true,
		        'info'=>'区块发布成功'
		    );
		}
		else
		{
			$this->section->nextupdate($sectionid,$arr);
		    return array(
		        'state'=>false,
		        'error'=>'区块发布失败'
		    );
		}
	}
	protected function _publishHand($section)
	{
		$sectionid = $section['sectionid'];
	    $ok = true;
	    $info = array();
	    
	    $orig_dir = $this->template->dir;
        $this->template->set_dir(CACHE_PATH);
	    $tmp_template = 'section_'.$this->_userid.'_'.$section['sectionid'].'.tpl';
        $tpl = $this->_tmptplfile($tmp_template);
        write_file($tpl, $section['template']);
		$d = $this->_string_to_array($section['data']);
		if ($d === FALSE)
		{
			$d = array();
		}
		
		foreach (explode(',', $section['output']) as $type)
        {
        	$type = strtolower($type);
        	$filepath = $this->_sectionPath($sectionid,$type);
        	if ($type == 'json')
        	{
        	    $output = $this->json->encode($d);
        	}
        	elseif ($type == 'xml')
        	{
        		$output = array2xml($d);
        	}
        	else
        	{
        		$this->template->assign('data', $d);
        	    $output = $this->template->fetch($tmp_template);
        	}
            if (false !== write_file($filepath, $output))
			{
				$info[] = '发布'.$type.'区块成功';
			    $this->log->add(array('action'=>'publish', 'sectionid'=>$sectionid));
			}
			else
			{
			    $info[] = '发布'.$type.'区块失败';
			    $ok = false;
			}
        }
        @unlink($tpl);
        $this->template->set_dir($orig_dir);
        $arr = array(
        	'nextupdate' => TIME + $section['frequency']
        );
        if ($ok)
        {
            $this->section->published($sectionid,$arr);
        }
        else
        {
        	$this->section->nextupdate($sectionid,$arr);
        }
        $info = implode('，',$info);
        return $ok ? array('state'=>true,'info'=>$info)
                   : array('state'=>false,'error'=>$info);
	}
	
	protected function _publishRpc($section)
	{
		$sectionid = $section['sectionid'];
		$arr = array(
        	'nextupdate' => TIME + $section['frequency']
        );
        $this->section->nextupdate($sectionid,$arr);
		import('helper.xmlrpc_client');
		//参数设置
        $output_options = array(
            "output_type" => 'php',
            "verbosity" => "pretty",
            "escaping" => array("markup", "non-ascii", "non-print"),
            "version" => "xmlrpc",
            "encoding" => "utf-8"
        );
        
        $xmlrpc_client = new xmlrpc_client($section['url'], 'POST');
        
        $data = $xmlrpc_client->request($section['method'], explode(',',$section['args']), $output_options);
        if (!$data) {
            return array('state'=>false,'error'=>"RPC抓取失败");
        }
        $info = array('RPC抓取成功');
	    $ok = 1;
	    $orig_dir = $this->template->dir;
        $this->template->set_dir(CACHE_PATH);
	    $tmp_template = 'section_'.$this->_userid.'_'.$section['sectionid'].'.tpl';
        $tpl = $this->_tmptplfile($tmp_template);
		write_file($tpl, $section['template']);
        $this->template->assign('data', $data);
        $output = $this->template->fetch($tmp_template);
        @unlink($tpl);
        $this->template->set_dir($orig_dir);
        
        $filepath = $this->_sectionPath($sectionid);;
        if (false !== write_file($filepath, $output))
		{
			$info[] = '区块保存成功';
		    $this->log->add(array('action'=>'catch', 'sectionid'=>$sectionid));
		} else {
		    $info[] = '区块保存失败';
		    $ok = 0;
		}
		
	    if (!$this->history->add(array('sectionid'=>$sectionid, 'data'=>$section['data']))) 
		{
		    $info[] = '记录生成失败，未保存本次内容';
		    return array('state'=>false,'error'=>implode('，',$info));
		}
		if ($this->section->edit($sectionid, array('data'=>$this->_array_to_string($data),'published'=>TIME)))
	    {
	        $info[] = '入库成功';
	        $this->log->add(array('action'=>'edit', 'sectionid'=>$sectionid));
	    }
	    else
	    {
	        $info[] = '入库失败';
	        $ok = 0;
	    }
	    $info = implode('，',$info);
	    return $ok ? array('state'=>true,'info'=>$info,'html'=>$output)
	               : array('state'=>false,'error'=>$info);
	}
	protected function _publishFeed($section)
	{
		$sectionid = $section['sectionid'];
		$arr = array(
        	'nextupdate' => TIME + $section['frequency']
        );
		// 抓取列表
	    $data = file_get_contents($section['url']);
	    if (!$data)
	    {
	        return array('state'=>false,'error'=>"RSS抓取失败");
	    }
	    $info = array('RSS抓取成功');
	    $ok = 1;
	    $data = xml2array(remove_bom(trim($data)));
	    $data = $data['rss']['channel']['item'];
	    
	    // 获得输出
	    $orig_dir = $this->template->dir;
        $this->template->set_dir(CACHE_PATH);
        $tmp_template = 'section_'.$this->_userid.'_'.$section['sectionid'].'.tpl';
        $tpl = $this->_tmptplfile($tmp_template);
		write_file($tpl, $section['template']);
        $this->template->assign('data', $data);
        $output = $this->template->fetch($tmp_template);
        @unlink($tpl);
        $this->template->set_dir($orig_dir);
        
        // 发布
        $filepath = $this->_sectionPath($sectionid);;
        if (false !== write_file($filepath, $output))
		{
			$info[] = '区块保存成功';
		    $this->log->add(array('action'=>'catch', 'sectionid'=>$sectionid));
		}
		else
		{
		    $info[] = '区块保存失败';
		    $ok = 0;
		}
		
	    if (!$this->history->add(array('sectionid'=>$sectionid, 'data'=>$section['data']))) 
		{
		    $info[] = '记录生成失败，未保存本次内容';
		    return array('state'=>false,'error'=>implode('，',$info));
		}
		if ($this->section->edit($sectionid, array('data'=>$this->_array_to_string($data),'published'=>TIME)))
	    {
	        $info[] = '入库成功';
	        $this->log->add(array('action'=>'edit', 'sectionid'=>$sectionid));
	    }
	    else
	    {
	        $info[] = '入库失败';
	        $ok = 0;
	    }
	    $info = implode('，',$info);
	    return $ok ? array('state'=>true,'info'=>$info,'html'=>$output)
	               : array('state'=>false,'error'=>$info);
	}
	protected function _publishJson($section)
	{
		$sectionid = $section['sectionid'];
		$arr = array(
        	'nextupdate' => TIME + $section['frequency']
        );
	    $data = file_get_contents($section['url']);
	    if (!$data)
	    {
	        return array('state'=>false,'error'=>"JSON抓取失败");
	    }
	    $info = array('JSON抓取成功');
	    $ok = 1;
	    $data = $this->json->decode(remove_bom(trim($data)));
	    
	    $orig_dir = $this->template->dir;
        $this->template->set_dir(CACHE_PATH);
        $tmp_template = 'section_'.$this->_userid.'_'.$section['sectionid'].'.tpl';
        $tpl = $this->_tmptplfile($tmp_template);
		write_file($tpl, $section['template']);
        $this->template->assign('data', $data);
        $output = $this->template->fetch($tmp_template);
        @unlink($tpl);
        $this->template->set_dir($orig_dir);
        
        $filepath = $this->_sectionPath($sectionid);;
        if (false !== write_file($filepath, $output))
		{
			$info[] = '区块保存成功';
		    $this->log->add(array('action'=>'catch', 'sectionid'=>$sectionid));
		}
		else
		{
		    $info[] = '区块保存失败';
		    $ok = 0;
		}
		
	    if (!$this->history->add(array('sectionid'=>$sectionid, 'data'=>$section['data']))) 
		{
		    $info[] = '记录生成失败，未保存本次内容';
		    return array('state'=>false,'error'=>implode('，',$info));
		}
		if ($this->section->edit($sectionid, array('data'=>$this->_array_to_string($data),'published'=>TIME)))
	    {
	        $info[] = '入库成功';
	        $this->log->add(array('action'=>'edit', 'sectionid'=>$sectionid));
	    }
	    else
	    {
	        $info[] = '入库失败';
	        $ok = 0;
	    }
	    $info = implode('，',$info);
	    return $ok ? array('state'=>true,'info'=>$info,'html'=>$output)
	               : array('state'=>false,'error'=>$info);
	}
    
	protected function _testTemplate($data)
	{
		return ($err = $this->template->test($data))
			? "模板代码语法错误[{$err[0]}]，大概位置:{$err[1]}行" : false;
	}
    protected function _tmptplfile($filename)
	{
		return CACHE_PATH.$filename;
	}
	protected function _tplfile($filename)
	{
		return $this->template_dir.$filename;
	}
	protected function _sectionPath($sectionid, $ext = 'html')
	{
		return WWW_PATH.'section/'.$sectionid.'.'.$ext;
	}
	
	protected function _error_out($msg)
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
	 * 将数组替换成字符串用来逐步把区块中的序列化替换成JSON
	 */
	protected function _array_to_string($array)
	{
		$string	= $this->json->encode($array);
		return $string;
	}

	/**
	 * 将字符串替换成数组用来逐步把区块中的序列化替换成JSON
	 * 兼容性处理:当不为序列化时进行json_decode
	 */
	protected function _string_to_array($string)
	{
		if (substr($string, 0, 1) != '{' && substr($string, 0, 1) != '|' && $array = unserialize($string))
		{
			return $array;
		}
		return $this->json->decode($string);
	}
}