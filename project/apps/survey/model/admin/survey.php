<?php

class model_admin_survey extends model implements SplSubject
{
	public $content, $catid, $modelid, $contentid, $data, $fields, $order, $action, $category;
	private $observers = array();

	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'survey';
		$this->_primary = 'contentid';
		$this->_fields = array('contentid', 'description', 'starttime', 'endtime', 'maxanswers', 'minhours', 'checklogined', 'mailto','questions', 'answers', 'template');
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
		$this->modelid = modelid('survey');
	}
	
	public function __call($method, $args)
	{
		if(in_array($method, array('clear', 'remove', 'restore', 'restores', 'approve', 'pass', 'reject', 'islock', 'lock', 'unlock', 'publish', 'unpublish'), true)) 
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

	function get($contentid, $fields = '*', $action = null, $table_survey = true)
	{
		if (!in_array($action, array(null, 'get', 'view', 'show'))) return false;
		
		$this->contentid = intval($contentid);
		$this->fields = $fields;
		$this->action = $action;
		$this->table_survey = $table_survey;

		$this->event = 'before_get';
		$this->notify();
		
		if ($this->table_survey)
		{
			$this->data = $this->db->get("SELECT $this->fields FROM `#table_content`, `#table_survey` WHERE `#table_content`.`contentid`=`#table_survey`.`contentid` AND `#table_content`.`contentid`=$this->contentid");
			if ($this->data && ($this->action == 'get' || $this->action == 'view' || $this->action == 'show'))
			{
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
		$this->output($this->data);
		
		$this->event = 'after_get';
		$this->notify();
		
		return $this->data;
	}
	
	function ls($where = null, $fields = '*', $order = 'c.`contentid` DESC', $page = null, $pagesize = null, $table_survey = false)
	{
		$this->where = $where;
		$this->fields = $fields;
		$this->order = $order;
		$this->page = $page;
		$this->pagesize = $pagesize;
		$this->table_survey = $table_survey;
		
		$this->event = 'before_ls';
		$this->notify();
		
		if ($this->table_survey)
		{
			if (is_array($this->where)) $this->where = str_replace('WHERE','',$this->content->where($this->where));
			$this->sql = "SELECT $this->fields FROM `#table_content` c, `#table_survey` s WHERE c.`contentid`=s.`contentid`";
			if (!is_null($this->where)) $this->sql .= ' AND '.$this->where;
			if ($this->order) $this->sql .= ' ORDER BY '.$this->order;
			$this->data = $this->db->page($this->sql, $this->page, $this->pagesize);
			if ($this->data)
			{
				array_map(array($this->content, 'output'),  $this->data);
				if (!is_null($page))
				{
					$sql = "SELECT count(*) as `count` FROM `#table_content` c, `#table_survey` s WHERE c.`contentid`=s.`contentid`";
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
		if(!$this->input($data))
		{
			return false;
		}
		$this->data = $data;
		
		$this->event = 'before_add';
		$this->notify();
		
		$this->contentid = $this->content->add($this->data);
		if (!$this->contentid)
		{
			$this->error = $this->content->error();
			return false;
		}
		
		$this->data['contentid'] = $this->contentid;
		$data = $this->filter_array($this->data, array('contentid', 'description', 'starttime', 'endtime', 'maxanswers', 'minhours', 'checklogined','mailto', 'template'));
		$result = $this->insert($data);
		if($result)
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
		$this->input($data);
		
		$this->contentid = intval($contentid);
		$this->data = $data;

		$this->event = 'before_edit';
		$this->notify();
		
		if (!$this->content->edit($this->contentid, $this->data))
        {
			$this->error = $this->content->error();
			return false;
		}
		
		$data = $this->filter_array($this->data, array('contentid', 'description', 'starttime', 'endtime', 'maxanswers', 'minhours', 'checklogined','mailto', 'template'));
		$result = $this->update($data, $this->contentid);
		if($result)
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
	
	function data_clear($contentid)
	{
		$answer = loader::model('admin/answer','survey');
		$answer->clear($contentid);
		
		$question = loader::model('admin/question','survey');
		$question->clear($contentid);
		
		return $this->set_field('answers', 0, $contentid);
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

		if (is_array($contentid)) return array_map(array($this, 'move'), $contentid, array_fill(0, count($contentid), $catid));
		
		$this->contentid = intval($contentid);
		$this->catid = intval($catid);
		
		$this->event = 'before_move';
		$this->notify();
		
		if ($this->content->move($this->contentid, $this->catid))
		{
			$this->event = 'after_move';
			$this->notify();
			return true;
		}
		else 
		{
			$this->error = $this->content->error();
			return false;
		}
	}
	
	function reference($contentid, $catid)
	{
		$this->contentid = intval($contentid);
		$this->catid = intval($catid);
		
		$this->event = 'before_reference';
		$this->notify();
		
		if ($this->content->reference($this->contentid, $this->catid))
		{
			$this->event = 'after_reference';
			$this->notify();
			return true;
		}
		else 
		{
			$this->error = $this->content->error();
			return false;
		}
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
	
	private function input(& $r)
	{
		$r['starttime'] = $r['starttime'] ? strtotime($r['starttime']) : 0;
		$r['endtime'] = $r['endtime'] ? strtotime($r['endtime']) : 0;
		if($r['endtime'] && $r['starttime']>$r['endtime']) 
		{
			$this->error = '调查开始时间不得晚于结束时间！';
			return false;
		}
		return true;
	}
	
	private function output(& $r)
	{
		$r['starttime'] = $r['starttime'] ? date('Y-m-d H:i:s', $r['starttime']) : '';
		$r['endtime'] = $r['endtime'] ? date('Y-m-d H:i:s', $r['endtime']) : '';
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