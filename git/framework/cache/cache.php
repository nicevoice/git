<?php

loader::register('cache_storage', dirname(__FILE__).DS.'storage.php');

class cache extends object
{
	private $_options, $_storage;

	function __construct($options)
	{
		$this->_options = & $options;
		$this->_options['caching'] = isset($options['caching']) ? $options['caching'] : true;
		$this->_options['storage'] = isset($options['storage']) ? $options['storage'] : 'file';
		$this->_storage = & $this->_get_storage();
	}

	public static function &get_instance($type = 'output', $options = array())
	{
		$type = strtolower(preg_replace('/[^A-Z0-9_\.-]/i', '', $type));
		$class = 'cache_'.$type;
		if(!class_exists($class))
		{
			$path = dirname(__FILE__).DS.'handler'.DS.$type.'.php';
			if (file_exists($path))
			{
				require_once($path);
			}
			else
			{
				throw new Exception('Unable to load Cache Handler: '.$type);
			}
		}
		$instance = new $class($options);
		return $instance;
	}

	public function set_caching($enabled)
	{
		$this->_options['caching'] = $enabled;
	}

	public function get($id)
	{
		return $this->_options['caching'] ? $this->_storage->get($id) : false;
	}

	public function set($id, $data, $ttl = 0)
	{
		return $this->_options['caching'] ? $this->_storage->set($id, $data, $ttl) : false;
	}

	public function rm($id)
	{
        return $this->_storage->rm($id);
	}
	
	public function clear()
	{
        return $this->_storage->clear();
	}

	public function gc()
	{
        return $this->_storage->gc();
	}

	private function &_get_storage()
	{
		if (is_a($this->_storage, 'cache_storage'))
		{
			return $this->_storage;
		}
		$this->_storage = & cache_storage::get_instance($this->_options['storage'], $this->_options);
		return $this->_storage;
	}
}
