<?php
class model_dms_picture extends model implements SplSubject
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
		if (!$url = $post['url'])
		{
			return array('state'=>false, 'data'=>'地址无效');
		}
		if (!$post['path'] = $this->_save($url))
		{
			return array('state'=>false, 'data'=>'插入图片失败');
		}
		$post	= $this->filter_array($post, array('title', 'source', 'author', 'description', 'path', 'expand', 'tags'));
		$post['expand']		= $post['expand'] ? json_encode($post['expand']) : '';
		$post['createtime']	= $post['updatetime'] = TIME;
		$post['serverid']	= 1;	// 临时放入服务器1
		$post['appid']		= $_GET['appid'];
		$post['status']		= 1;
		if ($result = $this->insert($post))
		{
			$tag	= loader::model('dms_tag');
			$tag->set($post['tags']);
		}
		return array('state'=>true, 'data'=>$result);
	}

	public function edit($post)
	{
		if (!$pictureid = intval($post['id']))
		{
			return array('state'=>false, 'error'=>'ID不存在');
		}
		// 图片处理
		unset($post['path']);
		if (!empty($post['url']))
		{
			$post['path']	= $this->_save($url);
		}
		// 标签处理
		$old_tags	= explode(' ', $post['old_tags']);
		$this->_edit_tags($old_tag, explode(' ', $post['tags']));
		$post	= $this->filter_array($post, array('title', 'source', 'author', 'description', 'path', 'expand', 'tags'));
		$post['expand']		= $post['expand'] ? json_encode($post['expand']) : '';
		$post['updatetime'] = TIME;
		if ($this->update($post, $pictureid))
		{
			return array('state'=>true, 'data'=>'修改成功');
		}
		else
		{
			return array('state'=>false, 'error'=>'修改失败');
		}
	}

	public function delete($post)
	{
		if (!$pictureid = intval($post['id']))
		{
			return array('state'=>false, 'error'=>'ID不存在');
		}
		if (!$p = parent::get($pictureid))
		{
			return array('state'=>false, 'error'=>'图片不存在');
		}
		$this->_edit_tags(explode(' ', $p['tags']), array());
		$r	= parent::delete($pictureid);
		$search	= loader::model('dms_search_picture');
		$search->delete($picture);
		@unlink(UPLOAD_PATH.$p['path']);
		return $r;
	}

	public function get($post)
	{
		$pictureid	= $post['id'];
		if (!$data = parent::get("pictureid=$pictureid"))
		{
			return false;
		}
		$serverid	= $data['serverid'];
		$server		= $this->db->get("SELECT `name`, `url` FROM ".$this->db->options['prefix']."dms_server WHERE `serverid` = $serverid LIMIT 1");
		$data['url']	= $server['url'].$data['path'];
		$data['server']	= $server['name'];
		$data['expand']	= json_decode($data['expand']);
		unset($data['serverid'], $data['path']);
		return $data;
	}

	public function get_in($ids)
	{
		if (is_array($ids))
		{
			$ids	= implode(',', $ids);
		}
		$data	= parent::select("`pictureid` IN ($ids)");
		$urls	= array();
		foreach ($data as &$item)
		{
			$item['url']	= get_url($item['serverid'], $item['path']);
		}
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
			foreach ($data as & $item)
			{
				$this->_after_select(& $item, false);
			}
			return;
		}
		$data['url'] = $this->_path2url($data['path'], $data['serverid']);
		$data['short_title'] = str_cut($data['title'], 12);
	}

	private function _path2url($path, $serverid)
	{
		$server	= $this->db->get("SELECT `name`, `url` FROM ".$this->db->options['prefix']."dms_server WHERE `serverid` = $serverid LIMIT 1");
		return $server['url'].$path;
	}

	private function _save($url)
	{
		import('helper.folder');
		$path	= get_setting('pic_local_path').date('Ymd').'/';
		$dir	= ROOT_PATH.$path;
		folder::create($dir);
		import('attachment.download');
		$download = new download($dir, 'jpg|jpeg|gif|png|bmp');
		$file = $download->by_content($url);
		return str_replace(UPLOAD_URL, '', $file);
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