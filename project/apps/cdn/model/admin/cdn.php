<?php
class model_admin_cdn extends model implements SplSubject 
{

	private $observers = array();

	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'cdn';
		$this->_primary = 'cdnid';
		$this->_fields = array('cdnid', 'name', 'tid');

		$this->_readonly = array();
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();
	}

	public function add($data)
	{
		$data = $this->filter_array($data, array('name', 'tid'));
		return $this->insert($data);
	}

	public function edit($data, $cdnid)
	{
		$data = $this->filter_array($data, array('name', 'tid'));
		return $this->update($data, "cdnid=$cdnid", 1);
	}

	public function get_type($cdnids)
	{
		$result	= array();
		foreach ($cdnids as $cdnid)
		{
			$tid	= $this->get("cdnid=$cdnid", 'tid');
			$tid	= $tid['tid'];
			$db = & factory::db();
			$para	= $db->select("SELECT `key`,`value` FROM #table_cdn_parameter WHERE `cdnid`=$cdnid");
			foreach ($para as $item)
			{
				$parameter[$item['key']] = $item['value'];
			}
			$result[]	= array('tid' => $tid, 'parameter' => $parameter);
		}
		return $result;
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