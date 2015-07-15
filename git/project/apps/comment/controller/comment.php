<?php
class controller_comment extends comment_controller_abstract  
{
	private $comment, $topic, $pagesize, $total;

	function __construct(&$app)
	{
		parent::__construct($app);
		$this->pagesize =  & $this->setting['pagesize'];
		$this->topic = loader::model('topic');
		$this->comment = loader::model('comment');

		$this->status = $this->setting['ischeck'] ? 1 : 2;
		$this->timeinterval = $this->setting['timeinterval'];
	}

	function index()
	{
		$topicid = intval($_GET['topicid']);
		if (!$topicid) $this->showmessage('非法主题ID！');

        if ($this->system['pagecached'])
        {
            $keyid = md5('pagecached_comment_comment_index_' .$topicid);
            cmstop::cache_start($this->system['pagecachettl'], $keyid);
        }

		$topic = $this->topic->get($topicid);
		if (!$topic) $this->showmessage('评论主题不存在！');
		if ($topic['disabled']) $this->showmessage('该主题已关闭评论！');

		$this->template->assign('total', $topic['comments']);							//总共多少数据
		$this->template->assign('pagesize', $this->setting['pagesize']);				//分页数 每页多少条
		$this->template->assign('islogin', $this->setting['islogin']);					//是否需要登录才能评论
		$this->template->assign('ischeck', $this->setting['ischeck']);					//评论是否要审核
		$this->template->assign('isseccode', $this->setting['isseccode']);				//是否开启验证码
		$this->template->assign($topic);
		$this->template->display('comment/index.html');

        if ($this->system['pagecached']) cmstop::cache_end();
	}

	/**
	 * 文章页下方的评论入口
	 * 
	 * @todo 可修改为无刷新提交方式
	 */
	public function add()
	{
		if ($commentid = $this->comment->add($_POST['topicid'], $_POST['content'], $_POST['followid'], $this->status, $this->setting, $_POST['anonymous']))
		{
			$msg = ($this->status == 2) ? '评论发表成功' : '评论发送成功，请等待管理员审核！';
			$this->showmessage($msg , "?app=comment&controller=comment&action=index&topicid=".$_POST['topicid'], 3000, true);
		}
		else
		{
			$this->showmessage($this->comment->error());
		}
	}

	/**
	 * 话题评论页面 评论入口 
	 */
	public function comment()
	{
		if ($commentid = $this->comment->add($_POST['topicid'], $_POST['content'], $_POST['followid'], $this->status, $this->setting, $_POST['anonymous']))
		{
			$result = array('state'=>true, 'data'=>$this->comment->get($commentid));
		}
		else
			$result = array('state'=>false, 'error'=>$this->comment->error());
		echo $this->json->encode($result); 
	}

	/**
	 * 评论页面 评论回复、盖楼
	 */
	public function reply()
	{
		if ($commentid = $this->comment->add($_POST['topicid'], $_POST['content'], $_POST['followid'], $this->status, $this->setting, $_POST['anonymous']))
		{
			$data = $this->comment->get($commentid);
			$result = array('state'=>true, 'data'=>$data);
		}
		else 
			$result = array('state'=>false, 'error'=>$this->comment->error());
		echo $this->json->encode($result);
	}
	
	public function specialReply()
	{
		if ($commentid = $this->comment->add($_GET['topicid'], $_GET['content'], $_GET['followid'], $this->status, $this->setting, $_POST['anonymous']))
		{
			$data = $this->comment->get($commentid);
			$result = array('state'=>true, 'data'=>$data);
		}
		else 
			$result = array('state'=>false, 'error'=>$this->comment->error());
		$json = $this->json->encode($result);
		echo "{$_GET['jsoncallback']}($json);";
	}

	/**
	 * 评论页面数据加载
	 */
	public function page()
	{
		$topicid = intval($_GET['topicid']);
		$page = intval($_GET['page']);
		if(empty($page)) $page = 1;
		$pagesize = intval($_GET['pagesize']);

        if ($this->system['pagecached'])
        {
            $keyid = md5('pagecached_comment_comment_page_' .$topicid.'_'.$page.'_'.$pagesize);
            cmstop::cache_start($this->system['pagecachettl'], $keyid);
        }

		$hotdata = array();
		if($page == 1)
		{
			$hotdata = $this->comment->page($topicid, 2, $page, $this->setting['hotcomment'], 'supports DESC, created DESC');
		}
		$data = $this->comment->page($topicid, 2, $page, $pagesize);
		echo $this->json->encode(array('data'=>$data, 'hotdata'=>$hotdata));

        if ($this->system['pagecached']) cmstop::cache_end();
	}

	/**
	 * 文章页下方数据加载
	 */
	public function get()
	{
		$topicid = intval($_GET['topicid']);
		$page = intval($_GET['page']);
		if(empty($page)) $page = 1;
		$pagesize = intval($_GET['pagesize']);
		if(empty($pagesize)) $pagesize = 5;

        if ($this->system['pagecached'])
        {
            $keyid = md5('pagecached_comment_comment_get_' .$topicid.'_'.$page.'_'.$pagesize);
            cmstop::cache_start($this->system['pagecachettl'], $keyid);
        }

		$data = $this->comment->ls_article_comment($topicid, 2, $page, $pagesize);
		$comments = (int) $this->comment->count("`status`=2 AND topicid='".$topicid."'");

		$result =  $this->json->encode(array('data'=>$data, 'total'=>$comments));
		echo $_GET['jsoncallback']."($result);";

        if ($this->system['pagecached']) cmstop::cache_end();
	}

	/**
	 * 评论支持
	 */
	public function support()
	{
		$support = loader::model('support');
		$commentid = intval($_GET['commentid']);
		$result = $support->add($commentid, $this->timeinterval)
				? array('state'=>true, 'supports'=>table('comment', $commentid, 'supports'))
				: array('state'=>false, 'error'=>$support->error());
		echo $this->json->encode($result);
	}

	/**
	 * 举报
	 */
	public function report()
	{
		$report = loader::model('report');
		$commentid = intval($_GET['commentid']);
		$result = $report->add($commentid, $this->timeinterval)
				? array('state'=>true)
				: array('state'=>false, 'error'=>$report->error());
		echo $this->json->encode($result);
	}

}
