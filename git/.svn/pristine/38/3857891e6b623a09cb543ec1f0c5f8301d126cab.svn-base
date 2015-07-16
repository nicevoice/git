<?php

class cache_storage_eaccelerator extends cache_storage
{

	function __construct($settings)
	{
		parent::__construct($settings);
	}

	function set($key, $value, $ttl = 0)
	{
		return $ttl ? eaccelerator_put($this->settings['prefix'].$key, $value, $ttl) : eaccelerator_put($this->settings['prefix'].$key, $value);
	}

	function get($key)
	{
		return eaccelerator_get($this->settings['prefix'].$key);
	}

	function rm($key)
	{
		return eaccelerator_rm($this->settings['prefix'].$key);
	}

	function clear()
	{
		$infos = eaccelerator_list_keys();
		if (is_array($infos))
		{
			foreach ($infos as $info)
			{
				if (FALSE !== strpos($info['name'], $this->settings['prefix']))
				{
					$key = 0 === strpos($info['name'], ':') ? substr($info['name'], 1) : $info['name'];
					if (!eaccelerator_rm($key))
					{
						return FALSE;
					}
				}
			}
		}
		return TRUE;
	}
}

//end