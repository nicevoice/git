<?php
class model_admin_link extends model implements SplSubject
{
	public $content, $catid, $modelid, $contentid, $data, $fields, $order, $action, $category, $filterword;
	private $observers = array();

	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'link';
		$this->_primary = 'contentid';
		$this->_fields = array('contentid', 'description');
		$this->_readonly = array('contentid');
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array('contentid'=>array('not_empty' =>array('内容ID不能为空'),
		                                              'is_numeric' =>array('内容ID必须是数字'),
		                                              'max_length' =>array(8, '内容ID不得超过8个字节'),
		                                             )
		                          );
        $this->content = loader::model('admin/content', 'system');
        $this->category = & $this->content->category;
        $this->modelid = modelid('link');
	}

	public function get($contentid)
	{
		$content	= $this->content->get($contentid, '*', 'get');
		$link		= parent::get($contentid);
		return $link ? array_merge($link, $content) : $content;
	}

	public function add($data)
	{
		$data['description']	= htmlspecialchars($data['description']);
		$this->data = $data;
		$this->event = 'before_add';
		$this->notify();
		$this->contentid = $this->content->add($this->data);
		if (!$this->contentid)
		{
			$this->error = $this->content->error();
			return false;
		}
		$this->data['contentid']	= $this->contentid;
		$this->data = $this->filter_array($this->data, $this->_fields);
		$result = $this->insert($this->data);
        if ($result)
        {
			$this->event = 'after_add';
			$this->notify();
			return $this->data['contentid'];
        }
		else
		{
			// link表添加失败,删除content表对应数据
			$this->content->delete($this->contentid);
			return false;
		}
	}

	public function edit($contentid, $data)
	{
		$this->contentid = intval($contentid);
		$data['description']	= htmlspecialchars($data['description']);
		$this->data = $data;

		$this->event = 'before_edit';
		$this->notify();

		if (!$this->content->edit($this->contentid, $this->data))
        {
			$this->error = $this->content->error();
			return false;
        }
        
        $this->data = $this->filter_array($this->data, $this->_fields);
        $result = $this->update($this->data, $this->contentid);
        if ($result)
        {
			$this->event = 'after_edit';
			$this->notify();
        }
        return $result;
	}

	public function delete($contentid)
	{
		parent::delete($contentid);
		return $this->content->delete($contentid);
	}

	public function move($contentid, $catid)
	{
		$contentid = id_format($contentid);
		if ($contentid === false)
		{
			$this->error = "$contentid 格式不正确";
			return false;
		}
		// 判断当前栏目是否支持此模型
		if (!$cate = value(table('category'), $catid))
		{
			$this->error = "栏目不存在";
			return false;
		}

		foreach (unserialize($cate['model']) as $key=>$item)
		{
			if (isset($item['show']) && $item['show'])
			{
				$model[] = $key;
			}
		}
		if (!in_array($this->modelid, $model))
		{
			$this->error	= '栏目不支持此模型内容';
			return false;
		}
		$result = $this->content->move($contentid, $catid);
		$this->error = $this->content->error;
		return $result;
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