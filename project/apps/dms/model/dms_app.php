<?php
class model_dms_app extends model implements SplSubject 
{

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
	}
	// 验证
	public function verify($token, $domain, $ip)
	{
		$where	= "`key`='$token'";
		if (!$query = $this->get($where, 'appid, name, ip, priv'))
		{
			return array('state' => false, 'data' => '无效的应用请求');
		}
		$appid		= $query['appid'];
		$appname	= $query['name'];
		$allowip	= $query['ip'];
		if (strpos($allowip, $ip) === false)
		{
			return array('state' => false, 'data' => 'IP段无效');
		}
		return array('state' => true, 'data' => array('appid' => $appid, 'name' => $name));
	}

	public function priv_check($appid, $action)
	{
		if (!$data = $this->get($appid))
		{
			return false;
		}
		$priv	= explode('|', $data['priv']);
		return in_array($action, $priv);
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