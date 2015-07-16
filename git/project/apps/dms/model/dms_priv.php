<?php
class model_dms_priv extends model implements SplSubject
{
    public $data = array();

    public $event, $where, $fields, $order, $page, $pagesize, $total;

	private $observers = array();

	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'dms_priv';
		$this->_primary = 'id';
		$this->_fields = array('id', 'source', 'target', 'priv');
		$this->_readonly = array();
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();
	}

	public function verify($source, $target, $priv)
	{
		if ($source == $target || $target == 0)
		{
			return true;
		}
		$p	= $this->get("source=$source and target=$target");
		if (empty($p['priv']))
		{
			return false;
		}
		// 按位与
		if ($p['priv'] & $priv)
		{
			return true;
		}
		return false;
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