<?php
/**
 * 自由列表页面 -- 列表管理
 *
 * @aca 列表管理
 */
class controller_admin_freelist extends freelist_controller_abstract
{
	private $freelist, $grouplist, $group, $uri, $pagesize = 15, $size = 40;
	const DEFAULT_TPL = 'freelist/list.html';
	const DEFAULT_SIGN = '_';

	public function __construct(& $app)
	{
		parent::__construct($app);

		import('helper.pinyin');
		import('helper.xml');
		$this->uri = loader::lib('uri','system');
		$this->freelist = loader::model('admin/freelist');
		$this->grouplist = loader::model('admin/freelist_group');
	}

    /**
     * 浏览
     *
     * @aca 浏览
     */
	public function index()
	{
		// 获取已有分组列表
		$grouplist = $this->grouplist->select();

		$this->view->assign('grouplist', $grouplist);
		$this->view->assign('head', array('title'=>'自由列表'));
		$this->view->display("freelist/index");
	}

    /**
     * 列表
     *
     * @aca 浏览
     */
	public function page()
	{
		// model 用到联合查询 f => freelist， g => freelist_group
		// name => 列表名称 ， gid => 分组名称
		if (isset($_GET['keywords']) && $_GET['keywords']) $where = where_keywords('f.name', $_GET['keywords']);
		if (isset($_GET['gid']) && $_GET['gid']) $where = where_keywords('g.gid', $_GET['gid']);

		// 默认时间排序
		$order = '`created` DESC';
		$page = max((isset($_GET['page']) ? intval($_GET['page']) : 1), 1);
		$size = max((isset($_GET['pagesize']) ? intval($_GET['pagesize']) : $this->pagesize), 1);
		$data = $this->freelist->page($where, $order, $page, $size);
		$total = $this->freelist->count();

		$result = array('total'=>$total, 'data'=>$data);
		echo $this->json->encode($result);
	}

	/**
     * 基本设置
     *
     * @aca 基本设置
     */
	public function add() 
	{
    	if ($this->is_post())
        {
			if ($id = $this->freelist->add($_POST))
			{
				$json = array(
					'state'=>true, 
					'data'=>$this->freelist->get_byid($id)
				);
			}
			else
			{
				$json = array(
					'state'=>false, 
					'error'=>$this->freelist->error()
				);
			}
			exit($this->json->encode($json));
        }

		$flid = intval($_GET['flid']);
		// 获取需要修改的数据
        if($flid>0 && $freelist = $this->freelist->get($flid))
		{
			$this->view->assign('freelist', $freelist);
		}

		// 获取已有分组列表
		if($grouplist = $this->grouplist->select()) 
		{
			$this->view->assign('grouplist', $grouplist);
		}

        $this->view->display('freelist/form');
	}

	/**
     * 配置筛选器
     *
     * @aca 配置筛选器
     */
	public function fadd()
	{
    	if ($this->is_post())
        {
			if ($id = $this->freelist->add($_POST))
			{
				$json = array(
					'state'=>true, 
					'data'=>$this->freelist->get_byid($id)
				);
			}
			else
			{
				$json = array(
					'state'=>false, 
					'error'=>$this->freelist->error()
				);
			}
			exit($this->json->encode($json));
        }

		$flid = intval($_GET['flid']);

		// 获取需要修改的数据
        if($freelist = $this->freelist->get($flid))
		{
			$options =  $this->json->decode($freelist['filterules']);

			$this->view->assign('flid', $flid);
		}

		// 排序 用到的数据
		$sortset = array(
        	'published'=>'发布时间',
        	'contentid'=>'ID',
        	'pv'=>'浏览量',
        	'comments'=>'评论数'
        );
        $this->view->assign('sortset', $sortset);

		// 如果 规则 里面有数据则是编辑页面
		if($options) 
		{
			$this->view->assign('options', $options);
			$this->view->display('freelist/filter_edit');
		}
		else
		{
		  $this->view->display('freelist/filter');
		}
	}

	/**
     * 显示列表
     *
     * @aca 显示列表
     * @param $flids
     * @return array
     */
	public function getview($flids) 
	{
		if ($flids)
		{
			$arrflid = explode(',', $flids);
		}
		else 
		{
			$where = 'autopublish=1 AND nextpublish > published AND nextpublish <= '.TIME;
			$arrflid = $this->freelist->gets_field('flid', $where);
		}
		if (empty($arrflid))
		{
			$result = array('state' => false, 'info' => '没有列表需要更新');
			exit(json_encode($result));
		}
		foreach($arrflid as $flid)
		{
			$json = $this->_getview($flid);
			// 更新下次执行时间
			$db = & factory::db();
			$db->exec("UPDATE #table_freelist SET published=" .TIME .",nextpublish=" .TIME ."+(frequency*60) WHERE `flid`=$flid");
		}
		return $json;
	}

	// 单个执行
	private function _getview($flid)
	{
		$setting = $this->freelist->get($flid);
		$filename = $setting['filename'];
		$u = $this->uri->psn($setting['path']);
		$s = $setting['pagesize'];
		
		$total = count($this->_getContent($setting['filterules']));	// 统计数据总条数
		$num = ceil($total / $s);	// 计算循环次数
		$num = min($setting['maxpage'],$num);
		
		$ext = array(SHTML, '.xml', '.json');
		$fileext = $ext[$setting['type']];
		$file = $u['path'].DS.$filename;

		for($i = 0; $i <= $num; $i++) {
			$c = array('p' => $i, 's' => $s, 'u' => $u, 'f' => $filename, 'mp' => $num, 'ext' => $fileext);
			$data = $this->_getData($setting, $c);

			$n = $i == 0 ? '' : self::DEFAULT_SIGN.$i;
			$temp = $file.$n.$fileext;
			file_exists($temp) && unlink($temp);
			write_file($temp, $data);
		}

		return array_merge(
			$this->freelist->get_byid($flid),
			array(
				'url' => $u['url'].'/'.$filename.$fileext
			)
		);
	}

	/**
	 * 获取数据 
	 *
	 * @param $setting array 配置信息
	 * @param $c array 基础信息
	 */
	private function _getData($setting, $c)
	{
		if(!$setting) return false;

		$page = max(isset($c['p']) ? intval($c['p']) : 1, 1);
		$size =	isset($c['s']) ? intval($c['s']) : $this->size;

		$u = $c['u'];
		$filename = $c['f'];
		$url = $u['url'].'/'.$filename;
		$filepath = $u['path'].'/'.$filename;

		$d = $this->_getContent(
			$setting['filterules'],
			array(
				'size' => $size,
				'page' => $page
			)
		);

		$maxpage = min($d['total'], $c['mp'] * $c['s']);
		$pagination = pages($maxpage, $d['page'], $d['size'], 2, $url . self::DEFAULT_SIGN . '{$page}' . $c['ext']);
		// 模板路径
		$tmpconfig = config('template');
        $dir = ROOT_PATH.'templates/' .$tmpconfig['name']. '/';
		$template = $setting['template'] && file_exists(realpath($dir.$setting['template']))
					? $setting['template']
					: self::DEFAULT_TPL;
        return $this->template
					->assign('data', $d['data'])
					->assign('pagination', $pagination)
					->assign($setting)
					->fetch($template);
	}

	/**
	 * 解析规则获取内容
	 * 
	 * @param $filterules array 规则
	 * @param $num array 自定义参数
	 */
	private function _getContent($filterules, $num = array()) 
	{
		$options = $this->json->decode($filterules);
		$options['fields'] = 'contentid , title, url ,published as `time`';
		if ($options['weight']['range'])
		{
			$options['weight'] = $options['weight'][0].','.$options['weight'][1];
		}
		else
		{
			$options['weight'] = $options['weight'][0];
		}
		$orderby = array();
		foreach ($options['orderby'][0] as $i=>$f)
		{
			$s = $options['orderby'][1][$i];
			$orderby = "$f $s";
		}
		$options['orderby'] = implode(',', $orderby);
		$options['page'] = (int) $num['page'];
		$options['size'] = (int) $num['size'];

		return tag_content($options);
	}

	private function _deleteFile($setting)
	{
		$filename = $setting['filename'];
		$u = $this->uri->psn($setting['path']);

		if(!$filename) return false;
		$filepath = $u['path'].DS.$filename;
		foreach(glob($filepath.'*') as $file)
		{
			$dirpath = dirname(realpath($file));
			$dirpath = str_replace('\\', '/', $dirpath);
			if (stripos($dirpath, WWW_PATH) !== false && $dirpath != WWW_PATH)
			{
				file_exists($file) && unlink($file) && rmdir($dirpath);
			}
		}
	}

	/**
	 * 计划任务
	 *
     * @aca 计划任务
	 */
	public function cron()
	{
		$typeid = isset($_GET['id']) ? $_GET['id'] : '';

		exit(
			$this->json->encode(
				array(
					'state' => $this->getview($typeid)
				)
			)
		);
	}

	/**
     * 批量删除
     *
     * @aca 批量删除
     */
	public function delete()
	{
		$typeid = $_GET['id'];
		$setting = $this->freelist->get($typeid);
		$result = $this->freelist->delete($typeid)
					? array('state'=> true, 'delfile' => $this->_deleteFile($setting))

					: array('state'=>false,'error'=>$this->freelist->error());
		echo $this->json->encode($result);
	}

	/**
     * 批量更新
     *
     * @aca 批量更新
     */
	public function update()
	{
		$typeid = $_GET['id'];
		$result = $this->freelist->fupdate($typeid) 
				? array('state'=>true, 'data'=> $this->getview($typeid))
				: array('state'=>false,'error'=>$this->freelist->error());
		echo $this->json->encode($result);
	}

	/**
     * 批量停止更新
     *
     * @aca 批量停止更新
     */
	public function stop() 
	{
		$typeid = $_GET['id'];
		$result = $this->freelist->fstop($typeid) 
				? array('state'=>true, 'data'=>$this->freelist->get_byid($typeid))
				: array('state'=>false,'error'=>$this->freelist->error());
		echo $this->json->encode($result);
	}
}
