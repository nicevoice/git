<?php

class controller_index extends space_controller_abstract
{
	private $space, $article, $pagesize = 10;
	
	function __construct(&$app)
	{
		parent::__construct($app);
		$this->space = loader::model('space'); 
		$this->article = loader::model('admin/article','article');
	}
	
	function index()
	{
		$page = empty($_GET['page']) ? 1 : intval($_GET['page']);
		if ($this->system['pagecached'])
		{
			$keyid = md5('pagecached_space_index_index_' .$page);
			cmstop::cache_start($this->system['pagecachettl'], $keyid);
		}
		
		$total = $this->space->totalPosts();
		$this->template->assign('page', $page);
		$this->template->assign('pagesize', $this->pagesize);
		$this->template->assign('total', $total);
		$this->template->display('space/index.html');
		
		if ($this->system['pagecached']) cmstop::cache_end();
	}
	
	function homepage()
	{
		$space = $this->space->get(array('alias' => trim($_GET['space'])));
		if(!$space)
		{
			$this->showmessage('指定的专栏不存在');
		}
		else if (intval($space['status']) < 3)
		{
			$this->showmessage('专栏状态未开通');
		}
		
		$this->space->set_inc('pv', $space['spaceid']);
		if(empty($space['name'])) $space['name'] = $space['name'].'的个人专栏';
		
		$where = array (
			'spaceid' => $space['spaceid'],
			'status' => 6
		);

		$articles = $this->article->ls($where, '*', '`published` DESC', 1, $this->pagesize, true);
		foreach($articles as $k => $v)
		{
			if(empty($v['description']))
				$articles[$k]['description'] = str_cut(strip_tags($v['content']),160);
			$articles[$k]['comment_url'] = APP_URL.url('comment/comment/index',"contentid=".$v['contentid']);
			$articles[$k]['published'] = time_format(strtotime($v['published']),'Y年n月j日 G:i');
		}
		
		$articles_count = $this->article->total;
		$comment = $this->space->get_comment($space['spaceid'], 1, 10);

		$this->template->assign('space', $space);
		$this->template->assign('comment', $comment);
		$this->template->assign('articles', $articles);
		$this->template->assign('articles_count',intval($articles_count));
		$this->template->display('space/space.html');
	}
	
	function listing()
	{
		if ($this->system['pagecached'])
		{
			$keyid = md5('pagecached_space_index_listing_');
			cmstop::cache_start($this->system['pagecachettl'], $keyid);
		}
		$this->template->display('space/list.html');
		if ($this->system['pagecached']) cmstop::cache_end();
	}
	
	function page()
	{
		$where = array (
			'spaceid' => intval($_GET['spaceid']),
			'status' => 6
		);
		$fields = '*';
		$order = (empty($_GET['order']) || !in_array($_GET['order'],array('latest','hits','hot')))?'latest':$_GET['order'];
		switch($order)
		{
			case 'hits':$order = '`pv` DESC';break;
			case 'hot':$order = '`comments` DESC';break;
			default:
				$order = '`published` DESC';
		}
		$page = isset($_GET['page'])?max(intval($_GET['page']),1):1;
		
		$data = $this->article->ls($where, $fields, $order, $page, $this->pagesize, true);
		
		foreach($data as $k =>$v)
		{
			if(empty($v['description'])) $data[$k]['description'] = str_cut(strip_tags($v['content']),255);
			$data[$k]['comment_url'] = APP_URL.url('comment/comment/index',"contentid=".$v['contentid']);
			$data[$k]['published'] = time_format(strtotime($v['published']),'Y年n月j日 G:i');
		}
		$total = $this->article->total;
		echo $this->json->encode(array('data' => $data, 'total' => intval($total)));
	}
	
	function rss()
	{
		$space = $this->space->get(array('alias' => trim($_GET['space'])));
		if(!$space) $this->showmessage('指定的专栏不存在');
		
		$rssurl = SPACE_URL.$spaceurl;

		$where = array (
			'spaceid' => $space['spaceid'],
			'status' => 6
		);
		$fields = '*';
		$order = '`published` DESC';
		$list = $this->article->ls($where, $fields, $order, 1, 20, true);
		
		foreach($list as $k => $v)
		{
			$list[$k]['published'] = gmdate('D,d M Y H:i:s', strtotime($v['published'])).' GMT+8';
			$list[$k]['comments'] = str_replace('&','&amp;',url('comment/comment/index','contentid='.$v['contentid'], true)); //地址有问题
			if(empty($v['description']))
				$list[$k]['description'] = str_cut(strip_tags($v['content']),160);
		}
		$pubDate = gmdate('D,d M Y H:i:s', TIME.' GMT+8');
		
		$this->template->assign('title',$space['name']);
		$this->template->assign('rssurl',$rssurl);
		$this->template->assign('pubDate',$pubDate);
		$this->template->assign('author',$space['name']);
		$this->template->assign('sitename',setting('system','sitename'));
		$this->template->assign('list',$list);
		$feet_content_type = 'text/xml';
		$charset = config::get('config', 'charset', 'utf-8');
		header('Content-Type: ' . $feet_content_type . '; charset=' . $charset, true);
		echo '<?xml version="1.0" encoding="'.$charset.'"?>'."\n";
		$this->template->display('space/rss.xml');
	}
}