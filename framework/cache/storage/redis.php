<?php

class cache_storage_redis extends cache_storage
{
	protected $redis = NULL;

	function __construct($settings)
	{
		parent::__construct($settings);
		
		$this->redis = new Redis();
		$server = $this->settings['redis'];
		$port = isset($server['port']) ? $server['port'] : 6379;
		$rs = false;
		if (empty($server['persistent'])){
			$rs = $this->redis->connect($server['host'], $port);
		} else {
			$rs = $this->redis->pconnect($server['host'], $port);
		}
		if (!$rs)
		{
			throw new ct_exception('can not connect to memcache!');
		}
		$this->redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);
		$this->redis->setOption(Redis::OPT_PREFIX, $this->settings['prefix']);
	}

	function set($key, $value, $ttl = 0)
	{
		return $this->redis->set($key, $value, $ttl);
	}

	function get($key)
	{
		return $this->redis->get($key);
	}

	function rm($key)
	{
		return $this->redis->delete($key);
	}

	function clear()
	{
		return $this->redis->flushDB();
	}
}

//end