<?php
define('PUSH_LIB_DIR', app_dir('push').'lib/');

/* include plugin abstract */
loader::import('lib.plugin');
class push
{
	function getList($db, $rule, $charset, $length = 20, $offset = 0, $where = '')
	{
		if (empty($rule['maintable']))
		{
			throw new Exception('need maintable');
		}
		if (empty($rule['primary']))
		{
			throw new Exception('need primary');
		}
		$sql = "SELECT * FROM ".$rule['maintable'];
		if (! empty($rule['jointable']))
		{
			foreach ($rule['jointable'] as $table => $on)
			{
				$sql .= " LEFT JOIN $table ON $on";
			}
		}
		$sql .= " WHERE 1";
		if (! empty($rule['condition']))
		{
			$sql .= " AND (".$rule['condition'].")";
		}
		if (! empty($where))
		{
			$sql .= " AND ($where)";
		}
		$sql .= " ORDER BY ".$rule['primary']." desc";
		$rowset = $db->limit($sql, $length, $offset);
		return $this->_getPlugin($rule, $charset)->getList($rowset);
	}
	function getOne($db, $guid, $rule, $charset)
	{
		if (empty($rule['maintable']))
		{
			throw new Exception('need maintable');
		}
		if (empty($rule['primary']))
		{
			throw new Exception('need primary');
		}
		$sql = "SELECT * FROM ".$rule['maintable'];
		if (! empty($rule['jointable']))
		{
			foreach ($rule['jointable'] as $table => $on)
			{
				$sql .= " LEFT JOIN $table ON $on";
			}
		}
		if (empty($guid))
		{
			return array();
		}
		$sql .= " WHERE ".$rule['primary']."=$guid";
		$rowset = $db->select($sql);
		$row = reset($rowset);
		return $this->_getPlugin($rule, $charset)->getOne($row);
	}
	function getTotal($db, $rule, $where)
	{
		if (empty($rule['maintable']))
		{
			throw new Exception('need maintable');
		}
		$sql = "SELECT count(*) as c FROM ".$rule['maintable'];
		if (! empty($rule['jointable']))
		{
			foreach ($rule['jointable'] as $table => $on)
			{
				$sql .= " LEFT JOIN $table ON $on";
			}
		}
		$sql .= " WHERE 1";
		if (! empty($rule['condition']))
		{
			$sql .= " AND (".$rule['condition'].")";
		}
		if (! empty($where))
		{
			$sql .= " AND ($where)";
		}
		$row = $db->select($sql);
		$row = reset($row);
		return $row['c'];
	}
	function _getPlugin($rule, $charset)
	{
		$plugin = empty($rule['plugin']) ? 'other' : $rule['plugin'];
		$pluginfile = 'plugin.'.$plugin;
		$class = 'push_plugin_'.$plugin;
		if (! class_exists($class,false))
		{
			if (! loader::import($pluginfile))
			{
				throw new Exception('no this type of plugin');
			}
		}
		$plugin =  new $class($rule, $charset);
		if (! $plugin instanceof push_plugin)
		{
			throw new Exception('not a valid plugin');
		}
		return $plugin;
	}
	function getDetails($db, $guid, $rule, $charset)
	{
		if (empty($rule['maintable']))
		{
			throw new Exception('need maintable');
		}
		if (empty($rule['primary']))
		{
			throw new Exception('need primary');
		}
		$sql = "SELECT * FROM ".$rule['maintable'];
		if (! empty($rule['jointable']))
		{
			foreach ($rule['jointable'] as $table => $on)
			{
				$sql .= " LEFT JOIN $table ON $on";
			}
		}
		$sql .= " WHERE ".$rule['primary']."=$guid";
		$rowset = $db->select($sql);
		$row = reset($rowset);
		return $this->_getPlugin($rule, $charset)->getDetails($row);
	}
}