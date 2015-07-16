<?php
//投稿
class model_contribution extends model implements SplSubject
{
	private $observers = array();
	public $contributionid,$action,$note,$status,$condition;
	
	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'contribution';
		$this->_primary = 'contributionid';
		$this->_fields = array('contributionid', 'title', 'content', 'description', 'tags', 'author', 'sourcetype', 'sourcename','sourceurl','created','createdby','catid','contentid','status','creator','email','isnotice');
		$this->_readonly = array('contributionid');
		$this->_create_autofill = array('created' => TIME, 'createdby' => $this->_userid);
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array(
			'title'=>array(
				'not_empty'=>array('标题不能为空'),
				'max_length' =>array(80, '标题不得超过80字节')
			),
			'catid'=> array(
				'not_empty'=>array('请选择栏目'),
				'max_length'=>array(5, '栏目ID不得超过5字节'),
				'is_numeric'=>array('栏目ID格式不正确')
			),
			'content'=>array(
				'not_empty'=>array('内容不能为空')
			),
			'email'=>array(
				'email' => array('E-mail格式不正确')
			),
		);
	}
	
	function add($data)
	{
		if($this->_userid)
		{
			$m = loader::model('member', 'member')->get($this->_userid);
			$data['email'] = $m['email'];
			$data['creator'] = $m['username'];
		}
		$this->data = $data;
		$this->event = 'before_add';
		$this->notify();
		//投递过来的字段
		$filter = array('title', 'content', 'description', 'tags', 'author', 'sourcetype', 'sourcename', 'sourceurl', 'catid', 'status', 'creator', 'email', 'isnotice');
		$this->data = $this->filter_array($this->data, $filter);
		$this->contributionid = $this->insert($this->data);
		if(!$this->contributionid)
		{
			return false;
		}
		$this->event = 'after_add';
		$this->notify();
		return $this->contributionid;
	}
	
	function ls($where, $fields, $order, $page, $pagesize)
	{
		$condition = array();
		switch ($where['date'])
		{
			case 'today':
				$where['created_min'] = date('Y-m-d H:i:s', strtotime('today'));
				break;
			case 'yesterday':
				$where['created_min'] = date('Y-m-d H:i:s', strtotime('yesterday'));
				$where['created_max'] = date('Y-m-d H:i:s', strtotime('today'));
				break;
			case 'week':
				$where['created_min'] = date('Y-m-d H:i:s', strtotime('last week'));
				break;
			case 'month':
				$where['created_min'] = date('Y-m-d H:i:s', strtotime('last month'));
				break;
			default:
				break;
		}
		if(!empty($where['catid']))
		{
			$condition[] =  table('category',$where['catid'],'childids') ? " `catid` IN (".table('category',$where['catid'],'childids').")" : " `catid`=".$where['catid'];
		}
		if(isset($where['createdbyname']) && !empty($where['createdbyname']))
		{
			$condition[] = where_keywords('creator', $where['createdbyname']);
		}
		if(isset($where['keywords']) && !empty($where['keywords'])) $condition[] = where_keywords('title', $where['keywords']);
		if(isset($where['status'])) $condition[] = '`status` ='.$where['status'];
		if(isset($where['createdby'])) $condition[] = '`createdby` ='.$where['createdby'];
		if (isset($where['created_min']) && $where['created_min']) $condition[] = where_mintime('created', $where['created_min']);
		if (isset($where['created_max']) && $where['created_max']) $condition[] = where_maxtime('created', $where['created_max']);
		$this->condition = implode(' AND ',$condition);
		$result = $this->page($this->condition,$fields,$order,$page,$pagesize);
		$this->total = intval($this->count($this->condition));
		return $result;
	}
	
	function get_total()
	{
		return $this->total;
	}
	
	function get_publish($where, $fields, $order, $page, $pagesize)
	{
		$condition = array();
		$orderby = isset($_GET['orderby']) ? trim($_GET['orderby']) : '';
		switch ($where['date'])
		{
			case 'today':
				$where['created_min'] = date('Y-m-d H:i:s', strtotime('today'));
				break;
			case 'yesterday':
				$where['created_min'] = date('Y-m-d H:i:s', strtotime('yesterday'));
				$where['created_max'] = date('Y-m-d H:i:s', strtotime('today'));
				break;
			case 'week':
				$where['created_min'] = date('Y-m-d H:i:s', strtotime('last week'));
				break;
			case 'month':
				$where['created_min'] = date('Y-m-d H:i:s', strtotime('last month'));
				break;
		}
		if(!empty($where['catid']))
		{
			$condition[] =  table('category',$where['catid'],'childids') ? " f.`catid` IN (".table('category',$where['catid'],'childids').")" : " f.`catid`=".$where['catid'];
		}
		if(isset($where['keywords']) && !empty($where['keywords'])) $condition[] = where_keywords('f.`title`', $where['keywords']);
		if(isset($where['status'])) $condition[] = 'f.`status` ='.$where['status'];
		if(isset($where['createdby'])) $condition[] = 'f.`createdby` ='.$where['createdby'];
		if (isset($where['created_min']) && $where['created_min']) $condition[] = where_mintime('f.created', $where['created_min']);
		if (isset($where['created_max']) && $where['created_max']) $condition[] = where_maxtime('f.created', $where['created_max']);
		$condition = implode(' AND ',$condition);
		
		$sql = "SELECT s.*,f.contributionid FROM #table_contribution f 
			LEFT JOIN #table_content s 
			ON f.contentid=s.contentid 
			WHERE {$condition}
			";
		if(strpos($orderby,'|'))
		{
			list($order,$by) = explode('|',$orderby);
			$orderby = "s." .$order ." " .$by;
		}else{
			$orderby = "s.contentid DESC";
		}
		$sql .= " ORDER BY " .$orderby;
		$data = $this->db->page($sql,$page,$pagesize);
		//格式化输出内容信息
		foreach($data as $k => $v)
		{
			$v['caturl'] = table('category',$v['catid'],'url');
			$v['catname'] = table('category',$v['catid'],'name');
			$v['publishedbyname'] = username($v['publishedby']);
			$v['published'] = date('Y-m-d H:i',$v['published']);
			$data[$k] = $v;
		}
		//获取统计
		$sql = "SELECT COUNT(*) AS count FROM #table_contribution f 
			LEFT JOIN #table_content s 
			ON f.contentid=s.contentid 
			WHERE {$condition}
			";
		$r = $this->db->get($sql);
		$this->total = $r ? $r['count'] : 0;
		return $data;
	}
	
	function edit($contributionid, $data)
	{
		$this->data = $data;
		$this->event = 'before_edit';
		$this->notify();
		$filter = array('title', 'content', 'description', 'tags', 'author', 'sourcetype', 'sourcename', 'sourceurl', 'catid', 'status', 'creator', 'email', 'isnotice');
		$this->data = $this->filter_array($this->data, $filter);
		$result = $this->update($this->data, $contributionid);
		$this->event = 'after_edit';
		$this->notify();
		return $result;
	}
	
	function del($contributionid)
	{
		$this->contributionid = $contributionid;
		$this->status = 0;
		$this->note = '';
		parent::delete($contributionid);
		$this->event = 'after_delete';
		$this->notify();
		return true;
	}
	
	function submit($contributionid)
	{
		$this->contributionid = $contributionid;
		$this->status = 3;
		$this->note = '';
		$this->set_field('status',$this->status,$contributionid);
		$this->event = 'after_submit';
		$this->notify();
		return true;
	}
	
	function remove($contributionid)
	{
		$this->contributionid = $contributionid;
		$this->status = 0;
		$this->note = '';
		$this->set_field('status',$this->status,$contributionid);
		$this->event = 'after_remove';
		$this->notify();
		return true;
	}
	
	function reject($contributionid)
	{
		$this->contributionid = $contributionid;
		$this->status = 2;
		$this->note = '';
		$this->set_field('status',$this->status,$contributionid);
		$this->event = 'after_reject';
		$this->notify();
		return true;
	}
	
	function cancel($contributionid)
	{
		$this->contributionid = $contributionid;
		$this->status = 1;
		$this->note = '';
		$this->set_field('status',$this->status,$contributionid);
		$this->event = 'after_cancel';
		$this->notify();
		return true;
	}
	
	function publish($contributionid,$contentid, $status = 6)
	{
		//需要发布到content表 加载article模型发布内容
		$this->contributionid = $contributionid;
		$this->status = $status;
		$this->note = '';
		$data = array(
			'status' => $this->status,
			'contentid' => $contentid,
		);
		if($status != 6)
		{
			$data['status'] = 3;
		}
		
		$this->update($data, $contributionid);
		$this->event = 'after_publish';
		$this->notify();
	}
	
	function get_count()
	{
		$result = array(
			'total' => $this->count(),
			'wait' => $this->count("`status` = '3'"),
			'publish' => $this->count("`status` = '6'"),
			'reject' => $this->count("`status` = '2'"),
			'draft' => $this->count("`status` = '1'"),
			'remove' => $this->count("`status` = '0'")
		);
		return $result;
	}
	
	function get_person_count($userid)
	{
		$result = array(
			'total' => $this->count(array('createdby' => $userid)),
			'wait' => $this->count(array('createdby' => $userid,'status' => 3)),
			'publish' => $this->count(array('createdby' => $userid,'status' => 6)),
			'reject' => $this->count(array('createdby' => $userid,'status' => 2)),
			'draft' => $this->count(array('createdby' => $userid,'status' => 1)),
			'remove' => $this->count(array('createdby' => $userid,'status' => 0))
		);
		return $result;
	}
	function _after_select(&$data,$multi = true)
	{
		$this->log = loader::model('contribution_log', 'contribution');
		if($multi)
		{
			foreach($data as $k => $v)
			{
				$data[$k]['created'] = date('Y-m-d H:i',$v['created']);
				$data[$k]['caturl'] = table('category',$v['catid'],'url');
				$data[$k]['catname'] = table('category',$v['catid'],'name');
				$data[$k]['creator'] = !empty($v['creator'])?$v['creator']:'';
				$data[$k]['isget'] = !empty($v['contentid'])?1:0;
				if($v['status'] == 2)
				{
					$r = $this->log->select(array('contributionid' =>$v['contributionid'],'status' => 2),'*','`created` DESC',1);
					$r = $r[0];
					$data[$k]['rejected'] = empty($r)?'':date('Y-m-d H:i',$r['created']);
					$data[$k]['rejectedbyname'] = empty($r)?'':username($r['createdby']);
				}
			}
			return $data;
		}
		else
		{
			$data['created'] = date('Y-m-d H:i',$data['created']);
			$data['caturl'] = table('category',$data['catid'],'url');
			$data['catname'] = table('category',$data['catid'],'name');
			$data['creator'] = !empty($data['creator'])?$data['creator']:'';
			$data['isget']  = !empty($data['contentid'])?1:0;
			if($data['status'] == 2)
			{
				$r = $this->log->select(array('contributionid' =>$data['contributionid'],'action' => 2),'*','`created` DESC',1);
				$r = $r[0];
				$data['rejected'] = empty($r)?'':date('Y-m-d H:i',$r['created']);
				$data['rejectedbyname'] = empty($r)?'':username($r['rejectedby']);
			}
		}
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