<?php
//搜索

class model_search extends model implements SplSubject
{
	public $cl, $data, $searchIndex;
	public $maxLimit = 1000;
	public $status = true;
	
	private $observers = array();
	private $matchMode;
	private $total;
	private $keywords;
	
	public function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'search';
		$this->_primary = 'contentid';
		$this->_fields = array('contentid', 'content');
		$this->_readonly = array();
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();
		
		if(class_exists('SphinxClient'))
		{
			$this->cl = new SphinxClient();
		}
		else
		{
			$this->cl = loader::lib('SphinxClient', 'search');
		}
		$setting = setting('search');
		if(!$setting['open'])
		{
			$this->error = '系统关闭了搜索服务';
			$this->status = false;
		}
		else
		{
			$this->status = $this->testServer($setting['host'],$setting['port']);
		}
		
		$indexs = empty($setting['addindex'])?$setting['mainindex']:$setting['mainindex'].' '.$setting['addindex'];
		$this->searchIndex = $indexs; 			//搜索的索引名称 'content|addcontent'
		$this->mainIndex = $setting['mainindex']; //搜索的主索引名称
		
		$this->cl->SetServer($setting['host'],$setting['port']);
		$this->cl->SetRankingMode(SPH_RANK_PROXIMITY_BM25);
		$this->cl->SetArrayResult(TRUE);
		$this->cl->SetMaxQueryTime(5000); //超时设为5秒
	}
	
	public function update($contentid, $content)
	{
		$contentid = intval($contentid);
		//处理更新
		$sql = 'SELECT f.contentid, f.content, s.catid, s.modelid, s.title, s.published, 0 AS isdeleted 
				FROM #table_search f
				LEFT JOIN #table_content s
				ON f.contentid=s.contentid
				WHERE s.status=6 AND s.contentid='.$contentid;
		if($r = $this->db->get($sql))
		{
			try {
				if($this->status) $this->updateIndex($this->mainIndex, array('catid', 'modelid', 'published', 'isdeleted'), array($contentid=>array(intval($r['catid']), intval($r['modelid']), intval($r['published']), intval($r['isdeleted']))));
			} catch (Exception $e) { }
		}
		return $this->db->update("REPLACE INTO `$this->_table` SET contentid=?, content=?", array($contentid, $content));
	}
	
	public function delete($contentid)
	{
		$contentid = intval($contentid);
		try {
			if($this->status ) $this->updateIndex($this->searchIndex,array("isdeleted"), array($contentid=>array(1)));
		} catch (Exception $e) { }
		return $this->db->delete("DELETE FROM `$this->_table` WHERE contentid=?", array($contentid));
	}
	
	public function updateIndex($index, $attr, $value)
	{
		return $this->cl->UpdateAttributes($index, $attr, $value);
	}
	
	public function page($q = array(), $type = 'all', $mode = '', $order = 'REL', $asc = 'DESC', $page, $pagesize)
	{
		$this->keywords = $q['wd'];
		$this->setMatchMode($mode);				//设置匹配模式
		$this->setSortMode($order, $asc);		//设置排序
		$this->setLimits($page, $pagesize);		//设置分页
		$this->setFilter($q);					//设置过滤 参数需要从前$q数组中传递过来。
		
		$this->event = 'before_search_'.$type;
		$this->notify();
		
		if($this->matchMode == SPH_MATCH_EXTENDED2)
		{
			$this->buildKeywords();
			if($q['title']) $this->keywords = '@title '.$this->keywords;
		}
		$result = $this->cl->query($this->keywords,$this->searchIndex);
		if ($result === false)
		{
			$this->error = $this->cl->GetLastError();
			return false;
		}
		elseif (is_array($result['matches']))
		{
			foreach($result['matches'] as $key => $value)
			{
				$ids[] = $value['id'];
			}
			
			$this->search($ids, $q['wd']);
			
			$this->event = 'after_search_'.$type;
			$this->notify();
			
			$this->total = intval($result['total_found']);
			return $this->data;
		}
		else
		{
			$this->total = 0;
			$this->error = 'not found';
			return false;
		}
	}
	
	private function buildKeywords()
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
	
	public function getTotal()
	{
		return $this->total;
	}
	
	function setFilter($q)
	{
		// 过滤分类
		if(!empty($q['catid']))
		{
			$this->cl->SetFilter('catid', explode(',',$q['catid']));
		}
		// 过滤时间范围
		if(!empty($q['before']) && !empty($q['after']) && $q['before'] <$q['after'])
		{
			$this->cl->SetFilterRange('published',$q['before'],$q['after']);
		}
		// 过滤权重范围
		if(!empty($q['weight']))
		{
			if(strpos($q['weight'],',') === FALSE)
			{
				$q_min = $q['weight'];
				$q_max = 100;
			}
			else
			{
				list($q_min,$q_max) = explode(',', $q['weight']);
			}
			$this->cl->SetFilterRange('weight',$q_min,$q_max);
		}
        // 过滤评论数范围
        if(!empty($q['comments']))
        {
            if(strpos($q['comments'],',') === FALSE)
            {
                $q_min = $q['comments'];
                $q_max = 65535;
            }
            else
            {
                list($q_min,$q_max) = explode(',', $q['comments']);
            }
            $this->cl->SetFilterRange('comments',$q_min,$q_max);
        }
		// 过滤未被标记删除的
		$this->cl->SetFilter('isdeleted',array(0));
		return $this;
	}
	
	function setMatchMode($mode)
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
	
	function setSortMode($orderby = 'REL', $asc = 'DESC')
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
	
	function setWhere($field, $value)
	{
		if (!is_array($value))
		{
			$value = array($value);
		}
		$this->cl->setFilter($field, $value);
		return $this;
	}
	
	function testServer($host,$port)
	{
		$fp = @fsockopen($host, $port, $errno, $errstr, 2);
		if(!$fp) {
			$this->error = '系统没有安装搜索服务';
			return false;
		}
		return true;
	}
	
	private function setLimits($page = 1, $limit = 10)
	{
		$page = max(1, intval($page));
		$offset = ($page - 1) * $limit;
		$this->cl->setLimits($offset, $limit, $this->maxLimit);
		return $this;
	}
	
	private function search($ids, $words)
	{
		$sql = 'SELECT f.contentid AS contentid, f.content, s.catid, s.modelid, s.title, s.thumb, s.url, s.published,s.pv,s.comments,s.topicid 
				FROM #table_search f 
				LEFT JOIN #table_content s 
				ON f.contentid = s.contentid 
				WHERE f.contentid IN('.implode_ids($ids).')';
		$this->data = $this->db->select($sql);
		$this->data = $this->sortList($ids, $this->data, 'contentid');
		$this->data = $this->makeExcerpts($ids, $this->data, $words, $this->mainIndex);
	}
	
	private function makeExcerpts($ids, $data, $words, $index)
	{
		foreach($data as $key => $value)
		{
			$title_array[$key]   = $data[$key]['title']   = strip_tags($value['title']);
			$content_array[$key] = $data[$key]['content'] = strip_tags($value['content']);
			$data[$key]['date'] = date('Y-m-d H:i:s',$value['published']);
			$data[$key]['type'] = table('model',$value['modelid'],'name'); 
			$channel = table('category',intval(table('category',$value['catid'],'parentids')));
			$data[$key]['catname'] = $channel['name'];
			$data[$key]['caturl'] = $channel['url'];
		}
		
		$title_array =  $this->excerpts($title_array, $index, $words);
		$content_array =  $this->excerpts($content_array, $index, $words);
		
		foreach($title_array as $key => $value)
		{
			$data[$ids[$key]]['title'] = $value;
		}
		
		foreach($content_array as $key => $value)
		{
			$data[$ids[$key]]['content'] = $value;
		}
		return $data;
	}
	
	private function sortList($ids,$array,$key = 'contentid')
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
	
	private function excerpts($array, $index, $words, $opt=array())
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