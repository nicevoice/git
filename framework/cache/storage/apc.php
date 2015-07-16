<?php

class cache_storage_apc extends cache_storage
{

	function __construct($settings)
	{
		parent::__construct($settings);
	}

	function set($key, $value, $ttl = 0)
    {
		return $ttl ? apc_store($this->settings['prefix'].$key, $value, time() + $ttl) : apc_store($this->settings['prefix'].$key, $value);
	}

	function get($key)
    {
		return apc_fetch($this->settings['prefix'].$key);
	}

	function rm($key)
    {
		return apc_delete($this->settings['prefix'].$key);
	}

	function clear()
    {
		return apc_clear_cache();
	}
}

//end