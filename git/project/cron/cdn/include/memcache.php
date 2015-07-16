<?php
class myMemcache
{
	protected $memcache = NULL;
	protected $settings = NULL;

	function __construct($settings='')
	{
		if (empty($settings))
		{
			define('CACHE_PATH', dirname(__FILE__) .'/../../../data/cache/');
			$settings = require dirname(__FILE__) .'/../../../config/cache.php';
			$settings['memcache'] = require dirname(__FILE__) .'/../../../config/memcache.php';
			$settings['prefix'] .= '_cdnQueue_';
		}
		$this->settings = $settings;
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

