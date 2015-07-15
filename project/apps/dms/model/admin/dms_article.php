<?php
class model_admin_dms_article extends model implements SplSubject
{
    public $data = array();

    public $event, $where, $fields, $order, $page, $pagesize, $total;

	private $observers = array();

	private $dms_app, $dms_quote;

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
		$this->_validators = array('title'=>array('not_empty'=>array('标题不得为空')));
	}

	public function page($page = 1, $pagesize = 20)
	{
		$page	= intval($page);
		$page	= max(1, $page);
		$page	= min($page, 25);
		$total	= $this->count();
		$data	= parent::page(null, '*', 'articleid desc', $page, $pagesize);
		return array('total'=>$total, 'data'=>$data);
	}

	public function add($post)
	{
		$post['createtime'] = TIME;
		$post['updatetime'] = TIME;
		$post['status']		= 1;
		$this->_add_tags($post['tags']);
		$post['expand']		= $post['expand'] ? json_encode($post['expand']) : '';
		$post['appid']		= 0;
		if ($aid = $this->insert($post))
		{
			return array('state'=>true, 'data'=>$aid);	
		}
		else
		{
			return array('state'=>false, 'error'=>$this->error());
		}
	}

	public function edit($aid, $post)
	{
		$old_tag	= explode(' ', $post['old_tags']);
		$post	= $this->filter_array($post, array('title', 'source', 'author', 'description', 'content', 'expand', 'tags'));
		$post['updatetime']	= TIME;
		$post['expand']		= $post['expand'] ? json_encode($post['expand']) : '';
		$this->_edit_tags($old_tag, explode(' ', $post['tags']));
		if ($this->update($post, "articleid=$aid", 1))
		{
			return array('state' => true);
		}
		else
		{
			return array('state' => false);
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
		if (!$data = $this->get($id))
		{
			return false;
		}
		$this->_edit_tags(explode(' ', $data['tags']), array());
		$r = parent::delete("articleid=$id");
		$search	= Loader::model('dms_search_article');
		$search->delete($id, 'article');
		return $r;
	}

	public function get($id)
	{
		$data	= parent::get("articleid=$id");
		$data['expand']	= $data['expand'] ? json_decode($data['expand']) : '';
		return $data;
	}

	public function search($where, $page, $pagesize)
	{
		$search	= Loader::model('dms_search_article');
		$where['page']		= $page;
		$where['pagesize']	= $pagesize;
		$where['createtime_start']	= is_int($where['createtime_start']) ? $where['createtime_start'] : strtotime($where['createtime_start']);
		$where['createtime_end']	= is_int($where['createtime_end']) ? $where['createtime_end'] : strtotime($where['createtime_end']);
		$where['updatetime_start']	= is_int($where['updatetime_start']) ? $where['updatetime_start'] : strtotime($where['updatetime_start']);
		$where['updatetime_end']	= is_int($where['updatetime_end']) ? $where['updatetime_end'] : strtotime($where['updatetime_end']);
		$data	= $search->page($where, 'article');
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
		$data['shorttitle']	= str_cut($data['title'], 80);
		if ($data['quote'])
		{
			$data['shorttitle'] .= '<a href="javascript:;" onclick="article.quoteInfo('.$data['articleid'].');" tips="被引用'.$data['quote'].'次" class="title_list"><img src="images/section.gif"></a>';
		}
		$data['createtime']	= date('Y-m-d H:i:s', $data['createtime']);
		$data['updatetime']	= date('Y-m-d H:i:s', $data['updatetime']);
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