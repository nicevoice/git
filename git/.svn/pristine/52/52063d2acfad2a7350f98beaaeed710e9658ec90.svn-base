<?php

class plugin_data extends object 
{
	private $log, $data;
	
	public function __construct(& $log)
	{
		$this->log = $log;
		$this->data = loader::model('admin/log_data','vote');
	}
	
	public function after_ls()
	{
		if (empty($this->log->data)) return false;
		
		$db = & factory::db();
		foreach ($this->log->data as $k=>$r)
		{
			$r['option'] = '<ul>';
			$option = $db->select("SELECT o.`optionid`, o.`name` FROM #table_vote_log_data d LEFT JOIN #table_vote_option o ON d.`optionid`=o.`optionid` WHERE d.`logid`=? ORDER BY d.`dataid` DESC", array($r['logid']));
			foreach ($option as $i=>$v)
			{
				$r['option'] .= "<li>".($i+1).'. '.$v['name']."</li>";
			}
			$r['option'] .= '</ul>';
			$this->log->data[$k]['option'] = $r['option'];
		}
	}
}