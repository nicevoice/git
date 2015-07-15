<?php
class model_dms_quote extends model implements SplSubject
{
	public $data = array();

	public $event, $where, $fields, $order, $page, $pagesize, $total;

	private $observers = array();

	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'dms_quote';
		$this->_primary = 'quoteid';
		$this->_fields = array('quoteid', 'target', 'modelid', 'appid', 'time', 'operator', 'status', 'disable');

		$this->_readonly = array();
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();
	}

	public function set($post)
	{
		$article = loader::model('dms_article');
		// model to modelid
		if (!empty($post['model']))
		{
			if ($model = get_model(null, $post['model']))
			{
				$post['modelid']	= $model['modelid'];	
			}
		}
		$post['appid']	= $_GET['appid'];
		$post['time']	= isset($post['time']) ? $post['time'] : TIME;
		$post	= $this->filter_array($post, array('target', 'appid', 'time', 'operator', 'modelid', 'status'));
		if ($result = (bool)$this->insert($post))
		{
			$article->add_quote($post['target']);
		}
		return $result;
	}

	public function get($post)
	{
		if (isset($post['quotes']) && count($post['quotes']))
		{
			return $this->get_array($post['quotes']);
		}
		// model to modelid
		if (!empty($post['model']))
		{
			if ($model = get_model(null, $post['model']))
			{
				$post['modelid']	= $model['modelid'];
			}
		}
		if (empty($post['modelid']) || empty($post['target']))
		{
			return false;
		}
		$where = "modelid=$post[modelid] AND target=$post[target]";
		$post['time_start']	= empty($post['time_start'])? 0 : intval($post['time_start']);
		$post['time_end']	= empty($post['time_end'])? 0 : intval($post['time_end']);
		if ($post['time_start'] > 0)
		{
			$where .= " AND time > $post[time_start]";
		}
		if ($post['time_end'] > $post['time_start'])
		{
			$where .= " AND time < $post[time_end]";
		}
		$post['page'] = empty($post['page']) ? 1 : intval($post['page']);
		$post['pagesize'] = empty($post['pagesize']) ? 30 : intval($post['pagesize']);
		$post['pagesize'] = $post['pagesize'] ? $post['pagesize'] : 30;
		$offset	= ((($post['page'] > 0) ? $post['page'] : 1) - 1) * $post['pagesize'];
		$data	= $this->select($where, '*', 'time desc', $post['pagesize'], $offset);
		$total	= $this->count($where);
		return array('total'=>$total, 'data'=>$data);
	}
	
	public function get_array($quotes)
	{
		$data = array();
		foreach ($quotes as $key=>$item)
		{
			if (!isset($item['target']) || (!isset($item['model']) && !isset($item['modelid'])))
			{
				continue;
			}
			$post = array();
			$post['target']	= $item['target'];
			if (!empty($item['model']))
			{
				$post['model'] = $item['model'];
			}
			if (!empty($item['modelid']))
			{
				$post['modelid'] = $item['modelid'];
			}
			if ($r = $this->get($post))
			{
				$data[$key] = $r;
			}
		}
		return $data;
	}

	public function update($post)
	{
		$article = loader::model('dms_article');
		// model to modelid
		if (!empty($post['model']))
		{
			if ($model = get_model(null, $post['model']))
			{
				$post['modelid']	= $model['modelid'];
			}
		}
		if (empty($post['target']) || empty($post['modelid']))
		{
			return false;
		}
		if (!$id = intval($post['target']))
		{
			return false;
		}
		if (!in_array($post['disable'], array(0, 1)))
		{
			return false;
		}
		$data	= $this->get(array('modelid'=>$post['modelid'], 'id'=>$id));
		if ($data['disable'] == 0 && ($post['disable'] == 1))
		{
			$article->remove_quote($id);
		}
		if ($data['disable'] == 1 && ($post['disable'] == 0))
		{
			$article->add_quote($id);
		}
		$post['time'] = isset($post['time']) ? $post['time'] : TIME;
		$post	= $this->filter_array($post, array('target', 'appid', 'time', 'operator', 'modelid', 'status', 'disable'));
		$result = (bool)$this->insert($post);
		return $result;
	}

	protected function _after_select(& $data, $multiple = false)
	{
		if ($multiple)
		{
			array_map(array($this, 'output'), & $data);
		}
		else
		{
			$this->output($data);
		}
	}

	protected function output(& $data)
    {
		$model	= get_model($data['modelid']);
		$app	= loader::model('dms_app');
		$data['model']	= $model['name'];
		$data['app']	= $data['appid'] ? value($app->get($data['appid']),'name', '') : 'dms';
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