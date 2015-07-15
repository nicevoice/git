<?php
class model_dms_article extends model implements SplSubject
{
    public $data = array();

    public $event, $where, $fields, $order, $page, $pagesize, $total;

	private $observers = array();

	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'dms_article';
		$this->_primary = 'articleid';
		$this->_fields = array('articleid', 'appid', 'title', 'source', 'author', 'description', 'content', 'createtime', 'updatetime', 'status', 'expand', 'tags', 'quote');

		$this->_readonly = array();
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();
	}

	public function add($post)
	{
		if (!$post['title'])
		{
			return array('state' => false, 'error' => '标题不得为空');	
		}
		if (!$post['force'] && $existids = $this->_title_exist($post['title']))
		{
			return array('state' => false, 'error' => '标题已存在', 'id' => $existids);
		}
		
		$post['createtime']	= TIME;
		$post['updatetime']	= TIME;
		$post['status']		= 1;
		$post['expand']		= $post['expand'] ? json_encode($post['expand']) : '';
		$post['appid']		= $_GET['appid'];
		if ($result = $this->insert($post))
		{
			$tag	= loader::model('dms_tag');
			$tag->set($post['tags']);
			return array('state' => true, 'data' => $result);
		}
		else
		{
			return array('state' => false, 'error' => '添加失败');
		}
	}

	public function edit($post)
	{
		if (!$articleid = intval($post['id']))
		{
			return array('state' => false, 'error' => 'id不存在');
		}
		$old_tag	= explode(' ', $post['old_tags']);
		$post		= $this->filter_array($post, array('title', 'source', 'author', 'description', 'content', 'expand', 'tags'));
		if (!$post['title'])
		{
			return array('state' => false, 'error' => '标题不得为空');	
		}
		$post['updatetime']	= TIME;
		$post['expand']		= $post['expand'] ? json_encode($post['expand']) : '';
		$this->_edit_tags($old_tag, explode(' ', $post['tags']));
		if ($this->update($post, $articleid))
		{
			return array('state' => true, 'data' => '修改成功');
		}
		else
		{
			return array('state' => false, 'error' => '修改失败');
		}
	}

	public function get($post)
	{
		if (!$articleid = intval($post['id']))
		{
			return array('state' => false, 'error' => 'id不存在');
		}
		if ($data	= parent::get("articleid=$articleid"))
		{
			$data['expand']	= $data['expand'] ? json_decode($data['expand']) : '';
			return array('state' => true, 'data' => $data);
		}
		return array('state' => false, 'error' => '文章不存在');
	}

	public function delete($post)
	{
		if (!$articleid = intval($post['id']))
		{
			return array('state' => false, 'error' => 'id不存在');
		}
		$tags	= explode(' ', $post['tags']);
		$this->_edit_tags($tags, array());
		$r = parent::delete($articleid);
		$search	= Loader::model('dms_search_article');
		$search->delete($articleid, 'article');
		return $r;
	}

	public function add_quote($id)
	{
		$this->set_inc('quote', $id);
	}

	public function remove_quote($id)
	{
		$this->set_dec('quote', $id);
	}

	private function _title_exist($title)
	{
		$search		= loader::model('dms_search_article');
		$article	= $search->page(array('title' => $title), 'article');
		$arr		= array();
		if (!$article['state'] || $article['total'] = 0)
		{
			return 0;
		}
		foreach ($article['data'] as $item)
		{
			if ($item['title'] == $title)
			{
				$arr[]	= $item['articleid'];
			}
		}
		return count($arr) ? $arr : 0;
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