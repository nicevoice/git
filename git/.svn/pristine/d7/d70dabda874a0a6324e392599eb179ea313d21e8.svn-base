<?php
class model_admin_cdn_parameter extends model implements SplSubject 
{

	private $observers = array();

	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'cdn_parameter';
		$this->_primary = 'id';
		$this->_fields = array('id', 'cdnid', 'key', 'value');

		$this->_readonly = array();
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();
	}

	public function add($cdnid, $data)
	{
		foreach ($data as $key => $item)
		{
			if (!$this->insert(array('cdnid'=>$cdnid, 'key'=>$key, 'value'=>$item)))
			{
				return false;
			}
		}
		return true;
	}

	public function edit($cdnid, $data)
	{
		foreach ($data as $key => $value)
		{
			if (!$this->update(array('value'=>$value), "`cdnid`=$cdnid and `key`='$key'", 1))
			{
				return false;
			}
		}
		return true;
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