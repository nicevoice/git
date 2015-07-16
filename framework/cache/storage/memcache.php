<?php

class cache_storage_memcache extends cache_storage
{
	protected $memcache = NULL;

	function __construct($settings)
	{
		parent::__construct($settings);
		
		$this->memcache = new Memcache();
		foreach ($this->settings['memcache'] as $server)
		{
			$port = isset($server['port']) ? $server['port'] : 11211;
			if (!$this->memcache->addServer($server['host'], $port, isset($server['persistent']) ? $server['persistent'] : true))
			{
				throw new ct_exception('can not connect to memcache!');
			}
		}
	}

	function set($key, $value, $ttl = 0)
	{
		return $this->memcache->set($this->settings['prefix'].$key, $value, MEMCACHE_COMPRESSED, $ttl);
	}

	function get($key)
	{
		return $this->memcache->get($this->settings['prefix'].$key);
	}

	function rm($key)
	{
		return $this->memcache->delete($this->settings['prefix'].$key);
	}

	function clear()
	{
		return $this->memcache->flush();
	}
}

//end