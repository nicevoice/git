<?php

class dbkv_storage_dba extends dbkv_storage
{
	function __construct($handler = 'flatfile')
	{
		$this->handler = $handler;
	}
	
	public function open($path, $mode = 'n')
	{
		$id = dba_open($path, $mode, $this->handler);
		if (!$id)
		{
			return false;
		}
		$this->id = $id;
		return $id;
	}

	public function popen($path, $mode = 'n')
	{
		$id = dba_popen($path, $mode, $this->handler);
		if (!$id)
		{
			return false;
		}
		$this->id = $id;
		return $id;
	}
	
	public function add($key, $value)
	{
		return dba_insert($key, $value, $this->id);
	}
	
	public function set($key, $value)
	{
		return dba_replace($key, $value, $this->id);
	}
	
	public function get($key)
	{
		return dba_fetch($key, $this->id);
	}
	
	public function rm($key)
	{
		return dba_delete($key, $this->id);
	}
	
	public function exists($key)
	{
		return dba_exists($key, $this->id);
	}
	
	public function close()
	{
		return dba_close($this->id);
	}
}