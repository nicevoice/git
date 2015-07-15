<?php
class model_admin_dms_app extends model implements SplSubject 
{

	public $apps;
	private $observers = array();

	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'dms_app';
		$this->_primary = 'appid';
		$this->_fields = array('appid', 'key', 'name', 'domain', 'ip', 'priv');

		$this->_readonly = array();
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();

		$this->apps	= table('dms_app');
	}

	// 添加app
	public function add($data)
	{
		$priv	= null;
		if (!empty($data['priv']))
		{
			$priv	= implode('|', $data['priv']);
		}
		$data = $this->filter_array($data, array('name', 'key', 'domain', 'ip'));
		$data['priv']	= $priv;
		$result	= $this->insert($data);
		table_cache('dms_app');
		return $result;
	}

	// 编辑app
	public function edit($data, $appid)
	{
		$priv	= null;
		if (!empty($data['priv']))
		{
			$priv	= implode('|', $data['priv']);
		}
		$data = $this->filter_array($data, array('name', 'key', 'domain', 'ip'));
		$data['priv']	= $priv;
		return $this->update($data, "appid=$appid", 1);
	}

	public function get($id)
	{
		if (!$data = $this->apps[$id])
		{
			return false;
		}
		$data['priv']	= explode('|', $data['priv']);
		return $data;
	}

	public function delete($where = null, $limit = null, $order = null, $data = array())
	{
		$result	= parent::delete($where, $limit, $order, $data);
		table_cache('dms_app');
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