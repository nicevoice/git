<?php
class model_admin_dms_picture_group extends model implements SplSubject
{
    public $data = array();

    public $event, $where, $fields, $order, $page, $pagesize, $total;

	private $observers = array();

	private $picture;

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

		$this->picture	= loader::model('admin/dms_picture');
	}
	
	public function page($page, $pagesize)
	{
		$page	= intval($page);
		$page	= max(1, $page);
		$page	= min($page, 25);
		$total	= $this->count();
		$data	= parent::page(null, '*', 'groupid desc', $page, $pagesize);
		foreach ($data as &$item)
		{
			if (!$coverid = $item['cover'])
			{
				$coverid	= array_shift(explode(',', $item['pictures']));
			}
			$item['cover']	= $this->picture->get($coverid);
			$item['cover']	= $item['cover']['url'];
			$item['shorttitle']	= str_cut($item['title'], 14);
		}
		return array('total'=>$total, 'data'=>$data);
	}

	public function add($post)
	{
		$post['createtime'] = TIME;
		$post['updatetime'] = TIME;
		$post['status']		= 1;
		$this->_add_tags($post['tags']);
		$post['pictures']	= $this->_add_pics($post['pictures'], $post['title']);
		$post['expand']		= json_encode($post['expand']);
		$post['appid']		= 0;
		if ($gid = $this->insert($post))
		{
			return array('state'=>true, 'data'=>$gid);	
		}
		else
		{
			return array('state'=>false, 'error'=>$this->error());
		}
	}

	public function delete($id)
	{
		if (is_array($id))
		{
			$result	= true;
			foreach ($id as $item)
			{
				$result	&= $this->delete($item);
			}
			return $result;
		}
		if (!$id = intval($id))
		{
			return false;
		}
		if ($query = $this->get("groupid=$id", 'pictures'))
		{
			$pictures	= explode(',', $query['pictures']);
		}
		foreach ($pictures as $pictureid)
		{
			$this->picture->delete(array('id'=>$pictureid));
		}
		$this->_edit_tags(explode(' ', $query['tags']), array());
		$r	= parent::delete("groupid=$id");
		$search	= Loader::model('dms_search_picture_group');
		$search->delete($id);
		return $r;
	}

	public function remove($id)
	{
		$data	= array('status' => 0);
		$where	= "groupid = $id";
		$limit	= 1;
		return $this->update($data, $where, $limit);
	}

	public function search($where, $page, $pagesize)
	{
		$search	= Loader::model('dms_search_picture_group');
		$where['page']		= $page;
		$where['pagesize']	= $pagesize;
		$data	= $search->page($where, 'picture_group');
		foreach ($data['data'] as &$item)
		{
			$item['shorttitle']	= str_cut($item['title'], 14);
		}
		return $data;
	}

	public function get_pic_list($id)
	{
		$picture_group	= $this->get("groupid=$id");
		if (!$picture_group['pictures'])
		{
			return array();
		}
		$pics	= explode(',', $picture_group['pictures']);
		$data	= array();
		foreach ($pics as $pictureid)
		{
			if (!$info = $this->picture->get($pictureid))
			{
				continue;
			}
			$info['short_title']	= str_cutword($info['title'], 7);
			$data[]	= $info;
		}
		return $data;
	}

	private function _add_tags($tags)
	{
		if ($tags)
		{
			$tag	= Loader::model('admin/dms_tag');
			$tag->set($tags);
		}
	}

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

	private function _add_pics($pics, $title)
	{
		$rst	= array();
		foreach ($pics as $item)
		{
			$item['title']	= $title;
			$rst[]	= $this->picture->add($item);
		}
		return implode(',', $rst);
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