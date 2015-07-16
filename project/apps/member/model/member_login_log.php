<?php
class model_member_login_log extends model 
{
	protected $maxfailedcount = 3, $lockedhours = 1;
	private $totals;
	function __construct()
	{
		parent::__construct();
		
		$this->_table = $this->db->options['prefix'].'member_login_log';
		$this->_primary = 'logid';
		$this->_fields = array('logid', 'username', 'ip', 'time', 'succeed');
		
		$this->_readonly = array('logid', 'username', 'ip', 'time', 'succeed');
		$this->_create_autofill = array('ip'=>IP, 'time'=>TIME);
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();
		
		$this->maxfailedcount = setting('member','log_max');
		$this->lockedhours = setting('member','lock_minute');
	}
	
	function add($data)
	{
		$data = $this->filter_array($data, array('username', 'succeed'));
		return $this->insert($data);
	}
	
	function del($post)
	{
		$where = array();
		if (!empty($post['date'])) 			$where[] = " `time` < '".strtotime($post['date'])."'";
		if (!empty($post['username'])) 		$where[] = "`username`='".$post['username']."'";
		if (!empty($post['ip'])) 			$where[] = "`ip`='".$post['ip']."'";
		if (isset($post['succeed']) && is_numeric($post['succeed'])) 		$where[] = "`succeed`=".intval($post['succeed']);
		
		if (!empty($where)) $where = implode(' AND ', $where);
		return parent::delete($where);
	}
	
	function ls($username = null, $ip = null, $succeed = null, $mintime = null, $maxtime = null, $order = '`logid` DESC', $page = 1, $size = 20)
	{
		$where = null;
		if ($username) $where[] = "`username`='$username'";
		if ($ip) $where[] = "`ip`='$ip'";
		if ($succeed != null) $where[] = "`succeed`=".intval($succeed);
		if ($mintime)
		{
			$mintime = trim($mintime);
			if (strlen($mintime) == 10) $mintime .= ' 00:00:00';
			$where[] = "`time`>=".strtotime($mintime);
		}
		if ($maxtime)
		{
			$maxtime = trim($maxtime);
			if (strlen($maxtime) == 10) $maxtime .= ' 23:59:59';
			$where[] = "`time`<=".strtotime($maxtime);
		}
		if ($where) $where = implode(' AND ', $where);
		
		$this->totals = $this->count($where);
		$result = $this->page($where, '*', $order, $page, $size);
		return $result;
	}
	
	function total()
	{
		return $this->totals;
	}
	
	function valid($username)
	{
		$time = TIME - $this->lockedhours*3600;
		return $this->count("`username`=? AND `ip`=? AND `succeed`=0 AND `time`>?", array($username, IP, $time)) <= $this->maxfailedcount;
	}
	
	function  _after_select(& $data, $multiple)
	{
		if (empty($data)) {
			return $data;
		}
		import('helper.iplocation');
		$this->iplocation = new iplocation();
		if ($multiple)
		{
			foreach ($data as $key => $value) 
			{
				$data[$key]['time'] = date('Y-m-d H:i:s', $value['time']);
				$data[$key]['succeed'] = $value['succeed']?'成功':'<font color="red">失败</font>';
				$data[$key]['location'] = $this->iplocation->get($value['ip']);;
			}
		}
		else
		{
			$data['time'] = date('Y-m-d H:i:s', $data['time']);
			$data['succeed'] = $data['succeed']?'成功':'<font color="red">失败</font>';
			$data['location'] = $this->iplocation->get($data['ip']);;
		}
	}
}