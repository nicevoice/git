<?php
loader::import("model.dms_search");
class model_dms_search_picture_group extends model_dms_search
{

	public function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'dms_picture_group';
		$this->_primary = 'groupid';
		$this->_fields = array('groupid', 'title', 'source', 'author', 'description', 'createtime', 'updatetime', 'status', 'expand', 'pictures', 'tags', 'cover');
		$this->_readonly = array('groupid');
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();
	}
	
	public function page($post, $model)
	{
		$order			= 'REL';
		$page			= $post['page'] ? $post['page'] : 1;
		$pagesize		= $post['pagesize'] ? $post['pagesize'] : 10;
		$model_info		= get_model(null, $model);
		$select_index	= "$model_info[mainindex] $model_info[deltaindex]";

		// 前台查询时过滤无权限条目
		if (!empty($_GET['appid']))
		{
			$priv	= loader::model('dms_priv');
			$p		= $priv->select("source=$_GET[appid] and priv & 1", "target");
			$p		= call_user_func_array('array_merge_recursive', $p);
			$target	= array();
			$target[]	= $p['target'];
			$target[]	= 0;
			$target[]	= $_GET['appid'];
		}

		if ($post['keyword'])
		{
			$this->keywords	= $post['keyword'];
		}
		else
		{
			$q	= $this->filter_array($post, array('title', 'source', 'author', 'description', 'tags'));
			$keyword_arr	= array();
			foreach ($q as $key => $item)
			{
				$keyword_arr[]	= '@'.$key.' '.$item;
			}
			$this->keywords	= implode(' , ', $keyword_arr);
		}
		// createtime范围
		if ($createtime_start = $post['createtime_start'])
		{
			if ($createtime_start > 0 && $createtime_start < TIME)
			{
				$createtime_end	= $post['createtime_end'] ? $post['createtime_end'] : TIME;
				$this->cl->SetFilterRange('createtime', $createtime_start, $createtime_end);
			}
		}
		// updatetime范围
		if ($updatetime_start > 0 && $updatetime_start = $post['updatetime_start'])
		{
			if ($updatetime_start < TIME)
			{
				$updatetime_end	= $post['updatetime_end'] ? $post['updatetime_end'] : TIME;
				$this->cl->SetFilterRange('updatetime', $updatetime_start, $updatetime_end);
			}
		}
		//$this->setFilter();					//设置过滤, 已删除
		$this->setMatchMode('EXT');				//设置匹配模式
		$this->setSortMode($order, 'DESC');		//设置排序
		$this->setSortMode('createtime', 'DESC');
		$this->setLimits($page, $pagesize);		//设置分页
		//$this->setFilter($q);					//设置过滤 参数需要从前$q数组中传递过来。

		if (!empty($target))
		{
			$this->cl->setFilter('appid', $target);
		}
		
		if($this->matchMode == SPH_MATCH_EXTENDED2)
		{
			$this->buildKeywords();				//如果支持新的扩展模式搜索，产生分词
		}

		$result = $this->cl->query($this->keywords, $select_index);

		if ($result === false)
		{
			return array('state' => false, 'error' => array('info' => 'error query', 'code' => $this->cl->GetLastError()));
		}
		elseif (is_array($result['matches']))
		{
			foreach($result['matches'] as $key => $value)
			{
				$ids[] = $value['id'];
			}
			
			$this->search($ids, $q['wd']);
			$this->total = intval(count($this->data));
			return array('state' => true, 'total' => $this->total, 'data' => $this->data);
		}
		else
		{
			$this->total = 0;
			return array('state' => false, 'error' => array('info' => 'no result'));
		}
	}

	public function delete($id)
	{
		$model_info		= get_model(null, 'picture_group');
		$this->searchIndex	= trim("$model_info[mainindex] $model_info[deltaindex]");
		parent::delete($id);
	}

	private function search($ids, $words)
	{
		$where = 'groupid IN('.implode_ids($ids).')';
		$this->data = $this->select($where);
		$this->data = $this->sortList($ids, $this->data, $this->_primary);
		$this->data = $this->makeExcerpts($ids, $this->data, $words, $this->mainIndex);
	}
	
	private function makeExcerpts($ids, $data, $words, $index)
	{
		foreach ($data as &$item)
		{
			if (empty($item))
			{
				unset($item);
				continue;
			}
			$item['expand']		= json_decode($item['expand']);
			$item['pictures']	= $this->_id2url($item['pictures']);
			$item['cover']		= $item['cover'] ? $this->_id2url($item['cover']) : $item['pictures'][0];
		}
		$data = array_filter($data);
		// 高亮关键字
		$title_array =  $this->excerpts($title_array, $index, $words);
		
		foreach($title_array as $key => $value)
		{
			if (is_null($data[$ids]))
			{
				continue;
			}
			$data[$ids[$key]]['title'] = $value;
		}
		return $data;
	}

	private function _id2url($pictures)
	{
		$picture = loader::model('dms_picture');
		$arr = $picture->get_in($pictures);
		return $arr;
	}
}
