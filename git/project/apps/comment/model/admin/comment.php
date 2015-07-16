<?php
class model_admin_comment extends model implements SplSubject 
{
    public $commentids;
	public $keyword, $sensekeyword, $unsafekeyword;
	private $observers = array();

	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'comment';
		$this->_primary = 'commentid';
		$this->_fields = array(
			'commentid', 'topicid', 'followid', 
			'content', 'ip', 'created','createdby',
			'status','supports','reports', 'sensitive','istop'
		);

		$this->_readonly = array('commentid', 'topicid', 'followid');
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array(
			'content'=>array(
						'not_empty'=>array('评论内容不能为空'),
						'max_length' =>array(2000, '评论内容不得超过2000字节')
						)
			);
	}

	/**
	 * 加亮敏感词
	 */
	public function replace_keyword($content)
	{
		$this->sensekeyword = str_replace(array("\r\n","\n","\r"),'|',$this->sensekeyword);
		$replace = '<span style="color:red">'.str_replace('|','</span>|<span style="color:red">',$this->sensekeyword).'</span>';
		return str_replace(explode('|',$this->sensekeyword),explode('|',$replace),$content);
	}

	/**
	 * 根据查询条件 获取数据
	 * 
	 * #table_comment_topic AS ct，#table_comment AS cm
	 * @todo 内容用了 like 性能需要优化
	 */
	public function ls($status = 1, $topicid = null, $url = null, $keyword=null, $ip = null , $createdby = null, $starttime = null, $endtime = null, $order, $page = 1, $pagesize = 20, $setting = null)
	{
		$this->sensekeyword = $setting['sensekeyword'];
		$where = null;
		$status && $where[] = "`cm`.`status`=".intval($status);
		$topicid && $where[] = "`ct`.`topicid`=".intval($topicid);
		$createdby && $where[] = "`cm`.`createdby` = $createdby";
		$url && $where[] = "`ct`.`url_md5`='".md5($url)."'";
		$keyword && $where[] = "`cm`.`content` like '%$keyword%'";
		$ip && $where[] = "`cm`.`ip`='$ip'";
		$starttime && $where[] = where_mintime('`cm`.`created`', $starttime);
		$endtime && $where[] = where_maxtime('`cm`.`created`', $endtime);
		$where && $where = implode(' AND ', $where);
		$order && $order = 'ORDER BY'. $order;

		$sql = "SELECT cm.*, ct.title, ct.url, ct.description, ct.thumb
			FROM #table_comment cm
			LEFT JOIN #table_comment_topic ct ON cm.topicid = ct.topicid
			WHERE $where $order";
		$data	= $this->db->page($sql,$page,$pagesize);
		$sql = "SELECT count(*)
			FROM #table_comment cm
			LEFT JOIN #table_comment_topic ct ON cm.topicid = ct.topicid
			WHERE $where";
		$total	= array_pop(array_pop($this->db->select($sql)));
		$this->output($data, true);
		return array('data'=>$data, 'total'=>$total);
	}

	/**
	 * 敏感评论查询
	 *
	 */
	public function ls_sensitive($commentid, $order, $page = 1, $pagesize = 20)
	{
		$where = null;
		$where[] = "`cm`.`sensitive`=1";
		$commentid && $where[] = "`cm`.`commentid`=".intval($commentid);
		$where && $where = implode(' AND ', $where);

		$sql = "SELECT cm.*, ct.title, ct.url
			FROM #table_comment cm 
			LEFT JOIN #table_comment_topic ct ON cm.topicid = ct.topicid 
			WHERE $where 
			ORDER BY $order ";
		$data = $this->db->page($sql,$page,$pagesize);
		$this->output($data, true);
		return $data;
	}

    /**
     * 通过审核
     *
     */
    public function check($commentid)
    {
        $ids = array();
        $data = $this->db->select("SELECT commentid FROM #table_comment WHERE status=1 AND commentid IN (". (string)$commentid .")");
        foreach($data as $row)
        {
            $ids[] = $row['commentid'];
        }
        if(empty($ids))
        {
            return true;
        }
        $this->commentids = implode(',', $ids);
        $this->commentids = (string)$this->commentids;
        $rs = $this->set_field('status', 2, "commentid IN (". $this->commentids .")");
        $this->event = 'after_check';
        $this->notify();
        return true;
    }

	/**
	 * 评论删除
	 *
	 */
	public function delete($commentid)
	{
		if(empty($commentid))
		{
			$this->error = '所要删除的ID不存在';
			return false;
		}
		$this->commentids = (string)$commentid;
		$this->event = 'before_delete';
		$this->notify();
		return parent::delete($commentid);
	}

	/**
	 * 查询举报 
	 *
	 * 查询条件必须满足：举报数 !=0
	 * @param contentid 为预留
	 * @todo 可以增加查找 功能
	 */
	public function ls_report($contentid = 0, $order, $page=1, $pagesize=5)
	{
		$where = null;
		$contentid && $where[] = '`cm`.`contentid` = '.$contentid;
		$where[] = 'reports != 0';
		$where = implode(' AND ', $where);

		$sql = "SELECT cm.*, ct.title, ct.url
			FROM #table_comment cm
			LEFT JOIN #table_comment_topic ct ON cm.topicid = ct.topicid
			WHERE $where
			ORDER BY $order ";
		$data = $this->db->page($sql,$page,$pagesize);
		$this->output($data, true);
		return $data;
	}

	// 将举报数重置为 0
	public function report_reset($commentid)
	{
		return $this->set_field('reports', 0, $commentid);
	}

	// 将敏感数重置为 0
	public function sensitive_reset($commentid)
	{
		return $this->set_field('sensitive', 0, $commentid);
	}

	// 修改IP
	public function ip_edit($commentid,$ip)
	{
		return $this->set_field('ip', $ip, $commentid);
	}

	// 锁定IP
	public function ip_disallow($ip,$expire)
	{
		return $this->db->query("REPLACE INTO #table_ipbanned (`ip`,`expires`) VALUES ('$ip','$expire')");
	}

	// 删除指定IP所有数据
	public function ip_delete($ip)
	{
		return parent::delete("`ip` = '$ip'");
	}

	/**
	 * 对 get 方法返回数据进行格式化 
	 * 修复了修改 ip 当前显示 undefined 问题
	 * 
	 */
	public function _get($commentid)
	{
		$data = parent::get($commentid);
		$this->output($data, false);
		return $data;
	}

	/**
	 * 对输出数据进行格式化
	 *
	 */
	private function output(& $data, $multiple)
	{
		if (empty($data)) return false;

		import('helper.iplocation');
		$this->iplocation = new iplocation();
		if ($multiple) 
		{
			foreach ($data as $key => $value) 
			{
				$data[$key]['username'] = username($value['createdby']);
				if(!$data[$key]['username']) $data[$key]['username'] = $value['nickname'];
				$data[$key]['created'] = date('Y-m-d H:i:s',$value['created']);
				$data[$key]['location'] = $this->iplocation->get($value['ip']);
				$data[$key]['content'] = $this->replace_keyword(preg_replace('/.*\[reply\]/s','',$value['content']));
				$data[$key]['warn'] = empty($value['count']) ? '' : '<img src="images/warn.png" style="vertical-align:bottom">';
			}
		}
		else
		{
			$data['username'] = username($data['createdby']);
			if(!$data['username']) $data['username'] = $data['nickname'];
			$data['created'] = date('Y-m-d h:i:s',$data['created']);
			$data['location'] = $this->iplocation->get($data['ip']);
			$data['content'] = $this->replace_keyword(preg_replace('/.*\[reply\]/s','',$data['content']));
			$data['warn'] = empty($data['count']) ? '' : '<img src="images/warn.png" style="vertical-align:bottom">';
		}
	}

	/**
	 * 置顶
	 *
	 */
	public function top($commentid)
	{
		return $this->set_field('istop', 1, "`commentid` = $commentid");
	}

	/**
	 * 取消置顶
	 *
	 */
	public function canceltop($commentid)
	{
		return $this->set_field('istop', 0, "`commentid` = $commentid");
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