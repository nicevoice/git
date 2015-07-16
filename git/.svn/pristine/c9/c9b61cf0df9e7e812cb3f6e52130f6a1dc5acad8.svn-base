<?php

class cache_callback extends cache
{
	function call()
	{
		$args		= func_get_args();
		$callback	= array_shift($args);
		return $this->get($callback, $args);
	}

	function get($callback, $args, $id = false)
	{
		if (is_array($callback))
		{
			// We have a standard php callback array -- do nothing
		}
		elseif (strstr( $callback, '::' ))
		{
			// This is shorthand for a static method callback classname::methodname
			list( $class, $method ) = explode( '::', $callback );
			$callback = array( trim($class), trim($method) );
		}
		elseif (strstr($callback, '->'))
		{
			list( $object_123456789, $method ) = explode('->', $callback);
			global $$object_123456789;
			$callback = array($$object_123456789, $method);
		}
		else
		{
			// We have just a standard function -- do nothing
		}

		if (!$id)
		{
			// Generate an ID
			$id = $this->_makeId($callback, $args);
		}

		$data = parent::get($id);
		if ($data !== false)
		{
			$cached = unserialize($data);
			$output = $cached['output'];
			$result = $cached['result'];
		}
		else
		{
			ob_start();
			ob_implicit_flush(false);
			$result = call_user_func_array($callback, $args);
			$output = ob_get_contents();
			ob_end_clean();
			$cached = array();
			$cached['output'] = $output;
			$cached['result'] = $result;
			$this->set($id, serialize($cached));
		}
		echo $output;
		return $result;
	}

	function _makeId($callback, $args)
	{
		if(is_array($callback) && is_object($callback[0]))
		{
			$vars = get_object_vars($callback[0]);
			$vars[] = strtolower(get_class($callback[0]));
			$callback[0] = $vars;
		}
		return md5(serialize(array($callback, $args)));
	}
}
