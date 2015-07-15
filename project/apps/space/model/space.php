<?php

class model_space extends model implements SplSubject
{
	public $statuss = array(0=>'已禁用',1=>'审核中', 2=>'未批准',3 =>'已开通', 4=>'已推荐');
	public $spaceid, $status;
	private $observers = array();
	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'space';
		$this->_primary = 'spaceid';
		$this->_fields = array('spaceid', 'name', 'author', 'initial', 'alias', 'photo','description', 'userid', 'created', 'createdby', 'modified', 'modifiedby', 'status', 'sort', 'iseditor','posts', 'comments', 'pv');
		$this->_txtfields = array();
		$this->_readonly = array('spaceid', 'created');
		$this->_create_autofill = array('created'=>TIME,'modifiedby' => $this->_userid,'modified'=>TIME);
		$this->_update_autofill = array('modified'=>TIME, 'modifiedby' => $this->_userid);
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();
		import('helper.pinyin');
	}
	
	function add($data)
	{
		if(isset($data['alias']))
		{
			if(preg_match('/[^a-zA-Z_0-9]/',$data['alias']))
			{
				$this->error = 'URL地址不正确';
				return false;
			}
		}
		
		if($this->get_by('author',$data['author']))
		{
			$this->error = '作者名已经存在';
			return false;
		}
		
		if($this->get_by('alias',$data['alias']))
		{
			$this->error = '个性URL地址已经存在';
			return false;
		}
		
		if(isset($data['username']) && $data['username'])
		{
			$data['userid'] = userid($data['username']);
			if(!$data['userid'])
			{
				$this->error = '不存在的关联用户';
				return false;
			}
			if($this->get_by('userid',$data['userid']))
			{
				$this->error = '已经关联的用户';
				return false;
			}
		}
		$data['initial'] = $this->_getIntial($data['author']);
		if (!isset($data['status'])) $data['status'] = 1;
		if (!isset($data['iseditor'])) $data['iseditor'] = 0;
		
		$data = $this->filter_array($data, array('userid','name','author','initial','alias','photo','description','status','sort','iseditor'));
		return $this->insert($data);
	}
	
	function edit($data,$spaceid)
	{
		if(isset($data['alias']))
		{
			if(preg_match('/[^a-zA-Z_0-9]/',$data['alias']))
			{
				$this->error = 'URL地址不正确';
				return false;
			}
		}
		
		$space = $this->get_by('alias',$data['alias']);
		if($space && ($space['spaceid']!= $spaceid))
		{
			$this->error = '个性URL地址已经存在';
			return false;
		}
		
		$space = $this->get_by('author',$data['author']);
		if($space && ($space['spaceid']!= $spaceid))
		{
			$this->error = '作者名已经存在';
			return false;
		}
		if(isset($data['username']) && $data['username'])
		{
			$data['userid'] = userid($data['username']);
			if(!$data['userid'])
			{
				$this->error = '不存在的关联用户';
				return false;
			}
			if($exists = $this->get_by('userid',$data['userid']))
			{
				if($exists['spaceid'] != $spaceid)
				{
					$this->error = '已经关联的用户';
					return false;
				}
			}
		}
		
		if (!isset($data['iseditor'])) $data['iseditor'] = 0;
		if (!isset($data['userid'])) $data['userid'] = null;
		$data['initial'] = $this->_getIntial($data['author']);
		return $this->update($data, $spaceid);
	}
	
	private function _getIntial($name)
	{
		return $name ? pinyin::initial($name, config('config', 'charset')) : '';
	}
	
	function ls($where, $fields, $order, $page = 1, $size)
	{
		return $this->page($where, $fields,$order, $limit, $offset);
	}
	
	function get_comment($spaceid, $page, $pagesize)
	{
		$sql = 'SELECT f.commentid,f.content,f.created,f.createdby,f.nickname,s.title,s.url
				FROM `#table_comment` AS `f`
				LEFT JOIN `#table_content` AS `s`
				ON `f`.`contentid` =`s`.`contentid`
				WHERE `s`.`status`=6 AND disabled=0 AND `s`.`spaceid`='.$spaceid.'
				ORDER BY `f`.`created` DESC';
		$data = $this->db->page($sql,$page,$pagesize);
		return $data;
	}
	
	function totalPosts($spaceid)
	{
		$sql = "SELECT COUNT(contentid) AS count FROM #table_content WHERE spaceid={$spaceid} AND modelid=1 AND status=6";
		$r = $this->db->get($sql);
		return intval($r['count']);
	}
	
	function suggest($where)
	{
		$sql = 'SELECT name FROM #table_space WHERE '.$where.' ORDER BY `count` DESC, `spaceid` DESC LIMIT 30';
		$data = $this->db->select($sql);
		return $data;
	}
	
	function username($where)
	{
		$sql = 'SELECT userid,username FROM #table_member WHERE '.$where;
		$data = $this->db->select($sql);
		return $data;
	}
	
	function spaceid($name)
	{
		$r = $this->get_by('author',$name);
		$id = intval($r['spaceid']);
		if(empty($id)) $id = null;
		return $id;
	}
	
	function _after_select(&$data,$multi = true)
	{
		$member = loader::model('member_front','member');
		if($multi)
		{
			foreach($data as $k => $v)
			{
				$data[$k]['created'] = date('Y-m-d H:i',$v['created']);
				$data[$k]['modified'] = date('Y-m-d H:i',$v['modified']);
				$data[$k]['status_name'] = $this->statuss[$v['status']];
				$data[$k]['username'] = empty($v['userid'])?'':username($v['userid']);
				$data[$k]['icon'] = ($v['status'] == 4)?'<img src="images/space-recommend.gif"/>':'';
				$data[$k]['photo'] = ($v['photo'])?$v['photo']:(($v['userid'])?$member->get_photo($v['userid']):'');
			}
			return $data;
		}
		else
		{
			$data['created_day'] = date('Y-m-d',$data['created']);
			$data['created'] = date('Y-m-d H:i',$data['created']);
			$data['modified'] = date('Y-m-d H:i',$data['modified']);
			$data['status_name'] = $this->statuss[$data['status']];
			$data['username'] = empty($data['userid'])?'':username($data['userid']);
			$data['icon'] = ($data['status'] == 4)?'<img src="images/space-recommend.gif"/>':'';
			$data['photo'] = ($data['photo'])?$data['photo']:(($data['userid'])?$member->get_photo($data['userid']):'');
		}
	}

	function status($spaceid,$status)
	{
		if(!$spaceid) return false;
		$where = "`spaceid` IN ({$spaceid})";
		if($this->update(array('status' => $status), $where))
		{
			$result = array('state' =>true,'message' => '修改成功');
		}
		else
		{
			$result = array('state' =>false,'error' => $this->error());
		}
		$this->spaceid = $spaceid;
		$this->status = $status;
		$this->event = 'after_check';
		$this->notify();
		return $result;
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
