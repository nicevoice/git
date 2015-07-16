<?php
class model_admin_dms_picture extends model implements SplSubject
{
    public $data = array();

    public $event, $where, $fields, $order, $page, $pagesize, $total;

	private $observers = array();

	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'dms_picture';
		$this->_primary = 'pictureid';
		$this->_fields = array('pictureid', 'appid', 'title', 'source', 'author', 'description', 'createtime', 'updatetime', 'status', 'expand', 'tags', 'path', 'serverid');

		$this->_readonly = array();
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();
	}

	public function add($post)
	{
		$image	= $post['image'];
		$post	= $this->filter_array($post, array('title', 'source', 'author', 'description', 'tags', 'expand'));
		$this->_add_tags($post['tags']);
		$post['path']		= str_replace(UPLOAD_URL, '' , $image);
		$post['serverid']	= 1;
		$post['createtime']	= TIME;
		$post['updatetime']	= TIME;
		$post['status']		= 1;
		$post['appid']		= 0;
		$post['expand']		= json_encode($post['expand']);
		$id = $this->insert($post);
		return $id;
	}

	public function edit($post, $id)
	{
		$image	= $post['image'];
		$old_tags	= explode(' ', $post['old_tags']);
		$post	= $this->filter_array($post, array('title', 'source', 'author', 'description', 'tags', 'expand'));
		$post['path']		= str_replace(UPLOAD_URL, '' , $image);
		$post['updatetime']	= TIME;
		$this->_edit_tags($old_tags, explode(' ', $post['tags']));
		return $this->update($post, $id);
	}

	public function remove($id)
	{
		$data	= array('status' => 0);
		$where	= "pictureid = $id";
		$limit	= 1;
		return $this->update($data, $where, $limit);
	}

	public function get($pictureid)
	{
		if (!$data = parent::get("pictureid=$pictureid and status>0"))
		{
			return false;
		}
		return $data;
	}

	public function page($page = 1, $pagesize = 20)
	{
		$r['total']	= $this->count();
		$r['data']	= parent::page('status>0', '*', 'updatetime desc', $page, $pagesize);
		return $r;
	}

	public function search($where, $page, $pagesize)
	{
		$search	= Loader::model('dms_search_picture');
		$where['page']		= $page;
		$where['pagesize']	= $pagesize;
		$data = $search->page($where, 'picture');
		return $data;
	}

	protected function _after_select(& $data, $multiple)
	{
		if (!$data)
		{
			return;
		}
		if ($multiple)
		{
                    $data = array();
			foreach ($data as & $item)
			{
				$_data[] = $this->_after_select($item, false);
			}
                        $data= $_data;
			return $data;
		}
		$data['url'] = $this->_path2url($data['path'], $data['serverid']);
		$data['short_title'] = str_cut($data['title'], 12);
                return $data;
	}

	private function _path2url($path, $serverid)
	{
		$server	= $this->db->get("SELECT `name`, `url` FROM ".$this->db->options['prefix']."dms_server WHERE `serverid` = $serverid LIMIT 1");
		return $server['url'].$path;
	}

	private function _add_tags($tags)
	{
		if ($tags)
		{
			$tag	= Loader::model('admin/dms_tag');
			$tag->set($tags);
		}
	}

	/**
	 * 编辑时同步修改标签表
	 *
	 * @param string old_tags 旧标签字段
	 * @param string new_tags 新标签字段
	 * @return void
	 */
	private function _edit_tags($old_tags, $new_tags)
	{
		$tag	= Loader::model('admin/dms_tag');
		$remove_tags	= array_diff($old_tags, $new_tags);
		$add_tags		= array_diff($new_tags, $old_tags);
		if ($add_tags)
		{
			$tag->set($add_tags);
		}
		if ($remove_tags)
		{
			$tag->remove($remove_tags);
		}
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