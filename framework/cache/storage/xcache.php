<?php

class cache_storage_xcache extends cache_storage
{

	function __construct($settings)
	{
		parent::__construct($settings);
	}

	function set($key, $value, $ttl = 0)
    {
		return xcache_set($this->settings['prefix'].$key, $value, $ttl);
	}

	function get($key)
    {
    	$key = $this->settings['prefix'].$key;
		return xcache_isset($key) ? xcache_get($key) : FALSE;
	}

	function rm($key)
    {
		return xcache_unset($this->settings['prefix'].$key);
	}

	function clear()
    {
		return apc_clear_cache();
	}
}

//end