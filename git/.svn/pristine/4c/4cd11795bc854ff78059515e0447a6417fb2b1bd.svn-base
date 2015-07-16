<?php
define('DB_CACHE_DIR', CACHE_PATH.'table'.DS);

class db_cache extends object
{
	private $cache_dir, $config;

	function __construct($cache_dir = null)
	{
		$this->cache_dir = is_null($cache_dir) ? DB_CACHE_DIR : $cache_dir;
		$this->_get_config();
	}

	function get($table, $id = null, $field = null)
	{
		static $_cache;
		if (!isset($_cache[$table]))
		{
			if (!isset($this->config[$table])) return false;
			$_cache[$table] = @include($this->cache_dir.$table.DS.'table.php');
			if (!$_cache[$table]) $this->set($table);
		}
		if (is_null($id)) return $_cache[$table];
		if (!isset($_cache[$table][$id]) && $this->config[$table]['rowcache'])
		{
			$_cache[$table][$id] = @include($this->cache_dir.$table.DS.$id.'.php');
			if (!$_cache[$table][$id]) $this->set($table);
		}
		return is_null($field) ? $_cache[$table][$id] : (isset($_cache[$table][$id][$field]) ? $_cache[$table][$id][$field] : false);
	}

	function set($table = null)
	{
		if(is_null($table))
		{
			@set_time_limit(600);
			$this->_set_config();
			return array_map(array($this, 'set'), array_keys($this->config));
		}
		else
		{
			if (!isset($this->config[$table])) return false;
			$dir = $this->cache_dir.$table.DS;
			$this->_mkdir($dir);
			$primary = $this->config[$table]['primary'];
			if ($this->config[$table]['allcache'])
			{
				$data = $this->_data($table, $primary, $this->config[$table]['allfields']);
				$this->_write($dir.'table.php', $data);
			}
			if ($this->config[$table]['rowcache'])
			{
				$data = $this->_data($table, $primary, $this->config[$table]['rowfields']);
				foreach ($data as $k=>$v)
				{
					$this->_write($dir.$k.'.php', $v);
				}
			}
			return true;
		}
	}

	function is_cache($table)
	{
		return isset($this->config[$table]);
	}

	function clear($table = null)
	{
		import('helper.folder');
        $cache_dir = $this->cache_dir;
        if(!is_null($table))
		{
            $cache_dir .= $table.DS;
		}
        return folder::delete($cache_dir);
	}

	function _mkdir($dir)
	{
		if (is_dir($dir)) return true;
		import('helper.folder');
		return folder::create($dir);
	}

	private function _get_config()
	{
		$this->config = @include($this->cache_dir.'cache'.DS.'table.php');
		if (!$this->config) $this->_set_config();
	}

	private function _set_config()
	{
		$dir = $this->cache_dir.'cache'.DS;
		$this->_mkdir($dir);
		$this->config = $this->_data('cache', 'tablename');
		return $this->_write($dir.'table.php', $this->config);
	}

	private function _write($file, $array)
	{
		$data = "<?php\nreturn ".var_export($array, true).';';
		if (!write_file($file, $data)) throw new ct_exception("$file is not exists or not writable");
		return true;
	}

	private function _data($table, $primary, $fields = '*')
	{
		if ($fields !== '*' && strpos($fields, $primary) === false) $fields .= ','.$primary;
		$array = array();
		$db = & factory::db();
		$data = $db->select("SELECT $fields FROM #table_$table ORDER BY `$primary`");
		foreach ($data as $k=>$v)
		{
			$array[$v[$primary]] = $v;
		}
		return $array;
	}
}