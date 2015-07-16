<?php

class controller_wap extends wap_controller_abstract
{
	private $wap, $pagesize = 15, $offset = 3, $catids;
	
	function __construct(& $app)
	{
		parent::__construct($app);
        $this->catids = normalize_categoryids($this->setting['catids'], true, false);
		$this->wap = loader::model('wap');
		$this->_header();
	}

	function index()
	{
		$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
		
		$channel = channel();

		foreach ($channel as $channelid=>&$c)
		{
			if (!in_array($c['catid'], $this->catids)) unset($channel[$channelid]);
		}
		$this->template->assign('channel', $channel);
		$this->template->assign('modelids', implode(',', $this->setting['modelids']));
		$this->template->assign('catids', implode_ids($this->catids));
		$this->template->assign('index_weight', $this->setting['index_weight']);
		$this->template->assign('pagesize', $this->setting['index_pagesize']);
		$this->template->assign('page', $page);
		$this->template->display($this->setting['template_index']);
	}

	function category()
	{
		$catid = intval($_GET['catid']);
		$page = isset($_GET['page']) ? intval($_GET['page']) : 1; 
		if (!in_array($catid, $this->catids)) $this->showmessage('栏目不存在', 'index.php');

		$children = $this->wap->content->category[$catid]['childids'];
		if ($children)
		{
			$children = explode(',', $children);
			foreach ($children as $childid)
			{
				if (in_array($childid, $this->catids))
				{
					$childids[$childid] = $this->wap->content->category[$childid];
				}
			}
			$size = intval($this->setting['category_pagesize']);
		}
		else 
		{
			$size = intval($this->setting['list_pagesize']);
		}
		$category = loader::model('category', 'system');
		$pos = $category->pos($catid);
		
		$this->template->assign('category', $this->wap->content->category[$catid]);
		$this->template->assign('childids', $childids);
		$this->template->assign('pos', $pos);
		$this->template->assign('modelids', implode(',', $this->setting['modelids']));
		$this->template->assign('list_weight', $this->setting['list_weight']);
		$this->template->assign('pagesize', $size);
		$this->template->assign('page', $page);
		$this->template->display($this->setting['template_list']);
	}

	function show()
	{
		$contentid = intval($_GET['contentid']);
		$page = isset($_GET['page']) ? intval($_GET['page']) : 0;
		$type = $_GET['type'];

		$r = $this->wap->get($contentid);
		if (!in_array($r['catid'], $this->catids))
		{
			$this->showmessage('栏目不存在', 'index.php');
		}
		if (!$r) $this->showmessage($this->wap->error, 'index.php');
		if (!in_array($r['modelid'], $this->setting['modelids'])) $this->showmessage('内容不支持WAP', 'index.php');

		if ($type != 'all')
		{
			$this->_read($r, $page);
		}
		else 
		{
			$category = loader::model('category', 'system');
			$pos = $category->pos($r['catid']);

			$this->template->assign($r);
			$this->template->assign('pos', $pos);
			$this->template->assign('relateds', $this->wap->related($contentid));
			$this->template->assign('tags', explode(' ', $r['tags']));
			$this->template->display($this->_template($r['modelid']));
		}
	}

	function image()
	{
		$path = $_GET['path'];
		$image_path = UPLOAD_PATH.$path;
		if (file_exists($image_path))
		{
			$img_info = getimagesize($image_path);
			$image_info = array (
				'src'=>UPLOAD_URL.$path, 
				'width'=>$img_info[0], 
				'height'=>$img_info[1], 
				'size'=>round(filesize($image_path)/1024, 2),
				'type'=>$img_info['mime']
			);
			$this->template->assign($image_info);
			$this->template->display('wap/image.html');
		}
		else
		{
			$this->showmessage('图片不存在', 'index.php');
		}
	}

	function comment()
	{
		$contentid = intval($_GET['contentid']);
		$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

		$content = $this->wap->get($contentid);
		if (!$content) $this->showmessage($this->wap->error, 'index.php');
		if (!in_array($content['modelid'], $this->setting['modelids'])) $this->showmessage('内容不支持WAP', 'index.php');

		$this->template->assign('contentid', $contentid);
		$this->template->assign('topicid', $content['topicid']);
		$this->template->assign('title', $content['title']);
		$this->template->assign('pagesize', $this->setting['comment_pagesize']);
		$this->template->assign('page', $page);
		$this->template->display($this->setting['template_comment']);
	}

	public function showmessage($message, $url = null, $ms = 2000)
	{
		$this->template->assign('message', $message);
		$this->template->assign('url', $url);
		$this->template->assign('ms', $ms);
		$this->template->display('wap/showmessage.html', 'wap');
		exit;
	}

	private function  _header()
	{
		header("Content-type: text/vnd.wap.wml; charset=UTF-8");
		header("Cache-Control: no-cache, must-revalidate");
		header("Pragma: no-cache");
		echo '<?xml version="1.0" encoding="UTF-8"?>';
		if (!$this->setting['open']) $this->showmessage($this->setting['webname'].'的WAP服务已关闭', 'index.php');
	}

	private function _template($modelid)
	{
		$alias = table('model', $modelid, 'alias');
		$template = isset($this->setting['template_'.$alias]) ? $this->setting['template_'.$alias] : false;
		return $template;
	}

	private function _read($r, $page)
	{
		$date = getdate($r['created']);
		$path = CACHE_PATH.'wap'.DS.$date['year'].DS.$date['mon'].$date['mday'].DS.$r['contentid'];
		$first_path = $path.'.xml';

		$expires = $this->setting['content_expires'];
		if ($expires == -1)
		{
			$expires = setting('system', 'pagecontentttl');
		}
		if (!file_exists($first_path) || TIME - filemtime($first_path) >= $expires)
		{
			$this->_html_create($r);
		}
		if ($page > 1)	$path .= '_'.$page;
		$path .= '.xml';

		if (!file_exists($path)) $path = $first_path;
		echo file_get_contents($path);exit;
	}

	function _html_create($r)
	{
		$data = $this->wap->pages($r);
		import('helper.folder');

		$pages = count($data);
		$category = loader::model('category', 'system');
		$pos = $category->pos($r['catid']);
		foreach ($data as $i=>$v)
		{
			if ($r['modelid'] == 2)
			{
				$r['picture'] = $v;
			}
			else 
			{
				$r['content'] = $v['content'];
			}
			$p = $i + 1;
			$per_words_count = $this->setting['content_words'] ? $this->setting['content_words'] : 500;
			$total = $pages*$per_words_count;
			$page = pages($total, $p, $per_words_count, $this->offset, WAP_URL.'?action=show&contentid='.$r['contentid']);

			$this->template->assign($r);
			$this->template->assign('pos', $pos);
			$this->template->assign('relateds', $this->wap->related($r['contentid']));
			$this->template->assign('page', $page);
			$str = $this->template->fetch($this->_template($r['modelid']));
			$filename = $v['path'];
			folder::create(dirname($filename), 0777);
			write_file($filename, $str);
		}
	}
}