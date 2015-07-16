<?php

abstract class cache_storage
{
	const SEPARATOR = ':';
	protected $settings = array();

	function __construct($settings)
	{
		$this->settings = $settings;
		$this->settings['prefix'] .= self::SEPARATOR;
	}

	static function &get_instance($handler = 'file', $options = array())
	{
		$handler = strtolower(preg_replace('/[^A-Z0-9_\.-]/i', '', $handler));
		$class   = 'cache_storage_'.$handler;
		if(!class_exists($class))
		{
			$path = dirname(__FILE__).DS.'storage'.DS.$handler.'.php';
			if (file_exists($path) )
			{
				require_once($path);
			}
			else
			{
				throw new Exception('Unable to load Cache Storage: '.$handler);
			}
		}
		$return = new $class($options);
		return $return;
	}
	
	abstract public function set($key, $value, $ttl = 0);

	abstract public function get($key);

	abstract public function rm($key);

	abstract public function clear();
}