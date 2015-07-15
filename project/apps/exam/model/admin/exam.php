<?php

class model_admin_exam extends model implements SplSubject
{
	public $content, $catid, $modelid, $contentid, $data, $fields, $order, $action, $category,$exam_qtype, $exam_qtype_question;
	private $observers = array();

	function __construct()
	{
		parent::__construct();

        /**
         * 会计网
         */
        $user =  login_member();
        $this->_userid = $this->_userid ? $this->_userid : $user['user_id'];
        $this->_username = $this->_username ? $this->_username : $user['name'];
		$this->_table = $this->db->options['prefix'].'exam';
		$this->_primary = 'contentid';
		$this->_fields = array('contentid', 'description', 'starttime', 'endtime', 'maxanswers', 'minhours', 'checklogined','questions', 'answers','isday', 'examtime', 'integral','qcount', 'count', 'md5id');
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
		$this->exam_qtype = loader::model('admin/exam_qtype', 'exam');
		$this->exam_qtype_question = loader::model('admin/exam_qtype_question', 'exam');
		$this->content = loader::model('admin/content', 'system');
        $this->content->_userid = $this->_userid;
		$this->category = & $this->content->category;
		$this->modelid = modelid('exam');
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

	function get($contentid, $fields = '*', $action = null, $table_exam = true)
	{
		if (!in_array($action, array(null, 'get', 'view', 'show'))) return false;
		
		$this->contentid = intval($contentid);
		$this->fields = $fields;
		$this->action = $action;
		$this->table_exam = $table_exam;

		$this->event = 'before_get';
		$this->notify();
		
		if ($this->table_exam)
		{
			$this->data = $this->db->get("SELECT $this->fields FROM `#table_content`, `#table_exam` WHERE `#table_content`.`contentid`=`#table_exam`.`contentid` AND `#table_content`.`contentid`=$this->contentid");
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
	
	function ls($where = null, $fields = '*', $order = 'c.`contentid` DESC', $page = null, $pagesize = null, $table_exam = false)
	{
		$this->where = $where;
		$this->fields = $fields;
		$this->order = $order;
		$this->page = $page;
		$this->pagesize = $pagesize;
		$this->table_exam = $table_exam;
		
		$this->event = 'before_ls';
		$this->notify();
		
		if ($this->table_exam)
		{
			if (is_array($this->where)) $this->where = str_replace('WHERE','',$this->content->where($this->where));
			$this->sql = "SELECT $this->fields FROM `#table_content` c, `#table_exam` s WHERE c.`contentid`=s.`contentid`";
			if (!is_null($this->where)) $this->sql .= ' AND '.$this->where;
			if ($this->order) $this->sql .= ' ORDER BY '.$this->order;
			$this->data = $this->db->page($this->sql, $this->page, $this->pagesize);
			if ($this->data)
			{
				array_map(array($this->content, 'output'),  $this->data);
				if (!is_null($page))
				{
					$sql = "SELECT count(*) as `count` FROM `#table_content` c, `#table_exam` s WHERE c.`contentid`=s.`contentid`";
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
        $qtype = $data['qtype'];
        $notwrite = $data['notwrite'];
		$this->data = $data;

		$this->event = 'before_add';
		$this->notify();
        if (!$this->_userid)$this->content->_userid = 100403;
		$this->contentid = $this->content->add($this->data);
		if (!$this->contentid)
		{
			$this->error = $this->content->error();
			return false;
		}
        $this->data['md5id'] = md5($this->contentid);
        $this->data['integral'] = $this->data['integral'] ? $this->data['integral'] : 0;
		$this->data['contentid'] = $this->contentid;
		$data = $this->filter_array($this->data, array('contentid', 'description', 'starttime', 'endtime', 'maxanswers', 'minhours', 'checklogined','isday', 'examtime', 'integral', 'md5id','qcount'));
		$result = $this->insert($data);
        if ($this->contentid && $qtype) {
            foreach ($qtype as $t) {
                $t['created'] = time();
                $t['createdby'] = $this->_userid ? $this->_userid : 100403;
                $t['contentid'] = $this->contentid;
                $t['qid'] = $t['id'];
                $t['num'] = $t['num'] ? $t['num'] : 0;
                if ($id = $this->exam_qtype->insert($t)) {
                    $_qtype = array();
                    foreach ($t['qids'] as $q) {
                        $_qtype[] = '(' . $id . ', '. $q . ')';
                        //不要打开...拜托
                        //$this->exam_qtype_question->insert(array('qtypeid'=>$id, 'questionid'=>$q));
                    }
                    if ($_qtype) {
                        $qtype_insert = "INSERT INTO #table_exam_qtype_question(qtypeid, questionid) VALUES" . implode(',', $_qtype);
                    $this->db->query($qtype_insert);
                    }
                }
            }
        }
        loader::model('admin/content', 'system')->update(array('url'=>WWW_URL.'exam/show/'.$this->data['md5id'] . '.html'), $this->contentid);
		if($result && !$notwrite)
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
        $qtype = $data['qtype'];
		$this->event = 'before_edit';
		$this->notify();

		if (!$this->content->edit($this->contentid, $this->data))
        {
			$this->error = $this->content->error();
			return false;
		}
		
		$data = $this->filter_array($this->data, array('contentid', 'description', 'starttime', 'endtime', 'maxanswers', 'minhours', 'checklogined', 'examtime', 'integral'));
		$result = $this->update($data, $this->contentid);
        if ($this->contentid && $qtype) {
            foreach ($qtype as $t) {
                $t['created'] = time();
                $t['createdby'] = $this->_userid;
                $t['contentid'] = $this->contentid;
                $t['qid'] = $t['id'];
                $t['num'] = $t['num'] ? $t['num'] : 0;
                if ($t['qtypeid']) {
                    $this->exam_qtype->update($t, $t['qtypeid']);
                    $id = $t['qtypeid'];
                } else {
                    $id = $this->exam_qtype->insert($t);
                }
                if ($id) {
                    foreach ($t['qids'] as $q) {
                        $this->exam_qtype_question->insert(array('qtypeid'=>$id, 'questionid'=>$q));
                    }
                }
            }
        }
		if($result)
		{
			$this->event = 'after_edit';
			$this->notify();
		}
        record2url($result['url']);
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
		$answer = loader::model('admin/answer','exam');
		$answer->clear($contentid);
		
		$question = loader::model('admin/question','exam');
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
	function add_property($data)
    {

        /*$property = $this->db->get("SELECT parentids FROM #table_property WHERE proid={$data['parentid']}");
        printR($property);*/
        $property_model = loader::model('admin/property', 'system');
        $property = $property_model->get($data['parentid'], 'parentids');
        $data['parentids'] = $property['parentids'] ? $property['parentids'] . ',' . $data['parentid'] : $data['parentid'];
        $data['disabled'] = 0;
        $parentids = explode(',', $data['parentids']);

        if ($parentids[1] == '101000') {
            $o_p = $this->db->get('SELECT proid FROM #table_property WHERE proid  > 101000 AND proid < 110000 ORDER BY proid DESC LIMIT 1');
            if (!$o_p){
                $data['proid'] = 101001;
            } else {
                $data['proid'] = $o_p['proid'] + 1;
            }

        } else {
            $o_p = $this->db->get('SELECT proid FROM #table_property WHERE proid  > 110000 AND proid < 200000 ORDER BY proid DESC LIMIT 1');
            if (!$o_p){
                $data['proid'] = 110001;
            } else {
                $data['proid'] = $o_p['proid'] + 1;
            }
        }
        $proid = $property_model->insert($data);
        if ($proid) {
            foreach($parentids as $val) {
                $n = $this->db->get("SELECT proid,childids FROM #table_property WHERE proid={$val} LIMIT 1");
                $childids = $n['childids']  ? $n['childids'] . ',' . $data['proid'] : $data['proid'];
                if ($n['proid'] )$property_model->update(array('childids'=> $childids) , $n['proid']);
            }
        }
        return $proid;
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
			$this->error = '题库开始时间不得晚于结束时间！';
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
    
    function mySystemMessage($catid, $title, $url)
    {
        set_time_limit(3600);
        load_uc();
        $category_id = array('11100'=>1,'11200'=>8,'11300'=>9,'11500'=>10);
        $Type = '【题库】';
        $Title = $title . ' 试卷更新了，小伙伴快去练习吧。';
        $Message = '<a href="'.$url.'" target="_blank">点击进入练习</a>';
        $pagesize = 20;
        $sqlCount='select count(*) as total from #table_member where category_id='.$category_id[$catid].' AND groupid=6';
        $result = $this->db->get($sqlCount);
        $total = $result['total'];
        $pages = ceil($total / $pagesize);
        for($page = 0; $page < $pages; $page++){
            $uid = '';
            $limit = $page*$pagesize;
            $sql='select userid from #table_member where category_id='.$category_id[$catid].' AND groupid=6 limit '.$limit.','.$pagesize.'';
            $resultA = $this->db->select($sql);
            foreach($resultA as $v){
                $uid .= ','.$v['userid'];
            }
            $uid = trim($uid, ',');
            $status = uc_pm_send(1, $uid, $Type . $Title, $Message, 1, 0, 0, 2);
            if($status > 0){
                sleep(1);
            }
        }
    }
}