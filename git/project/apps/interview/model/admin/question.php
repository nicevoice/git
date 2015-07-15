<?php
class model_admin_question extends model
{
	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'interview_question';
		$this->_primary = 'questionid';
		$this->_fields = array('questionid', 'contentid', 'nickname', 'content', 'created', 'createdby', 'ip', 'iplocked', 'state');
		$this->_readonly = array('questionid');
		$this->_create_autofill = array('created'=>TIME, 'createdby'=>$this->_userid, 'ip'=>IP);
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array('content'=>array('not_empty'=>array('内容不能为空'))
		                          );
	}
	
	function add($data)
	{
		$data = $this->filter_array($data, array('contentid', 'nickname', 'content', 'state'));
		return $this->insert($data);
	}

	function edit($questionid, $data)
	{
        $data = $this->filter_array($data, array('nickname', 'content', 'ip'));
		return $this->update($data, $questionid);
	}
	
	function clear($contentid, $state = 0)
	{
		return $this->delete("`contentid`=$contentid AND `state`=$state");
	}
	
	function state($questionid, $state)
	{
		return $this->set_field('state', $state, $questionid);
	}
	
	function iplock($questionid)
	{
		$r = $this->get($questionid);
		if (!$r)
		{
			$this->error = '记录不存在';
			return false;
		}
		$contentid = $r['contentid'];
		$ip = $r['ip'];
		
		$ipbanned = loader::model('admin/ipbanned', 'system');
		$ipbanned->add($ip, TIME+86400);
		
		return $this->update(array('state'=>0, 'iplocked'=>TIME), "`contentid`=$contentid AND `ip`='$ip'");
	}
	
	function ipunlock($questionid)
	{
		$r = $this->get($questionid);
		if (!$r)
		{
			$this->error = '记录不存在';
			return false;
		}
		$contentid = $r['contentid'];
		$ip = $r['ip'];
		
		$ipbanned = loader::model('admin/ipbanned', 'system');
		$ipbanned->delete($ip);
		
		return $this->update(array('state'=>2, 'iplocked'=>0), "`contentid`=$contentid AND `ip`='$ip'");
	}
	
	function ls($contentid, $state = 0)
	{
		$contentid = intval($contentid);
		return $this->select("`contentid`=$contentid AND `state` = $state", '*', '`questionid` DESC');
	}
	
	function _after_select(& $data, $multiple)
	{
		if (!$data) return ;
		import('helper.iplocation');
		$iplocation = new iplocation();
		if ($multiple)
		{
			foreach ($data as $k=>$r)
			{
				$r['created'] = date('Y-m-d H:i', $r['created']);
				if ($r['createdby']) $r['username'] = username($r['createdby']);
				$r['iparea'] = $iplocation->get($r['ip']);
                $r['content'] = remove_xss($r['content']);
				$data[$k] = $r;
			}
		}
		else 
		{
			$data['created'] = date('Y-m-d H:i', $data['created']);
			if ($data['createdby']) $data['username'] = username($data['createdby']);
			$data['iparea'] = $iplocation->get($data['ip']);
            $data['content'] = remove_xss($data['content']);
		}
	}
}