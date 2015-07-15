<?php
class model_dms_attachment extends model implements SplSubject
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
		$file_size	= value(get_headers($post['url'], 1), 'Content-Length', 0);
		$queue_size	= get_setting('queue_size') * 1024 * 1024;
		$post['createtime'] = TIME;
		$post['updatetime'] = TIME;
		$post['ext']		= array_pop(explode('.', $post['url']));
		$post['size']		= $file_size;
		$post['appid']		= $_GET['appid'];
		if ($file_size > $queue_size)
		{	// 队列上传
			$post['path']	= $post['url'];
			$post['status'] = 9;	// 9代表加入下载队列
		}
		else
		{
			$post['path']	= $this->_save($post['url']);
			$post['status']	= 1;
		}
		$this->_add_tags($post['tags']);
		$post['expand']		= $post['expand'] ? json_encode($post['expand']) : '';
		$result	= $this->insert($post);
		return $result;
	}

	public function remove($post)
	{
		if (!$id = intval($post['id']))
		{
			return array('state' => false, 'error' => 'id不存在');
		}
		$tags	= explode(' ', $post['tags']);
		return $this->update(array('status' => 0), $id, 1);
	}

	/**
	 * 添加时更新标题表
	 *
	 * @param string tags
	 * @return 
	 */
	private function _add_tags($tags)
	{
		if ($tags)
		{
			$tag	= Loader::model('admin/dms_tag');
			$tag->set($tags);
		}
	}

	/**
	 * 将远程附件保存在本地
	 *
	 * @param string url 远程附件url
	 * @return 本地附件url
	 */
	private function _save($url)
	{
		import('helper.folder');
		$path	= get_setting('pic_local_path').date('Ymd').'/';
		$dir	= ROOT_PATH.$path;
		folder::create($dir);
		import('attachment.download');
		$download = new download($dir, get_setting('allowed_ext'));
		$file = $download->by_content($url);
		return str_replace(UPLOAD_URL, '', $file);
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
		$status	= array(
			'1'=>'正常',
			'9'=>'上传中'
		);
		$data['shorttitle'] = str_cut($data['title'], 80);
		$data['status']	= $status[$data['status']];
		$data['size']	= size_format($data['size']);
		$data['url']	= $this->_path2url($data['path'], $data['serverid']);
		if (file_exists(IMG_PATH."images/ext/$data[ext].gif"))
		{
			$data['exticon'] = '<img src="'.IMG_URL.'images/ext/'.$data['ext'].'.gif" alt="'.$data['ext'].'" title="'.$data['ext'].'" />';
		}
		else
		{
			$data['exticon'] = $data['ext'];
		}
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
