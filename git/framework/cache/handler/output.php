<?php

class cache_output extends cache
{
	function start($id)
	{
		// If we have data in cache use that...
		$data = $this->get($id);
		if ($data !== false)
		{
			echo $data;
			return true;
		}
		else
		{
			// Nothing in cache... lets start the output buffer and start collecting data for next time.
			ob_start();
			ob_implicit_flush(false);
			// Set id and group placeholders
			$this->_id = $id;
			return false;
		}
	}

	function end()
	{
		// Get data from output buffer and echo it
		$data = ob_get_contents();
		ob_end_clean();
		echo $data;
		// Get id and group and reset them placeholders
		$id	= $this->_id;
		$this->_id = null;
		// Get the storage handler and store the cached data
		$this->set($id, $data);
	}
}
