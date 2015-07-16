<?php
class model_admin_dms_priv extends model implements SplSubject
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

	public function set_enable($source, $target, $priv)
	{
		if ($data = $this->get("source=$source AND target=$target", 'id, priv'))
		{
			$priv	= $data['priv'] | $priv;
			return $this->update(array('priv'=>$priv), $data['id']);
		}
		return $this->insert(array('source'=>$source, 'target'=>$target, 'priv'=>$priv));
	}

	public function set_disable($source, $target, $priv)
	{
		if ($data = $this->get("source=$source AND target=$target", 'id, priv'))
		{
			$priv	= $data['priv'] & (7 - $priv);
			return $this->update(array('priv'=>$priv), $data['id']);
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