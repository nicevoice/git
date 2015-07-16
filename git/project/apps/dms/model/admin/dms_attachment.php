<?php
class model_admin_dms_attachment extends model implements SplSubject
{
    public $data = array();

    public $event, $where, $fields, $order, $page, $pagesize, $total;

	private $observers = array();

	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'dms_attachment';
		$this->_primary = 'attachmentid';
		$this->_fields = array('attachmentid', 'title', 'source', 'author', 'description', 'createtime', 'updatetime', 'status', 'expand', 'tags', 'size', 'ext', 'path', 'serverid');

		$this->_readonly = array();
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array('title'=>array('not_empty'=>array('标题不得为空')));
	}

	public function add($post)
	{
		$post	= $this->filter_array($post, array('title', 'source', 'author', 'description', 'tags', 'expand', 'size', 'ext', 'path'));
		$this->_add_tags($post['tags']);
		$post['serverid']	= 1;
		$post['createtime']	= TIME;
		$post['updatetime']	= TIME;
		$post['status']		= 1;
		$post['appid']		= 0;
		$post['expand']		= json_encode($post['expand']);
		$id = $this->insert($post);
		return $id;
	}

	public function remove($id)
	{
		$data	= array('status' => 0);
		return $this->update($data, $id, 1);
	}

	public function search($where, $page, $pagesize)
	{
		$search	= Loader::model('dms_search_attachment');
		$where['page']		= $page;
		$where['pagesize']	= $pagesize;
		$where['createtime_start']	= is_int($where['createtime_start']) ? $where['createtime_start'] : strtotime($where['createtime_start']);
		$where['createtime_end']	= is_int($where['createtime_end']) ? $where['createtime_end'] : strtotime($where['createtime_end']);
		$where['updatetime_start']	= is_int($where['updatetime_start']) ? $where['updatetime_start'] : strtotime($where['updatetime_start']);
		$where['updatetime_end']	= is_int($where['updatetime_end']) ? $where['updatetime_end'] : strtotime($where['updatetime_end']);
		$data	= $search->page($where, 'attachment');
		foreach ($data['data'] as &$item)
		{
			$item['shorttitle']	= str_cut($item['title'], 80);
			$item['createtime']	= date('Y-m-d H:i:s', $item['createtime']);
			$item['updatetime']	= date('Y-m-d H:i:s', $item['updatetime']);
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
                    $_data = array();
			foreach ($data as & $item)
			{
				$_data[] = $this->_after_select($item, false);
			}
                        $data = $_data;
			return $data;
		}
		$status	= array(
			'1'=>'正常',
			'9'=>'上传中'
		);
		$data['shorttitle'] = str_cut($data['title'], 80);
		$data['status']		= $status[$data['status']];
		$data['size']		= size_format($data['size']);
		if ($status == 9)
		{
			$data['url'] = 'javascript:;';
			$data['path'] = '上传中';
		}
		else
		{
			$data['url']	= $this->_path2url($data['path'], $data['serverid']);
			if (strlen($data['path']) > 30)
			{
				$data['path'] = str_cutword($data['path'], 22).substr($data['path'], -8);
			}
		}
		if (file_exists(IMG_PATH."images/ext/$data[ext].gif"))
		{
			$data['exticon'] = '<img src="'.IMG_URL.'images/ext/'.$data['ext'].'.gif" alt="'.$data['ext'].'" title="'.$data['ext'].'" />';
		}
		else
		{
			$data['exticon'] = '';
		}
                return $data;
	}

	/**
	 * 将路径转换为URL
	 *
	 * @param string path	路径
	 * @param int serverid	所存储的服务器ID
	 * @return string URL
	 */
	private function _path2url($path, $serverid)
	{
		$server		= $this->db->get("SELECT `name`, `url` FROM ".$this->db->options['prefix']."dms_server WHERE `serverid` = $serverid LIMIT 1");
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