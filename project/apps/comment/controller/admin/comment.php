<?php
/**
 * 评论管理
 *
 * @aca 评论管理
 */
class controller_admin_comment extends comment_controller_abstract
{
	private $comment, $pagesize;

	function __construct(&$app)
	{
		parent::__construct($app);

		$this->pagesize =  & $this->setting['pagesize'];
		$this->comment = loader::model('admin/comment');
		$this->topic = loader::model('topic');

		$this->comment->sensekeyword = $this->setting['sensekeyword'];
		$this->comment->unsafekeyword = $this->setting['unsafekeyword'];
	}

	/**
     * 评论管理
     *
     * status 0、否决，1、待审，2、已审
     *
     * @aca 浏览
     */
	public function index()
	{
		$status = isset($_GET['status']) ? intval($_GET['status']): 2;
		$orderlist = array(
			'created' => '发布时间',
			'supports' => '支持数',
			'reports' => '举报数'
		);

		$this->view->assign('status', $status);
		$this->view->assign('orderlist', $orderlist);
		$this->view->assign('pagesize', $this->pagesize);
		$this->view->assign('head', array('title'=>'全部评论'));
		$this->view->display("index");
	}

	/**
     * 首页评论加载执行方法
     *
     * @aca 浏览
     */
	function page()
	{
		$page = max((isset($_GET['page']) ? intval($_GET['page']) : 1), 1);
		$size = max((isset($_GET['pagesize']) ? intval($_GET['pagesize']) : $this->pagesize), 1);

		// where开始
		$status = isset($_GET['status']) ? intval($_GET['status']) : 2;
		$starttime = $_GET['published'];
		$endtime = $_GET['unpublished'];

		$orderarr = array('created', 'supports', 'reports');
		$order = ($_GET['oid'] && in_array($_GET['oid'], $orderarr, true)) ? '`'.trim($_GET['oid']).'` DESC' : '`created` DESC';

		switch($_GET['type'])
		{
			case '1':
				$keyword = $_GET['keywords'];
				break;
			case '2':
				$ip = $_GET['keywords'];
				break;
			case '3':
				$createby = userid($_GET['keywords']);
				break;
			default:
				break;
		}

		if($_GET['rwkeyword'])
		{
			if(is_numeric($_GET['rwkeyword']))
			{
				$topicid = intval($_GET['rwkeyword']);
			}
			elseif(preg_match('/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"])*$/i',$_GET['rwkeyword']))
			{
				$url = $_GET['rwkeyword'];
			}
		}

		// where结束
		// $this->seeting 里面包含了后台设置信息
		$data = $this->comment->ls(
			$status, $topicid, $url,
			$keyword, $ip, $createby,
			$starttime, $endtime, $order,
			$page, $size, $this->setting
		);
		echo $this->json->encode($data);
	}

	/**
	 * 评论修改
	 *
	 * @param commentid 评论ID
	 * @param content 评论内容
     * @aca 编辑
	 */
	public function edit()
	{
		$commentid = intval($_POST['commentid']);
		if(empty($commentid)) return false;

		$data = $this->comment->get($commentid, 'content');
		$_POST['content'] = preg_replace(array('/\[/','/\]/'), array('&#91;','&#93;'), htmlspecialchars($_POST['content']));
		if($start = strpos($data['content'],'[reply]'))
		{
			$data['content'] = substr($data['content'], 0, $start+7).$_POST['content'];
		}
		else
		{
			$data['content'] = $_POST['content'];
		}

		$result = $this->comment->update(array('content' => $data['content']), "commentid=".$commentid) 
				? array('state'=>true,'conentet'=>$_POST['content'])
				: array('state'=>false,'error'=>$this->comment->error());
		echo $this->json->encode($result);
	}

	/**
     * 评论通过审核
     *
     * @aca 通过
     */
	public function check()
	{
		$commentid = $_GET['commentid'];
		$result = $this->comment->check($commentid)
				? array('state'=>true)
				: array('state'=>false, 'error'=>$this->comment->error());
		echo $this->json->encode($result);
	}

	/**
     * 评论删除
     *
     * @aca 删除
     */
	public function delete()
	{
		$commentid = $_GET['commentid'];
		$result = $this->comment->delete($commentid)
				? array('state'=>true) 
				: array('state'=>false, 'error'=>$this->comment->error());
		echo $this->json->encode($result);
	}

	/**
     * 修改IP
     *
     * @aca 修改 IP
     */
	public function ip_edit()
	{
		if($this->is_post())
		{
			$commentid = $_POST['commentid'];
			$ip = $_POST['ip'];
			$result = $this->comment->ip_edit($commentid, $ip) 
					? array('state'=>true,'data'=> $this->comment->_get($commentid))
					: array('state'=>false,'error'=>'数据库错误,请稍候重试');
			echo $this->json->encode($result);
		}
		else
		{
			$commentid = $_GET['commentid'];
			$r = $this->comment->get($commentid);
			$this->view->assign('comment', $r);
			$this->view->display("ip_edit");
		}
	}

	/**
     * IP 锁定
     *
     * @aca IP 锁定
     */
	public function ip_disallow()
	{
		$ip = $_GET['ip'];
		$commentid = $_GET['commentid'];
		$expire = TIME + $this->setting['iptime']*3600;
		$result = $this->comment->ip_disallow($ip, $expire)
				? array('state'=>true,'message'=>'锁定成功')
				: array('state'=>false,'error'=>'锁定失败.请稍后重试');
		echo $this->json->encode($result);
	}

	/**
     * 删除指定ip所有评论
     *
     * @aca 删除指定 IP 所有评论
     */
	public function ip_delete()
	{
		$ip = $_GET['ip'];
		$result = $count = $this->comment->ip_delete($ip)
				? array('state'=>true)
				: array('state'=>false,'error'=>"删除失败.请稍后重试");
		echo $this->json->encode($result);
	}

	/**
     * 评论置顶
     *
     * @aca 评论置顶
     */
	public function top()
	{
		$commentid = $_GET['commentid'];
		$result = $this->comment->top($commentid)
				? array('state'=>true, 'data' => '置顶成功')
				: array('state'=>false, 'error'=> $this->comment->error());
		echo $this->json->encode($result);
	}

	/**
     * 取消置顶
     *
     * @aca 取消置顶
     */
	public function canceltop()
	{
		$commentid = $_GET['commentid'];
		$result = $this->comment->canceltop($commentid)
				? array('state'=>true, 'data' => '操作成功')
				: array('state'=>false, 'error'=> $this->comment->error());
		echo $this->json->encode($result);
	}

	/**
     * 话题管理
     *
     * @aca 话题管理
     */
	public function topic()
	{
		$this->view->assign('head', array('title'=>'话题管理'));
		$this->view->display("topic/index");
	}

    /**
     * 话题分页
     *
     * @aca 话题管理
     */
	public function topic_page()
	{
		if (isset($_GET['keywords']) && $_GET['keywords']) $where = where_keywords('title', $_GET['keywords']);

		// 默认时间排序
		$order = '`created` DESC';
		$page = max((isset($_GET['page']) ? intval($_GET['page']) : 1), 1);
		$size = max((isset($_GET['pagesize']) ? intval($_GET['pagesize']) : $this->pagesize), 1);

		$data = $this->topic->page($where, $order, $page, $size);
		$total = $this->topic->count();

		$result = array('total'=>$total, 'data'=>$data);
		echo $this->json->encode($result);
	}

	/**
     * 话题添加、修改
     *
     * @aca 添加话题
     */
	public function topic_add()
	{
    	if ($this->is_post())
        {
			// 话题修改在模板里面有一个隐藏的 tid，
			if(!$_POST['tid']) {
				$id = $this->topic->add(
					$_POST['title'],
					$_POST['url'],
					$_POST['description'],
					$_POST['thumb'],
					$_POST['disabled']
				);
			}
			else
			{
				$id = $this->topic->edit(
					$_POST['tid'],
					$_POST['title'],
					$_POST['url'],
					$_POST['description'],
					$_POST['thumb'],
					$_POST['disabled']
				);
				// 判断update 是否成功
				if($id) $id = $_POST['tid'];
			}

			if ($id)
			{
				$json = array(
					'state'=>true, 
					'data'=>$this->topic->_get($id)	// _get 返回 格式化后的 get 数据
				);
			}
			else
			{
				$json = array(
					'state'=>false, 
					'error'=>$this->topic->error()
				);
			}
			exit($this->json->encode($json));
        }

		$tid = intval($_GET['tid']);
		// 获取需要修改的数据
        if($topic = $this->topic->get($tid))
		{
			$this->view->assign('topic', $topic);
		}

        $this->view->display('topic/form');
	}

	/**
     * 话题开启
     *
     * @aca 话题开启
     */
	public function topic_enable()
	{
		$tid = $_GET['id'];
		$result = $this->topic->_enable($tid)
				? array('state'=>true)
				: array('state'=>false,'error'=>$this->topic->error());
		echo $this->json->encode($result);
	}

	/**
     * 话题关闭
     *
     * @aca 话题关闭
     */
	public function topic_disable()
	{
		$tid = $_GET['id'];
		$result = $this->topic->_disable($tid)
				? array('state'=>true)
				: array('state'=>false,'error'=>$this->topic->error());
		echo $this->json->encode($result);
	}

	/**
     * 删除话题
     *
     * @aca 删除话题
     */
	public function topic_del()
	{
		$tid = $_GET['id'];
		$result = $this->topic->_delete($tid)
					? array('state'=>true) 
					: array('state'=>false,'error'=>$this->topic->error());
		echo $this->json->encode($result);
	}

	/**
     * 举报评论管理
     *
     * @aca 举报评论管理
     */
	public function report()
	{
		$this->view->assign('pagesize', $this->pagesize);
		$this->view->assign('head', array('title'=>'举报评论'));
		$this->view->display('report');
	}

	/**
     * 举报评论首页加载
     *
     * @aca 举报评论管理
     */
	public function report_page()
	{
		$page = max((isset($_GET['page']) ? intval($_GET['page']) : 1), 1);
		$size = max((isset($_GET['pagesize']) ? intval($_GET['pagesize']) : $this->pagesize), 1);

		$order =($_GET['type'] == 'report' ) ? '`cm`.`reports` DESC' : '`cm`.`created` DESC';

		$conetentid = intval($_GET['contentid']);
		$data = $this->comment->ls_report($conetentid, $order, $page, $size);
		$total = $this->comment->count('`reports`!=0');
		echo $this->json->encode(array('data' =>$data,'total' => $total));
	}

	/**
     * 举报重置
     *
     * @aca 举报重置
     */
	public function report_reset()
	{
		$commentid = intval($_GET['commentid']);
		$result = $this->comment->report_reset($commentid)
				? array('state'=>true)
				: array('state'=>false,'error'=>$this->comment->error);
		echo $this->json->encode($result);
	}

	/**
     * 敏感评论
     *
     * @aca 敏感评论
     */
	public function sensitive()
	{
		$this->view->assign('pagesize', $this->pagesize);
		$this->view->assign('head', array('title'=>'敏感评论'));
		$this->view->display('sensitive');
	}

    /**
     * 敏感评论列表
     *
     * @aca 敏感评论
     */
	public function sensitive_page()
	{
		$page = max((isset($_GET['page']) ? intval($_GET['page']) : 1), 1);
		$size = max((isset($_GET['pagesize']) ? intval($_GET['pagesize']) : $this->pagesize), 1);

		$order = '`cm`.`created` DESC';

		$conetentid = intval($_GET['contentid']);
		$data = $this->comment->ls_sensitive($conetentid, $order, $page, $size);
		$total = $this->comment->count('`sensitive`=1');
		echo $this->json->encode(array('data' =>$data,'total' => $total));
	}

	/**
     * 敏感重置
     *
     * @aca 敏感重置
     */
	public function sensitive_reset()
	{
		$commentid = intval($_GET['commentid']);
		$result = $this->comment->sensitive_reset($commentid)
				? array('state'=>true)
				: array('state'=>false,'error'=>$this->comment->error);
		echo $this->json->encode($result);
	}
}