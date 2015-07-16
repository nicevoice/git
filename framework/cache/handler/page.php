<?php

class cache_page extends cache
{
	function get($id = false)
	{
		// Initialize variables
		$data = false;

		// If an id is not given generate it from the request
		if ($id == false) $id = $this->_makeId();

		// If the etag matches the page id ... sent a no change header and exit : utilize browser cache
		if (!headers_sent() && isset($_SERVER['HTTP_IF_NONE_MATCH']))
		{
			$etag = stripslashes($_SERVER['HTTP_IF_NONE_MATCH']);
			if( $etag == $id)
			{
				$browserCache = isset($this->_options['browsercache']) ? $this->_options['browsercache'] : false;
				if ($browserCache) 
				{
					$this->_noChange();
				}
			}
		}

		// We got a cache hit... set the etag header and echo the page data
		$data = parent::get($id, $group);
		if ($data !== false)
		{
			$this->_setEtag($id);
			return $data;
		}

		// Set id and group placeholders
		$this->_id		= $id;
		return false;
	}

	function set()
	{
		// Get page data from JResponse body
		$data = response::get_body();

		// Get id and group and reset them placeholders
		$id = $this->_id;
		$this->_id = null;

		// Only attempt to store if page data exists
		if ($data)
		{
			return parent::set($id, $data);
		}
		return false;
	}

	function _makeId()
	{
		return md5(request::get_uri());
	}

	function _noChange()
	{
		// Send not modified header and exit gracefully
		header( 'HTTP/1.x 304 Not Modified', true );
        exit;
	}

	function _setEtag($etag)
	{
		response::set_header('ETag', $etag, true);
	}
}
