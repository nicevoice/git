<?php
class model_admin_dms_tag extends model implements SplSubject 
{

	private $observers = array();

	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'dms_tag';
		$this->_primary = 'tagid';
		$this->_fields = array('tagid', 'name', 'total');

		$this->_readonly = array();
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();
	}

	public function set($tags)
	{
		(strpos($tags, ' ') !== false) && $tags = explode(' ', $tags);
		if (is_array($tags))
		{
			foreach ($tags as $item)
			{
				trim($item) && $this->set($item);
			}
		}
		else
		{
			if (!trim($tags))
			{
				return;
			}
			if ($q = $this->get($tags))
			{	// existed
				$this->update(array('total' => ($q['total'] + 1)), "tagid=$q[tagid]", 1);
			}
			else
			{
				$q = $this->insert(array('name' => $tags));
			}
		}
	}

	public function get($tag)
	{
		return parent::get("name='$tag'");
	}

	public function remove($tags)
	{
		(strpos($tags, ' ') !== false) && $tags = explode(' ', $tags);
		if (is_array($tags))
		{
			foreach ($tags as $item)
			{
				trim($item) && $this->remove($item);
			}
		}
		else
		{
			if (!$data = $this->get($tags))
			{
				return;
			}
			if ($data['total'] <= 1)
			{
				$this->delete("tagid=$data[tagid]");
			}
			else
			{
				$this->update(array('total'=>($data['total']-1)), "tagid=$data[tagid]");
			}
			return;
		}
	}

	public function page($post)
	{
		if (!empty($post['orderby']))
		{
			$order_arr = explode('|', $post['orderby'], 2);
			if (count($order_arr) < 2)
			{
				$order = "$order_arr[0] desc";
			}
			else
			{
				$order_arr[1] = in_array($order_arr[1], array('asc', 'desc')) ? $order_arr[1] : 'desc';
				$order = "$order_arr[0] $order_arr[1]";
			}
		}
		if (!$page = $post['page'])
		{
			$page = 1;
		}
		if (!$size = $post['pagesize'])
		{
			$size = 15;
		}
		if (!empty($post['kw']))
		{
			$where = "`name` LIKE '%$post[kw]%'";
		}
		return parent::page($where, '*', $order, $page, $size);
	}

	public function count($post)
	{
		if (!empty($post['kw']))
		{
			$where = "`name` LIKE '%$post[kw]%'";
		}
		return parent::count($where);
	}

	public function attach(SplObserver $observer)
	{
		$this->observers[] = $observer;
	}

	public function detach(SplObserver $observer)
	{
		if($index = array_search($observer, $this->observers, true)) unset($this->observers[$index]);
	}

	public function notify()
	{
		foreach ($this->observers as $observer)
		{
			$observer->update($this);
		}
	}
}