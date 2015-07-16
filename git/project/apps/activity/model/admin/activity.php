<?php

class model_admin_activity extends model implements SplSubject
{
	public $content, $catid, $modelid, $contentid, $data, $fields, $order, $action, $category;
	private $observers = array();

	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'activity';
		$this->_primary = 'contentid';
		$this->_fields = array('contentid', 'description', 'content', 'starttime', 'endtime', 'address', 'point', 'type', 'maxpersons', 'gender', 'mailto', 'signstart', 'signend', 'selected', 'required', 'displayed', 'total', 'checkeds', 'signstoped', 'mininterval');
		$this->_readonly = array('contentid');
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array('contentid'=>array('not_empty' =>array('内容ID不能为空'),
		                                              'is_numeric' =>array('内容ID必须是数字'),
		                                              'max_length' =>array(8, '内容ID不得超过8个字节'),
		                                             )
		                          );
        $this->content = loader::model('admin/content', 'system');
        $this->category = & $this->content->category;
        $this->modelid = modelid('activity');
	}
	
	public function __call($method, $args)
	{
		if(!priv::aca('activity', 'activity', $method)) return true;
		if(in_array($method, array('clear', 'remove', 'restore', 'restores', 'approve', 'pass', 'reject', 'reference', 'move', 'islock', 'lock', 'unlock', 'publish', 'unpublish'), true)) 
		{
			$id = id_format($args[0]);
			if ($id === false)
			{
				$this->error = "$id 格式不正确";
				return false;
			}
			if (is_array($id)) return array_map(array($this, $method), $id);
			if (in_array($method, array('clear', 'restores')))
			{
				$this->catid = $args[0];
			}
			else 
			{
				$this->contentid = $args[0];
			}
			
			$this->event = 'before_'.$method;
			$this->notify();
			$result = $this->content->$method($args[0]);
			if (!$result)
			{
				$this->error = $this->content->error();
				return false;
			}
			$this->event = 'after_'.$method;
			$this->notify();
			return $result;
        }
	}

	function get($contentid, $fields = '*', $action = null, $table_activity = true)
	{
		if (!in_array($action, array(null, 'get', 'view', 'show'))) return false;
		
		$this->contentid = intval($contentid);
		$this->fields = $fields;
		$this->action = $action;
		$this->table_activity = $table_activity;

		$this->event = 'before_get';
		$this->notify();
		
		if ($this->table_activity)
		{
			$this->data = $this->db->get("SELECT $this->fields FROM `#table_content`, `#table_activity` WHERE `#table_content`.`contentid`=`#table_activity`.`contentid` AND `#table_content`.`contentid`=$this->contentid");
			if ($this->data && ($this->action == 'get' || $this->action == 'view' || $this->action == 'show'))
			{
				$this->output($this->data);
				if ($this->action != 'show') $this->content->output($this->data);
				$this->content->contentid = & $this->contentid;
				$this->content->action = & $this->action;
				$this->content->data = & $this->data;
				$this->content->event = 'after_get';
				$this->content->notify();
			}
		}
		else 
		{
			$this->data = $this->content->get($this->contentid, $this->fields, $this->action);
		}

		$this->event = 'after_get';
		$this->notify();
		
		return $this->data;
	}
	
	function ls($where = null, $fields = '*', $order = 'c.`contentid` DESC', $page = null, $pagesize = null, $table_activity = false)
	{
		$this->where = $where;
		$this->fields = $fields;
		$this->order = $order;
		$this->page = $page;
		$this->pagesize = $pagesize;
		$this->table_activity = $table_activity;
		
		$this->event = 'before_ls';
		$this->notify();
		
		if ($this->table_activity)
		{
			if (is_array($this->where)) $this->where = $this->content->where($this->where);
			$this->sql = "SELECT $this->fields FROM `#table_content` c, `#table_activity` a WHERE c.`contentid`=a.`contentid`";
			if (!is_null($this->where)) $this->sql .= ' AND '.$this->where;
			if ($this->order) $this->sql .= ' ORDER BY '.$this->order;
			$this->data = $this->db->page($this->sql, $this->page, $this->pagesize);
			if ($this->data)
			{
				array_map(array($this->content, 'output'),  $this->data);
				array_map(array($this,'output'), $this->data);
				if (!is_null($page))
				{
					$sql = "SELECT count(*) as `count` FROM `#table_content` c, `#table_activity` a WHERE c.`contentid`=a.`contentid`";
					if (!is_null($this->where)) $sql .= ' AND '.$this->where;
					$r = $this->db->get($sql);
					$this->total = $r ? $r['count'] : 0;
				}
			}
		}
		else 
		{
			$this->data = $this->content->ls($this->where, $this->fields, $this->order, $this->page, $this->pagesize);
			if (!is_null($page)) $this->total = $this->content->total;
		}
		
		$this->event = 'after_ls';
		$this->notify();
		
		return $this->data;
	}

	function add($data)
	{
		$this->data = $data;
		
		$this->event = 'before_add';
		$this->notify();

		if(!$this->checkdate()) return false;
		
		$this->contentid = $this->content->add($this->data);
		if (!$this->contentid)
		{
			$this->error = $this->content->error();
			return false;
		}
		$this->data['contentid'] = $this->contentid;
		$result = $this->insert($this->data);
        if ($result)
        {
			$this->event = 'after_add';
			$this->notify();
        }
        $firstid = $this->contentid ? $this->contentid : 0;
        if($_POST['options']['catid']) //同时发到其他栏目
		{
			$catids = explode(',', $_POST['options']['catid']);
			foreach ($catids as $catid)
			{
				$this->content->reference($this->contentid, $catid);
			}
		}
		return $result ? $firstid : false;
	}

	function edit($contentid, $data)
	{
		$this->contentid = intval($contentid);
		$this->data = $data;

		$this->event = 'before_edit';
		$this->notify();
		
		if(!$this->checkdate()) return false;
		
		if (!$this->content->edit($this->contentid, $this->data))
        {
			$this->error = $this->content->error();
			return false;
        }
        
        $result = $this->update($this->data, $this->contentid);
        if ($result)
        {
			$this->event = 'after_edit';
			$this->notify();
        }
        return $result;
	}
	
	function delete($contentid)
	{
		$contentid = id_format($contentid);
		if ($contentid === false)
		{
			$this->error = "$contentid 格式不正确";
			return false;
		}
		if (is_array($contentid)) return array_map(array($this, 'delete'), $contentid);
		
		$this->contentid = intval($contentid);
		$this->event = 'before_delete';
		$this->notify();
		if (!$this->content->delete($this->contentid))
		{
			$this->error = $this->content->error();
			return false;
		}
		$this->event = 'after_delete';
		$this->notify();
		return true;
	}
	
	function move($contentid, $catid)
	{
		$contentid = id_format($contentid);
		if ($contentid === false)
		{
			$this->error = "$contentid 格式不正确";
			return false;
		}

		// 判断当前栏目是否支持此模型
		if (!$cate = value(table('category'), $catid))
		{
			$this->error = "栏目不存在";
			return false;
		}

		foreach (unserialize($cate['model']) as $key=>$item)
		{
			if (isset($item['show']) && $item['show'])
			{
				$model[] = $key;
			}
		}
		if (!in_array($this->modelid, $model))
		{
			$this->error	= '栏目不支持此模型内容';
			return false;
		}
		
		if(!priv::aca('activity', 'activity', 'move')) return true;
		if (is_array($contentid)) return array_map(array($this, 'move'), $contentid, array_fill(0, count($contentid), $catid));
		
		$this->contentid = $contentid;
		$this->catid = intval($catid);
		
		$this->event = 'before_move';
		$this->notify();
		
		if (!$this->content->move($this->contentid, $this->catid))
		{
			$this->error = $this->content->error();
			return false;
		}
		
		$this->event = 'after_move';
		$this->notify();
		
		return true;
	}
	
	function reference($contentid, $catid)
	{
		$this->contentid = $contentid;
		$this->catid = intval($catid);
		
		$this->event = 'before_reference';
		$this->notify();
		
		if (!$this->content->reference($this->contentid, $this->catid))
		{
			$this->error = $this->content->error();
			return false;
		}
		
		$this->event = 'after_reference';
		$this->notify();
		
		return true;
	}
	
	function html_write($contentid)
	{
		$contentid = id_format($contentid);
		if ($contentid === false)
		{
			$this->error = "$contentid 格式不正确";
			return false;
		}
		if (is_array($contentid)) return array_map(array($this, 'html_write'), $contentid);

		$this->contentid = $contentid;
		
		$this->event = 'html_write';
		$this->notify();
		
		return true;
	}
	
	function stop($contentid)
	{
		$result = $this->update(array('signstoped'=>1), "contentid in($contentid)");
		return $result;
	}
	
	function unstop($contentid)
	{
		$result = $this->update(array('signstoped'=>0), "contentid in($contentid)");
		return $result;
	}
	
	function checkdate()
	{
		$this->data['starttime'] = $this->data['starttime']?strtotime($this->data['starttime']):TIME;
		$this->data['endtime'] = $this->data['endtime']?strtotime($this->data['endtime']):null;
		$this->data['signstart'] = $this->data['signstart']?strtotime($this->data['signstart']):TIME;
		$this->data['signend'] = $this->data['signend']?strtotime($this->data['signend']):null;
		
		if (!is_null($this->data['endtime']) && $this->data['starttime']>=$this->data['endtime']) 
		{
			$this->error = "活动结束时间不能早于开始时间";
			return false;
		}
		
		if(!is_null($this->data['endtime']) && $this->data['signstart'] >= $this->data['endtime'])
		{
			$this->error = "报名开始时间不能晚于活动结束时间";
			return false;
		}
		
		if(!is_null($this->data['signend']) && $this->data['signend'] <= $this->data['signstart'])
		{
			$this->error = "报名结束时间不能早于开始时间";
			return false;
		}
		
		
		return true;
	}
	
	function output(& $r)
	{
		
		$signend = is_null($r['signend'])?TIME+3600:$r['signend'];
		$maxpersons = $r['maxpersons']?$r['maxpersons']:$r['checked']+1;
		if(TIME<$r['signstart'])
		{
			$r['signstate'] = '报名未开始';
			$r['signstatef'] = '报名未开始';
		}
		else 
		{
			if(!$r['signstoped'] && $r['checkeds']<$maxpersons && TIME<$signend)
			{
				$r['signstate'] = '<a href="javascript:;" class="stop">报名中</a>';
				$r['signstatef'] = '报名中';
			}
			
			if($r['signstoped'] && $r['checkeds']<$maxpersons && TIME<$signend)
			{
				$r['signstate'] = '<a href="javascript:;" class="unstop">暂停</a>';
				$r['signstatef'] = '暂停';
			}
			
			if(TIME>$signend || $r['checkeds']>=$maxpersons)
			{
				$r['signstate'] = '报名结束';
				$r['signstatef'] = '报名结束';
			}
		}
		
		$r['starttime'] = date('Y-m-d H:i:s',$r['starttime']);
		$r['endtime'] = !is_null($r['endtime'])?date('Y-m-d H:i:s',$r['endtime']):'';
		$r['signstart'] = date('Y-m-d H:i:s',$r['signstart']);
		$r['signendls'] = !is_null($r['signend'])?date('Y-m-d H:i',$r['signend']):'';
		$r['signend'] = !is_null($r['signend'])?date('Y-m-d H:i:s',$r['signend']):'';
		$r['maxpersons'] = $r['maxpersons']?$r['maxpersons']:'';
		
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