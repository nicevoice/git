<?php
class model_admin_cdn_type extends model implements SplSubject 
{

	private $observers = array();

	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'cdn_type';
		$this->_primary = 'tid';
		$this->_fields = array(
			'tid', 'name', 'parameter', 'type', 'status'
		);

		$this->_readonly = array();
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array('type'=>array('not_empty' =>array('未选择执行文件')));
	}

	public function ls()
	{
		return $this->select("status=1");
	}

	function add($data)
	{
		if ($data['para'])
		{
			$para	= array();
			foreach ($data['para'] as $item)
			{
				if (!$item['name'])
				{
					continue;
				}
				$para[$item['name']] = $item['info'];
			}
			$data['parameter']	= json_encode($para);
		}
		$data = $this->filter_array($data, array('name', 'type', 'parameter'));
		return $this->insert($data);
	}

	function edit($data, $tid)
	{
		if ($data['para'])
		{
			$para	= array();
			foreach ($data['para'] as $item)
			{
				if (!$item['name'])
				{
					continue;
				}
				$para[$item['name']] = $item['info'];
			}
			$data['parameter']	= json_encode($para);
		}
		else
		{
			$data['parameter']	= null;
		}
		$data = $this->filter_array($data, array('name', 'type', 'parameter'));
		return $this->update($data, "tid=$tid", 1);
	}

	public function excute($excute)
	{
		foreach ($excute as $item)
		{
			$type	= $this->get("tid=$item[tid]", "type");
			$type	= $type['type'];
			$cdn	= loader::lib($type, 'cdn');
			$r		= $cdn->run($item['parameter']);
			if ($r['state'] == 0)
			{
				return $r;
			}
		}
		return $r;
	}

	public function get_cdn_file()
	{
		$type	= glob(ROOT_PATH.'apps/cdn/lib/*.php');
		foreach ($type as $key => $item)
		{
			$item	= array_shift(explode('.', $item));
			$item	= array_pop(explode('/', $item));
			$type[$key]	= $item;
		}
		return $type;
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