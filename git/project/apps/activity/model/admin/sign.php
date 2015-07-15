<?php

class model_admin_sign extends model implements SplSubject
{
	public $content, $catid, $modelid, $signid, $data, $fields, $order, $action, $category,$activity;
	private $observers = array();
	
	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'activity_sign';
		$this->_primary = 'signid';
		$this->_fields = array('signid', 'contentid', 'name', 'sex', 'photo', 'identity', 'company', 'job', 'telephone', 'mobile', 'email', 'qq', 'msn','site','address','zipcode','aid','note','created','createdby','ip','state','checked','checkedby');
		$this->_readonly = array('signid');
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array('signid'=>array('not_empty' =>array('参与者ID不能为空'),
		                                              'is_numeric' =>array('参与者ID必须是数字'),
		                                              'max_length' =>array(8, '参与者ID不得超过8个字节'),
		                                             )
		                          );
		$this->content = loader::model('admin/content', 'system');
        $this->category = & $this->content->category;
        $this->modelid = modelid('activity');
        $this->activity = loader::model('admin/activity','activity');
	}
	
	function get($signid, $fields = '*')
	{	
		$signid = intval($signid);
	    $data = parent::get($signid);
	    $r = $this->activity->get($data['contentid'],'selected,required');
	    $data['fields'] = $r['fields'];
		return $data;
	}
	
	function ls($where = null, $fields = '*', $order = '`#table_activity_sign`.`created` DESC', $page = null, $pagesize = null)
	{
		$this->where = $where;
		$this->fields = $fields;
		$this->order = $order;
		$this->page = $page;
		$this->pagesize = $pagesize;
	
		$this->data = $this->page($where, $this->fields, $this->order, $this->page, $this->pagesize);
     	if ($this->data)
		{

			$sql = "SELECT count(*) as `count` FROM `#table_activity_sign` WHERE ";
			if (!is_null($this->where)) $sql .= $this->where;
			$r = $this->db->get($sql);
			$this->total = $r ? $r['count'] : 0;
		}
		$this->event = 'after_ls';
		$this->notify();
		return $this->data;
	}
	
	function edit($signid,$data)
	{
		$data['photo'] = $data['thumb'];
		$this->data = $data;
		$result = $this->update($this->data, "signid =$signid");
		return $result;
	}
	
	function pass($signid)
	{
		
		$signmsg = $this->get($signid);
		$result = $this->update(array('state'=>1,'checked'=>TIME,'checkedby'=>$this->_userid), "signid in($signid)");
		if($result) 
		{
		   $counts = strpos($signid,',') ?count(explode(',',$signid)):1;
		   $data = $this->get(strpos($signid,',')?substr($signid,0,strpos($signid,',')):$signid);

		   $this->db->exec("UPDATE `#table_activity` SET `checkeds`=`checkeds`+$counts WHERE `contentid`=$data[contentid]");
		}
		return $result;
	}
	
	function unpass($signid)
	{
		$signmsg = $this->get($signid);
		$result = $this->update(array('state'=>0), "signid in($signid)");
		if($result) 
		{
		   $counts = strpos($signid,',') ?count(explode(',',$signid)):1;
		   $data = $this->get(strpos($signid,',')?substr($signid,0,strpos($signid,',')):$signid);

		   $this->db->exec("UPDATE `#table_activity` SET `checkeds`=`checkeds`-$counts WHERE `contentid`=$data[contentid]");
		}
		return $result;
	}

    function delete($signid)
    {
    	$data = $this->get(strpos($signid,',')?substr($signid,0,strpos($signid,',')):$signid);
    	$counts = strpos($signid,',') ?count(explode(',',$signid)):1;
    	$result =parent::delete($signid);
    	if($result) 
		{
		   if($data['state'])
		   {
		   	   $r = $this->db->exec("UPDATE `#table_activity` SET `total`=`total`-$counts,`checkeds`=`checkeds`-$counts WHERE `contentid`=$data[contentid]");
		   }
		   else
		   {
	
               $r = $this->db->exec("UPDATE `#table_activity` SET `total`=`total`-$counts WHERE `contentid`=$data[contentid]");
		   }
     	}
    	return $result;

    }
    
    function export($contentid,$type = 'checked')
    {
    	import('helper.xls');
    	$xl = new XLS();
    	$xl->SetAuthor($this->_userid);
    	$xl->SetCompany('CMSTOP');
    	$xl->AddSheet($type=='checked'?'已审核报名者信息':'所有报名者信息');
    	
    	$activity = $this->activity->get($contentid);
    	$fields = explode(',',$activity['selected']);	
    	foreach ($fields as $fk => $fv)
    	{
    		if($fv=='photo' || $fv=='aid') unset($fields[$fk]);
    	}
    	$fields=array_values($fields);
    	$fieldstr = implode(',',$fields);
    	if($type == 'checked')
    	{
    		$r = $this->db->select("SELECT $fieldstr  FROM `#table_activity_sign` WHERE state = 1 and contentid = ".$contentid);
    	}
    	else 
    	{
    		$r = $this->db->select("SELECT $fieldstr  FROM `#table_activity_sign` WHERE contentid = ".$contentid);
    	}
    	
    	$namemap = array(
    	    'name'     =>'名字',
    	    'sex'      =>'性别',
    	    'identity' =>'身份证',
    	    'company'  =>'公司',
    	    'job'      =>'工作',
    	    'telephone'=>'电话号码',
    	    'mobile'   =>'手机号码',
    	    'email'    =>'电子邮箱',
    	    'qq'       =>'QQ',
    	    'msn'      =>'MSN',
    	    'site'     =>'主页',
    	    'address'  =>'地址',
    	    'zipcode'  =>'邮编',
    	    'note'     =>'个人说明',
    	    'created'  =>'报名时间',
    	    'ip'       =>'ip',
    	    'state'    =>'状态',
    	    'checked'  =>'审核时间',
    	    'checkedby'=>'审核人'
    	    );
    	$xl->NewStyle('bold');
    	$xl->StyleSetFont(0, 0, 0, 1, 0, 0);
        $xl->SetActiveStyle('bold');
    	foreach ($fields as $k => $v)
    	{
    		$xl->Text(1,$k+1,$namemap[$v]);
    	}
    	
    	$xl->NewStyle('normal');
    	$xl->StyleSetFont(0, 0, 0, 0, 0, 0);
        $xl->SetActiveStyle('normal');
        
    	foreach ($r as $rk=>$rv)
    	{
    	   if(array_key_exists('sex',$rv)) $rv['sex'] = $rv['sex']?'男':'女';
    	   if(array_key_exists('created',$rv)) $rv['created'] = $rv['created']?date('Y-m-d H:i:s',$rv['created']):'';
    	   if(array_key_exists('checked',$rv)) $rv['checked'] = $rv['checked']?date('Y-m-d H:i:s',$rv['checked']):'';
    	   if(array_key_exists('checkedby',$rv)) $rv['checkedby'] = $rv['checkedby']?$this->getusername($rv['checkedby']):'';
    	   $rv=array_values($rv);
    	   foreach ($rv as $ck=>$cv)
    	   {
              $xl->Text($rk+2,$ck+1,$cv);
    	   }
    	}
    	$xl->Output('活动_'.$activity['title'].'_'.($type=='checked'?'已审核报名者信息':'所有报名者信息').'.xls');
    }
    
    private function getusername($userid)
    {
    	$r = $this->db->get('select * from #table_member where userid='.$userid);
    	return $r['username'];
    }

	function _after_select(& $data,$multiple = false)
	{
		if (!$data) return ;
		if ($multiple)
		{
			$data = array_map(array($this, 'output'), $data);
		}
		else 
		{
			$data = $this->output($data);
		}
                return $data;
	}
	
	function output(& $r)
	{
		if (!$r) return ;

		foreach ($r as $key => $value)
		{
			if($key == 'created') $r['created'] = date('Y-m-d H:i:s',intval($r['created']));
			if($key == 'sex')
			{
				if($r['sex'] == null) $r['sex'] = '-';
				else $r['sex'] = $r['sex']?'男':'女';
			}
			if($key == 'identity') $r['identity'] = $r['identity'] != ''?$r['identity']:'-';
			if($key == 'mobile') $r['mobile'] = $r['mobile']?$r['mobile']:'-';
			if($key == 'job') $r['job'] = $r['job']?$r['job']:'-';
			if($key == 'company') $r['company'] = $r['company']?$r['company']:'-';
			if($key == 'note') $r['note'] = $r['note']?$r['note']:'-';
			if($key == 'photo')
			{ 
				$r['photo'] = $r['photo']?$r['photo']:'';
				$r['phototips'] = $r['photo']?htmlspecialchars('<img width="200" src="'.UPLOAD_URL.$r['photo'].'" />'):'-';
			}
			if($key == 'aid') $r['aid'] = $r['aid']?$this->getatturl($r['aid']):'';
			
		}
                return $r;
	}
	
	function getatturl($aid)
	{
		$r = $this->db->get("select `filename`,`filepath` from `#table_attachment` where aid=?",array($aid));
		return $r?UPLOAD_URL.$r['filepath'].$r['filename']:'';
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