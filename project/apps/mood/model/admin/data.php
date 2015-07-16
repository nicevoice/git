<?php
class model_admin_data extends model
{
	private $total_sql;

	public function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'mood_data';
		$this->_primary = 'contentid';
		$this->_fields = array('');
		
		$this->_readonly = array('contentid');
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();
	}

	public function delete($contentid)
	{
		return parent::delete(implode_ids($contentid));
	}

	public function ls($get = null, $fields, $order = "contentid ASC", $page = 0, $pagesize = 20)
	{
		$where = null;
		if (isset($get['keywords']) && $get['keywords']) 		$where[] = where_keywords('title', $get['keywords']);
		if (isset($get['initial']) && $get['initial']) 			$where[] = "`initial`='".$get['initial']."'";
		if (isset($get['catid']) && intval($get['catid']))
		{
			$r = subcategory($get['catid']);
			if(!empty($r))
			{
				$r = array_keys($r);
				array_push($r, $get['catid']);
				$where[] = "`catid` IN (".implode_ids($r).")";
			}
			else
			{
				$where[] = "`catid`='".$get['catid']."'";
			}
		}
		
		if (isset($get['modelid']) && intval($get['modelid']))	$where[] = "`modelid`='".$get['modelid']."'";
		if (isset($get['published']) && $get['published']) 		$where[] = where_mintime('published', $get['published']);
		if (isset($get['unpublishd']) && $get['unpublishd']) 	$where[] = where_maxtime('published', $get['unpublishd']);
		if (isset($get['createdby']) && $get['createdby']) 		$where[] = "`createdby`='".$get['createdby']."'";
		
		$range = intval($get['range']) ? $get['range'] : null;
		if (is_numeric($range))
		{
			$limit = TIME - $range * 36800;
			$where[] = "`c`.`published`>$limit";
		}
		if (is_array($where))
		{
			$where = implode(' AND ', $where);
		}

		if ($where)
		{
			$this->total_sql = "SELECT count(*) as count FROM $this->_table md LEFT JOIN
				#table_content c ON md.contentid =c.contentid WHERE $where";
			$sql = "SELECT md.*,c.title,c.pv,c.url,c.created,c.published,c.catid,c.modelid FROM $this->_table md LEFT JOIN
				#table_content c ON md.contentid = c.contentid	WHERE ".$where." order by $order ";
		} else
		{
			$sql = "SELECT md.*,c.title,c.pv,c.url,c.created,c.published,c.catid,c.modelid FROM $this->_table md
				LEFT JOIN #table_content c ON md.contentid = c.contentid order by $order ";
			$this->total_sql = "SELECT count(*) as count FROM $this->_table md
				LEFT JOIN #table_content c ON md.contentid =c.contentid";
		}
		
		$result = $this->db->page($sql, $page, $pagesize);

		if (is_array($result))
			$this->_after_select($result, true);
		return $result;
	}

	public function total()
	{
		$r = $this->db->get($this->total_sql);
		return $r['count'];
	}

	public function page($where = null, $fields = '*', $order = null, $page = 1, $size = 20, $data = array())
	{
		return model::page($where = null, $fields = '*', $order = null, $page = 1, $size = 20, $data = array());
	}

	private function _alias($modelid)
	{
		return table('model', $modelid, 'alias');
	}

	protected function _after_select(&$result, $multiple){
		if (empty($result)) {
			return $result;
		}
		if ($multiple)
		{
			foreach ($result as $key=>$value)
			{
				$result[$key]['alias'] = isset($value[modelid])&&!empty($value[modelid])?$this->_alias($value[modelid]):"article";
				$result[$key]['title'] =  htmlspecialchars(str_cut($value['title'],40));
				$result[$key]['fulltitle'] = $value['title'];
				$result[$key]['published'] =  date('Y-m-d H:i:s', $value['published']);
				$result[$key]['updatetime'] = $value['updatetime'] ? date('Y-m-d H:i', $value['updatetime']) : '从未';
				
				$result[$key]['modelname'] = table('model',$value['modelid'],'name');
				$result[$key]['catname'] = table('category',$value['catid'],'name');
				$result[$key]['caturl'] = table('category',$value['catid'],'caturl');
				$result[$key]['createdbyname'] =($value['createdby'])?table('member', $value['createdby'], 'username') : '';
				$result[$key]['created'] = date('Y-m-d H:i:s', $value['created']);
			}
		}
		else
		{
			$result['title'] =  str_cut($value['title'],40) ;
			$result['fulltitle'] = $value['title'];
			$result['alias'] = $this->_alias($value['modelid']);
			$result['published'] = date('Y-m-d H:i', $value['published']);
			$result['updatetime'] = $value['updatetime'] ? date('Y-m-d H:i', $value['updatetime']) : '从未';
		}
	}
}