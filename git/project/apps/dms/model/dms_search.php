<?php
class model_dms_search extends model
{
	public $cl, $data, $searchIndex, $mainIndex;
	public $maxLimit = 1000;
	public $status = true;
	
	protected $observers = array();
	protected $matchMode;
	protected $total;
	protected $keywords;
	protected static $serverConfig;
	
	public function __construct()
	{
		parent::__construct();
		if(class_exists('SphinxClient'))
		{
			$this->cl = new SphinxClient();
		}
		else
		{
			$this->cl = loader::lib('SphinxClient');
		}
		$setting = setting('dms');
//		if(!$setting['status'])
//		{
//			$this->error = '系统关闭了联合搜索服务';
//			$this->status = false;
//		}
//		else
//		{
//			$this->status = $this->testServer($setting['search_host'],$setting['search_port']);
//		}
		$this->status = $this->testServer($setting['search_host'],$setting['search_port']);
		
		$this->searchIndex = '';
		$this->cl->SetServer($setting['search_host'],$setting['search_port']);
		$this->cl->SetRankingMode(SPH_RANK_PROXIMITY_BM25);
		$this->cl->SetArrayResult(TRUE);
		$this->cl->SetMaxQueryTime(5000); //超时设为5秒
	}
	
	/**
	 * 实时更新索引中的记录
	 */
	public function update($id, $data = array())
	{
		$id = intval($id);
		if (empty($data))
		{
			return false;
		}
		else {
			foreach ($data as $k=>$v)
			{
				$attrs[] = $k;
				$values[$id][] = $v;
			}
		}
		try {
			$this->updateIndex($this->searchIndex, $attrs, $values);
		} catch (Exception $e) { }
		return true;
	}
	
	/**
	 * 删除时，标记索引中的记录已被删除
	 */
	public function delete($id)
	{
		$id = intval($id);
		try {
			$this->updateIndex($this->searchIndex, array("isdeleted"), array($id=>array(1)));
		} catch (Exception $e) { }
		return true;
	}
	
	/**
	 * 私有方法，调用sphinx接口设置属性
	 */
	public function updateIndex($index, $attr, $value)
	{
		if (empty($index))
		{
			return false;
		}
		if (strpos($index, ' '))
		{
			$indexArr = explode(' ', $index);
		}
		else
		{
			$indexArr = array($index);
		}
		$index = '';
		foreach ($indexArr as $index)
		{
			$this->cl->UpdateAttributes($index, $attr, $value);
		}
		return true;
	}

	public function getTotal()
	{
		return $this->total;
	}
	
	protected function buildKeywords()
	{
		$keywords = $this->cl->buildKeywords($this->keywords, $this->mainIndex, false);
		$query = array();
		foreach ($keywords as $value)
		{
			if(mb_strlen($value["tokenized"],'utf-8') < 2) continue;
			$query[] = $value["tokenized"];
		}
		if(empty($query))
		{
			$query[] = $this->keywords;
		}
		$this->keywords= implode("|", $query);
		return $this;
	}
	
		
	protected function excerpts($array, $index, $words, $opt=array())
	{
		$opts = array(
			"before_match"		=> '<span class="keyword">',
			"after_match"		=> "</span>",
			"chunk_separator"	=> "...",
			"limit"				=> 200,
			"around"			=> 18,
			"exact_phrase"		=> 0
		);
		if(!empty($opt))
		{
			foreach($opt as $key => $value)
			{
				$opts[$key] = $opt[$key];
			}
		}
		return $this->cl->BuildExcerpts($array, $index, $words, $opts);
	}

	protected function setFilter($q)
	{
		if(!empty($q['before']) && !empty($q['after']) && $q['before'] <$q['after'])
		{
			$this->cl->SetFilterRange($q['dateField'],$q['before'],$q['after']);
		}
		$this->cl->SetFilter('isdeleted',array(0));  //选取未删除的。
		return $this;
	}

	protected function SetFieldWeights($array)
	{
		if(is_array($array))
		{
			$this->cl->SetFieldWeights($array);
		}
		return $this;
	}
	
	protected function setMatchMode($mode)
	{
		if($mode == 'PHRASE')
		{
			$this->matchMode = SPH_MATCH_PHRASE;
		}
		elseif ($mode == 'ALL')
		{
			$this->matchMode = SPH_MATCH_ALL;
		}
		elseif ($mode == 'ANY')
		{
			$this->matchMode = SPH_MATCH_ANY;
		}
		else
		{
			$this->matchMode = SPH_MATCH_EXTENDED2;
		}
		
		$this->cl->SetMatchMode($this->matchMode);
		return $this;
	}
	
	protected function setSortMode($orderby = 'REL', $asc = 'DESC')
	{
		if($orderby == 'REL')
		{
			$this->cl->SetSortMode(SPH_SORT_RELEVANCE);
		}
		else 
		{
			$this->cl->SetSortMode(($asc == 'DESC' ? SPH_SORT_ATTR_DESC : SPH_SORT_ATTR_ASC), $orderby);
		}
		return $this;
	}
	
	protected function setWhere($field, $value)
	{
		if (!is_array($value))
		{
			$value = array($value);
		}
		$this->cl->setFilter($field, $value);
		return $this;
	}
	
	protected function testServer($host,$port)
	{
		$fp = @fsockopen($host, $port, $errno, $errstr, 2);
		if(!$fp) {
			$this->error = '搜索服务连接测试失败';
			return false;
		}
		return true;
	}
	
	protected function setLimits($page = 1, $limit = 10)
	{
		$page = max(1, intval($page));
		$offset = ($page - 1) * $limit;
		$this->cl->setLimits($offset, $limit, $this->maxLimit);
		return $this;
	}
	
	protected function sortList($ids,$array,$key = '')
	{
		$return  = array();
		foreach($ids as $id)
		{
			$return[$id] = array();
		}
		foreach ($array as $value)
		{
			$return[$value[$key]] = $value;
		}
		return $return;
	}

}