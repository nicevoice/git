<?php

class model_pv
{
	private $interval = 3600;
	
	function set_interval($second)
	{
		$this->interval = intval($second);
	}
	
	function get($pageid)
	{
		$cache = & factory::cache();
		$pv = $cache->get('page_pv');
		return isset($pv[$pageid]) ? $pv[$pageid] : 0;
	}
	
	function set($pageid)
	{
		$cache = & factory::cache();
		$pv = $cache->get('page_pv');
		if ($pv)
		{
			$page_pv = isset($pv[$pageid]) ? $pv[$pageid] + 1 : 1;
	        if ($pv['time'] < TIME - $this->interval || date('Y-m-d', $pv['time']) != date('Y-m-d', TIME))
	        {
	        	$this->update($pv);
	        }
			$pv[$pageid] = isset($pv[$pageid]) ? $pv[$pageid] + 1 : 1;
		}
		else 
		{
			$page_pv = 1;
			$pv = array('time'=>TIME, $pageid=>1);
		}
		$cache->set('page_pv', $pv);
		return $page_pv;
	}
	
	function update(& $pv)
	{
		$db = & factory::db();
		$date = date('Y-m-d', $pv['time']);
		
		unset($pv['time']);
		
		foreach ($pv as $pageid=>$count)
		{
			$result = $db->exec("UPDATE `#table_page` SET `pv`=`pv`+$count WHERE `pageid`=$pageid");
			if ($result)
			{
				$r = $db->get("SELECT `id` FROM `#table_page_stat` WHERE `pageid`=? AND `date`=?", array($pageid, $date));
				if ($r)
				{
					$result = $db->update("UPDATE `#table_page_stat` SET `pv`=`pv`+$count WHERE `id`=?", array($r['id']));
				}
				else
				{
					$result = $db->insert("INSERT INTO `#table_page_stat`(`pageid`, `date`, `pv`) VALUES(?, ?, ?)", array($pageid, $date, $count));
				}
			}
		}
		$pv = array('time'=>TIME);
	}
}