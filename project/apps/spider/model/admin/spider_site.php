<?php
/**
 * cmstop_spider_site
 *   siteid
 * 	 name
 */
class model_admin_spider_site extends model
{
	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'spider_site';
		$this->_primary = 'siteid';
		$this->_fields = array('siteid','name');
		
		$this->_readonly = array('siteid');
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();
	}
	function getNum($siteid)
	{
		$return = array('rulenum'=>0,'tasknum'=>0);
		$sql = "SELECT ruleid FROM #table_spider_rules WHERE siteid='$siteid'";
		$rs = $this->db->select($sql);
		if (!$rs) return $return;
		$return['rulenum'] = count($rs);
		$ruleids = array();
		foreach ($rs as &$r)
		{
			$ruleids[] = $r['ruleid'];
		}
		$sql = "SELECT count(*) as c FROM #table_spider_task WHERE ruleid IN ( ".implode_ids($ruleids)." )";
		$rs = $this->db->select($sql);
		$return['tasknum'] = $rs[0]['c'];
		return $return;
	}
	function _after_select(&$result, $multi = true)
	{
		if ($multi)
		{
			foreach ($result as &$r)
			{
				$r = array_merge($r, $this->getNum($r['siteid']));
			}
		}
		else
		{
			$result = array_merge($result, $this->getNum($result['siteid']));
		}
	}
}