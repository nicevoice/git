<?php

class model_comment extends model implements SplSubject
{
    public $data = array(), $event, $topicid, $commentid, $setting = array(), $floorary = array(), $topic;

    private $observers = array();

    function __construct()
    {
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'comment';
		$this->_primary = 'commentid';
		$this->_fields = array('commentid', 'topicid', 'followid', 'content', 'ip', 'created', 'createdby', 'status', 'supports', 'reports', 'sensitive', 'istop', 'anonymous');
		$this->_readonly = array('commentid', 'followid', 'ip', 'created', 'createdby');
		$this->_create_autofill = array('ip'=>IP, 'created'=>TIME, 'createdby'=>$this->_userid);
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array(
		'topicid' => array('integer' => array('评论话题ID必须是整数'),
		                 ),
		'followid' => array('integer' => array('回复评论ID必须是整数'),
		               ),
		'content' => array('not_empty' => array('评论内容不能为空'),
		                   'max_length' => array(10000, '评论内容不得超过10000字节'),
		                 ),
        );

		$this->topic = loader::model('topic', 'comment');
    }

    //发表评论
    public function add($topicid, $content, $followid = null, $status = 1, $setting = null, $anonymous = 0)
    {
    	$this->topicid = intval($topicid);
		if(empty($setting))
		{
			$setting = setting('comment');
		}
		$this->setting = $setting;
    	$r = table('comment_topic', $this->topicid);
		// 验证码
		if ($setting['isseccode'])
		{
			import('helper.seccode');
			$seccode = new seccode();
			if (!$seccode->valid(true))
			{
				$this->error = '验证码不正确';
    			return false;
			}
		}
    	if (!$r)
    	{
    		$this->error = '您所评论的话题不存在';
    		return false;
    	}
		if (is_null($this->_userid) && $setting['islogin'])
		{
			$this->error = '请登陆后留言';
    		return false;
		}
    	if ($r['disabled'])
    	{
    		$this->error = '您所评论的话题已关闭评论';
    		return false;
    	}
    	$followid = $followid ? intval($followid) : null;
    	if ($followid && !parent::get($followid))
    	{
    		$this->error = '您所回复的评论不存在';
    		return false;
    	}

		if($this->_check_empty($content))
		{
    		$this->error = '评论内容不能为空';
    		return false;
		}
		// 过滤掉评论中的HTML代码，只允许纯文本
		$content = strip_tags($content);

		if($this->_find_unsafekeyword($content))
		{
    		$this->error = '和谐社会，文明用语';
    		return false;
		}

		$user_time = $setting['timeinterval'] ? $this->_get_user_time($this->topicid) : null;
		if($user_time && ((TIME - $user_time) <= $setting['timeinterval'])) {
			$this->error = '您的语速过快，请 '.$setting['timeinterval'].' 秒后回复';
			return false;
		}
		if(words_count($content) > $setting['wordage'])
		{
    		$this->error = '请将评论字数限制在'.$setting['wordage'].'个字符以内';
    		return false;
		}
    	$status = in_array($status, array(1, 2)) ? $status : 1;

		// 如果出现敏感词 则设置为待审 并将 sensitive 设置为1
		$sensitive = 0;
		if($this->_find_sensekeyword($content))
		{
			$status = 1;
			$sensitive = 1;
		}

		$anonymous == 1 || $anonymous = 0;

    	$this->data = array('topicid'=>$this->topicid, 'followid'=>$followid, 'content'=>$content, 'status'=>$status, 'sensitive'=>$sensitive, 'anonymous'=>$anonymous);
    	$this->event = 'before_add';
    	$this->notify();
    	if ($this->commentid = $this->insert($this->data))
    	{
	    	$this->event = 'after_add';
	    	$this->notify();
    	}
    	return $this->commentid;
    }

    //修改评论
    public function edit($commentid, $content)
    {
    	$this->commentid = intval($commentid);
    	$this->data = array('content'=>$content);
    	$this->event = 'before_edit';
    	$this->notify();
    	if ($this->result = $this->update($this->data, $this->commentid))
    	{
	    	$this->event = 'after_edit';
	    	$this->notify();
    	}
    	return $this->result;
    }

    //删除评论
    public function delete($commentid)
    {
        $this->commentid = intval($commentid);
    	$this->event = 'before_delete';
    	$this->notify();
    	if ($this->result = parent::delete($this->commentid))
    	{
	    	$this->event = 'after_delete';
	    	$this->notify();
    	}
    	return $this->result;
    }

    //获取评论
    public function get($commentid)
    {
    	$this->commentid = intval($commentid);
    	$this->event = 'before_get';
    	$this->notify();
    	if ($this->result = parent::get($this->commentid))
    	{
	    	$this->event = 'after_get';
	    	$this->notify();
    	}
    	return $this->result;
    }

    public function page($topicid, $status = 2, $page = 1, $pagesize = 10, $order = 'created DESC')
    {
    	$this->topicid = intval($topicid);
    	$this->status = intval($status);
    	$this->event = 'before_ls';
    	$this->notify();
    	if ($this->data = parent::page("topicid=:topicid AND status=:status", '*', $order, $page, $pagesize, array('topicid'=>$this->topicid, 'status'=>$this->status)))
    	{
	    	$this->event = 'after_ls';
	    	$this->notify();
    	}
    	return $this->data;
    }

	/**
	 * 文章页面下方的评论 
	 *
	 */
	public function ls_article_comment($topicid, $status = 2, $page = 1, $pagesize = 10)
	{
		$where = "topicid=:topicid AND status=:status";
		$fields = '*';
		$order = '`commentid` DESC';
		// $order = '`istop` DESC, `supports` DESC';
		$data = array(
			'topicid'=>$topicid,
			'status'=>$status
		);
		$data = parent::page($where, $fields, $order, $page, $pagesize, $data);
		return $data;
	}

    //查询热门评论
    public function ls_hot($topicid, $page = 1, $pagesize = 20)
    {
    	return parent::page("topicid=$topicid", '*', '`supports` DESC', $page, $pagesize);
    }

    //查询最新评论话题
    public function ls_last($topicid, $page = 1, $pagesize = 20)
    {
    	return parent::page("topicid=$topicid", '*', '`created` DESC', $page, $pagesize);
    }

    //评论审核通过
    public function pass($commentid)
    {
    	$this->commentid = intval($commentid);
    	$this->event = 'before_pass';
    	$this->notify();
    	if ($this->result = $this->update(array('status'=>2), $this->commentid))
    	{
	    	$this->event = 'after_pass';
	    	$this->notify();
    	}
    	return $this->result;
    }

    //评论审核否决
    public function reject($commentid)
    {
    	$this->commentid = intval($commentid);
    	$this->event = 'before_reject';
    	$this->notify();
    	if ($this->result = $this->update(array('status'=>0), $this->commentid))
    	{
	    	$this->event = 'after_reject';
	    	$this->notify();
    	}
    	return $this->result;
    }

    //评论置顶
    public function top($commentid, $istop = 1)
    {
    	$commentid = intval($commentid);
    	$istop = intval($istop) ? 1 : 0;
    	return $this->update(array('istop'=>$istop), $commentid);
    }

    public function supports($commentid)
    {
    	$commentid = intval($commentid);
    	return $this->set_inc('supports', $commentid);
    }

    public function reports($commentid)
    {
    	$commentid = intval($commentid);
    	return $this->set_inc('reports', $commentid);
    }

	function _after_select(& $data, $multiple = false)
	{
		import('helper.iplocation');
		$this->iplocation = new iplocation();
		
		if ($multiple)
		{
			$data = array_map(array($this, 'output'), $data);
            return $data;
		}
		else 
		{
			$data = $this->output($data);
			return $data;
		}
	}

	//2011-04-17 添加 楼层号替换 标识 ，添加 ||| 为自定义分隔符
	public function floor($followid)
	{
		if (!$followid) return false;
		$row = table('comment', $followid);
		if (!$row) return false;
		$row['location'] = $this->iplocation->get($row['ip']);
		$row['nickname'] = $row['createdby'] ? username($row['createdby']) : '匿名';
		$floor = '<div class="citation">';
        if ($row['followid']) $floor .= $this->floor($row['followid']);
		$floor .= '<div class="citation-title">'.$row['location'].'网友 ['.$row['nickname'].'] 的原贴：<span class=" f-r">floorno</span></div>'.$row['content'];
		$floor .= '</div>';
		$floor .= '|||';
		return $floor;
	}

	// 2011-04-17 楼层输出时添加了楼层号
	public function output(& $r)
	{
		if (!$r) return ;
		$r['location'] = $this->iplocation->get($r['ip']);
		$r['date'] = time_format($r['created'], 'Y年n月j日 G:i');
		$r['nickname'] = $r['createdby'] ? username($r['createdby']) : '游客';
		if ($r['anonymous'])
		{
			$r['nickname'] = '匿名网友';
		}
		if ($r['followid'])
		{
			$r['content'] = $this->_add_floorno($this->floor($r['followid'])).$r['content'];
		}
		return $r;
	}

	/**
	 * 解析回复数据 添加楼层号（即键值）
	 * 
	 * 预留接口 after_addfloorno，可用数据: print_r($this->floorary)
	 * @param 回复数据
	 * @return 加了楼层号的数据
	 */
	private function _add_floorno($floor)
	{
		$floorary = array();
		$floors = explode('|||', $floor);
		foreach($floors as $floorno => $f)
		{
			$floorary[] = str_replace('floorno', $floorno+1, $f);
		}

		$this->floorary = $floorary;

		$this->event = 'after_addfloorno';
	    $this->notify();

		return implode("", $this->floorary);
	}

	/**
	 * 获取用户最后一次回复主题时间
	 *
	 * @param $topicid
	 * @param ip 可选 默认为客户端IP
	 * @return int
	 */
	private function _get_user_time($topicid, $ip = IP)
	{
		return parent::get_field('MAX(created)', "topicid='$topicid' AND ip='$ip'");
	}

	/**
	 * 检测是否包含敏感键字
	 *
	 * @param $content 要检测的内容
	 * @return boolen
	 */
	private function _find_sensekeyword($content)
	{
		$keyword = array_map('preg_quote',array_filter(split("\n|\r|\r\n",$this->setting['sensekeyword'])));
		if(empty($keyword)) return false;
		return preg_match("/".implode('|', $keyword)."/", $content) ? true : false;
	}

	/**
	 * 检测是否包含非法字符
	 *
	 * @param $content 要检测的内容
	 * @return boolen
	 */
	private function _find_unsafekeyword($content)
	{
		$keyword = array_map('preg_quote',array_filter(split("\n|\r|\r\n", $this->setting['unsafekeyword'])));
		return !empty($this->setting['unsafekeyword']) && preg_match("/".implode('|', $keyword)."/", $content) 
				? true 
				: false;
	}

	private function _check_empty($content)
	{
		return (trim($content) == '');
	}

	public function attach(SplObserver $observer)
	{
		$this->observers[] = $observer;
	}

	public function detach(SplObserver $observer)
	{
		if($index = array_search($observer, $this->observers, true)) unset($this->observers[$index]);
	}

	public function notify()
	{
		foreach ($this->observers as $observer)
		{
			$observer->update($this);
		}
	}
}