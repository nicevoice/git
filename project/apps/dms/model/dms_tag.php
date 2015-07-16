<?php
class model_dms_tag extends model implements SplSubject
{
    public $data = array();

    public $event, $where, $fields, $order, $page, $pagesize, $total;

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