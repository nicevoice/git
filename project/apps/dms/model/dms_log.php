<?php
class model_dms_log extends model implements SplSubject
{
	public $data = array();

	public $event, $where, $fields, $order, $page, $pagesize, $total;

	private $observers = array();

	private $log;

	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'dms_log';
		$this->_primary = 'logid';
		$this->_fields = array('logid', 'appid', 'operator', 'modelid', 'target', 'action', 'data', 'time', 'ip');

		$this->_readonly = array();
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();
	}

	public function get($id)
	{
		$data = parent::get($id);
		$data['ip']		= is_numeric($data['ip']) ? long2ip($data['ip']) : $data['ip'];
		$data['model']	= value(get_model($data['modelid']), 'name', '其他');
		return $data;
	}

	public function set($action, $model, $target = 0, $post = null)
	{
		$model		= get_model(null, $model);
		$data['appid']		= $_GET['appid'];
		$data['operator']	= $_GET['operator'];
		$data['modelid']	= $model ? $model['modelid'] : 0;
		$data['target']		= $target;
		$data['action']		= $action;
		$data['data']		= json_encode($post);
		$data['time']		= TIME;
		$data['ip']			= ip2long(IP);

		$result	= $this->insert($data);
		return $result;
	}

	public function search($option)
	{
		// model to modelid
		if (!empty($option['model']))
		{
			if ($model = get_model(null, $option['model']))
			{
				$option['modelid']	= $model['modelid'];	
			}
		}
		$where	= array();
		$limit	= is_numeric($option['pagesize']) ? intval($option['pagesize']) : 30;
		$offset	= ((intval($option['page']) ? $option['page'] : 1) - 1 ) * $limit;
		// app to appid
		if (!empty($option['app']))
		{
			$apps	= loader::model('dms_app');
			$option['appid']	= array_pop($this->get("name=$option[app]", 'appid'));
		}
		// 转换时间
		if (!empty($option['time_start']))
		{
			$where[]	= " time > $option[time_start] ";
		}
		if (!empty($option['time_end']))
		{
			$where[]	= " time < $option[time_end] ";
		}
		// 格式化ip
		if (isset($option['ip']) && strpos($option['ip'], '.') !== false)
		{
			$option['ip'] = ip2long($option['ip']);
		}
		$option	= $this->filter_array($option, array( 'appid', 'operator', 'modelid', 'target', 'action', 'ip'));
		foreach ($option as $key=>$item)
		{
			if (!empty($item))
			{
				// 对于逗号分隔的值默认使用where in
				if (strpos($item, ','))
				{
					$where[]	= " $key in '$item' ";
				}
				else
				{
					$where[]	= " $key = '$item' ";
				}
			}
		}
		$where	= implode('and', $where);
		$data	= $this->select($where, 'logid, appid, operator, modelid, target, action, time, ip', 'time desc', $limit, $offset);
		$total	= $this->count($where);
		foreach ($data as &$item)
		{
			$item['ip']		= is_numeric($item['ip']) ? long2ip($item['ip']) : $item['ip'];
			$item['model']	= value(get_model($item['modelid']), 'name', '其他');
		}
		return array('state'=>true, 'data'=>$data, 'total'=>$total);
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