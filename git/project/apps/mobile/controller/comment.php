<?php

class controller_comment extends mobile_controller_abstract
{
	private $mobile, $comment;
	
	function __construct(& $app)
	{
		parent::__construct($app);
        $this->mobile = loader::model('mobile');
		if (!$this->setting['open']) $this->showmessage($this->setting['webname'].'的mobile服务已关闭', 'index.php');
	}

	function index()
	{
		$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
		$_key = 'mobile_comment_index_'.$page;
		if(!$data = $this->cache->get($_key))
		{
            $catids = $this->mobile->filter_catids($this->setting['catids']);
            $catids = array_keys($catids);
			$q = array(
				'weight'=>$this->setting['weight'],
                'comments'=>'1,65535',
				'catid'=>implode(',',$catids)
			);
            if($this->setting['comment_days'])
            {
                $q['before'] = TIME - intval($this->setting['comment_days']) * 86400;
                $q['after'] = TIME;
            }
			$order = 'comments';
			$orderby = 'DESC';
			$this->mobile = loader::model('mobile');
			$data = $this->mobile->ls_comment($q,$order,$orderby,$page);
			// 放入缓存
			if($this->setting['cache']) $this->cache->set($_key,$data,$this->setting['cache']);
		}
		echo json_encode(array($data));
	}
	
	function show()
	{
		$topicid = isset($_GET['topicid']) ? intval($_GET['topicid']) : 0;
		$contentid = isset($_GET['contentid']) ? intval($_GET['contentid']) : 0;
		$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
		if(!$topicid && !$contentid)
		{
			exit("[]");
		}
		$_key = 'mobile_comment_show_'.$contentid.'_'.$topicid.'_'.$page;
		if(!$data = $this->cache->get($_key))
		{
            $db = &factory::db();
			$content = $db->get("SELECT modelid,topicid,comments FROM #table_content WHERE contentid=?",array($contentid));
			$topicid = $content['topicid'];

			$pagesize = $this->setting['comment_pagesize'];
			$this->comment = loader::model('comment', 'comment');
			$commentlist = $this->comment->page($topicid, 2, $page, $pagesize);
			$data = array(
				'contentid'=>$contentid,
				'topicid'=>$topicid,
				'modelid'=>$content['modelid'],
				'total'=>$content['comments'],
				'more'=>max(ceil($content['comments']/$pagesize) - $page,0),
				'data'=>$commentlist
			);
			// 放入缓存
			if($this->setting['cache']) $this->cache->set($_key,$data,$this->setting['cache']);
		}
		echo $this->json->encode(array($data));
	}
	
	function comment()
	{
		$this->comment = loader::model('comment', 'comment');
		$comment_setting = setting('comment');
        $comment_setting['isseccode'] = false;
		$status = $comment_setting['ischeck'] ? 1 : 2;
		if ($commentid = $this->comment->add($_POST['topicid'], $_POST['content'], $_POST['followid'], $status, $comment_setting, $_POST['anonymous']))
		{
			$result = array('state'=>true, 'data'=>$this->comment->get($commentid));
		}
		else
		{
			$result = array('state'=>false, 'error'=>$this->comment->error());
		}
		if(!empty($result['data']))
		{
			$result['data'] = array($result['data']);
		}
		echo json_encode(array($result));
	}
}