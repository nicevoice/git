<?php

class cache_view extends cache
{
	function get(&$view, $method, $id = false)
	{
		global $mainframe;
		// Initialize variables
		$data = false;
		// If an id is not given generate it from the request
		if ($id == false)
		{
			$id = $this->_makeId($view, $method);
		}

		$data = parent::get($id);
		if ($data !== false) {
			$data		= unserialize($data);
			$document	= &factory::get_document();

			// Get the document head out of the cache.
			$document->set_headdata((isset($data['head'])) ? $data['head'] : array());

			// If the pathway buffer is set in the cache data, get it.
			if (isset($data['pathway']) && is_array($data['pathway']))
			{
				// Push the pathway data into the pathway object.
				$pathway = &$mainframe->getPathWay();
				$pathway->setPathway($data['pathway']);
			}

			// If a module buffer is set in the cache data, get it.
			if (isset($data['module']) && is_array($data['module']))
			{
				// Iterate through the module positions and push them into the document buffer.
				foreach ($data['module'] as $name => $contents)
				{
					$document->set_buffer($contents, 'module', $name);
				}
			}

			// Get the document body out of the cache.
			echo (isset($data['body'])) ? $data['body'] : null;
			return true;
		}

		/*
		 * No hit so we have to execute the view
		 */
		if (method_exists($view, $method))
		{
			$document = &factory::get_document();

			// Get the modules buffer before component execution.
			$buffer1 = $document->get_buffer();

			// Make sure the module buffer is an array.
			if (!isset($buffer1['module']) || !is_array($buffer1['module']))
			{
				$buffer1['module'] = array();
			}

			// Capture and echo output
			ob_start();
			ob_implicit_flush(false);
			$view->$method();
			$data = ob_get_contents();
			ob_end_clean();
			echo $data;

			$cached = array();

			// View body data
			$cached['body'] = $data;

			// Document head data
			$cached['head'] = $document->getHeadData();

			// Pathway data
			$pathway			= &$mainframe->getPathWay();
			$cached['pathway']	= $pathway->getPathway();

			// Get the module buffer after component execution.
			$buffer2 = $document->getBuffer();

			// Make sure the module buffer is an array.
			if (!isset($buffer2['module']) || !is_array($buffer2['module']))
			{
				$buffer2['module'] = array();
			}

			// Compare the second module buffer against the first buffer.
			$cached['module'] = array_diff_assoc($buffer2['module'], $buffer1['module']);

			// Store the cache data
			$this->store($id, serialize($cached));
		}
		return false;
	}
	
	function _makeId(&$view, $method)
	{
		return md5(serialize(array(request::get_uri(), get_class($view), $method)));
	}
}
