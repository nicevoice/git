<?php
class model_dms_picture_group extends model implements SplSubject
{
    public $data = array();

    public $event, $where, $fields, $order, $page, $pagesize, $total;

	private $observers = array();

	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'dms_picture_group';
		$this->_primary = 'groupid';
		$this->_fields = array('groupid', 'appid', 'title', 'source', 'author', 'createtime', 'updatetime', 'status', 'expand', 'pictures', 'tags', 'cover');

		$this->_readonly = array();
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();
	}

	public function add($post)
	{
		if (!$data = $post['pictures'])
		{
			return false;
		}
		$picture	= loader::model('dms_picture');
		$pics		= array();
		foreach ($data as $item)
		{
			if (empty($item['url']))
			{
				continue;
			}
			foreach (array('title', 'source', 'author', 'description', 'expand', 'tags') as $key)
			{
				$item[$key]	= empty($item[$key]) ? $post[$key] : $item[$key];
			}
			$r = $picture->add($item);
			if (isset($r) && $r['state'])
			{
				$pics[] = $r['data'];
			}
		}
		$pics	= implode(',', $pics);
		$post	= $this->filter_array($post, array('title', 'source', 'author', 'description', 'expand', 'tags', 'cover'));
		$post['pictures']	= $pics;
		$post['createtime'] = $post['updatetime'] = TIME;
		$post['expand']		= $post['expand'] ? json_encode($post['expand']) : '';
		$post['appid']		= $_GET['appid'];
		$post['status']		= 1;
		if ($result = $this->insert($post))
		{
			$tag	= loader::model('dms_tag');
			$tag->set($post['tags']);
		}
		return $result;
	}

	public function get($post)
	{
		$groupid	= $post['id'];
		if (!$data = parent::get("groupid=$groupid"))
		{
			return false;
		}
		$picture	= loader::model('dms_picture');
		$data['expand']		= $data['expand'] ? json_decode($data['expand']) : '';
		$data['pictures']	= $picture->get_in($data['pictures']);
		unset($data['serverid'], $data['path']);
		return $data;
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