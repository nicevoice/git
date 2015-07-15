<?php
class model_admin_digg extends model
{
 	public $digg, $sql_total;

 	function __construct()
 	{
 		parent::__construct();
 		
 		$this->_table = $this->db->options['prefix'].'digg';
 		$this->_primary = 'contentid';
 		$this->_fields = array('contentid','supports','againsts'); 
 		$this->_readonly = array('contentid');
 		$this->_create_autofill = array();
 		$this->_update_autofill = array();
 		$this->_filters_input = array();
 		$this->_filters_output = array();
 		$this->_validators = array();
 	}

 	function ls($where, $order = 'contentid DESC', $page, $pagesize)
 	{
		if (isset($where['catid']) && intval($where['catid']))
		{
			$r = subcategory($where['catid']);
			if(!empty($r))
			{
				$r = array_keys($r);
				array_push($r, $_GET['catid']);
				$condition[] = "`catid` IN (".implode_ids($r).")";
			}
			else
			{
				$condition[] = "`catid`='".$where['catid']."'";
			}
		}
		if (isset($where['modelid']) && intval($where['modelid']))	$condition[] = "`modelid`='".$where['modelid']."'";
		if (isset($where['published']) && $where['published']) 		$condition[] = where_mintime('published', $where['published']);
		if (isset($where['unpublishd']) && $where['unpublishd']) 	$condition[] = where_maxtime('published', $where['unpublishd']);
		if (isset($where['createdby']) && $where['createdby']) 		$condition[] = "`createdby`='".$where['createdby']."'";
		
 		if (is_array($condition)) $wheresql = implode(' AND ', $condition);
 		if ($wheresql) $wheresql = ' WHERE '.$wheresql;
 		$sql = "SELECT d.contentid,c.modelid,c.catid,c.url,c.title,c.created,c.createdby, d.supports, d.againsts, c.published
 		FROM #table_digg d LEFT JOIN #table_content c ON d.contentid = c.contentid $wheresql ORDER BY $order";  
		
 		$this->sql_total = "SELECT COUNT(*) as count FROM  #table_digg d LEFT JOIN #table_content c ON d.contentid = c.contentid $wheresql";
 		$data = $this->db->page($sql, $page, $pagesize);
		$data = $this->output($data);
 		return  $data;
 	}

	function output($data)
	{
		foreach ($data as $k => $value)
 		{
			$data[$k]['title'] = htmlspecialchars($value['title']);
 			$data[$k]['modelname'] = table('model',$value['modelid'],'name');
 			$data[$k]['catname'] = table('category',$value['catid'],'name');
 			$data[$k]['caturl'] = table('category',$value['catid'],'caturl');
 			$data[$k]['createdbyname'] =($value['createdby'])?table('member', $value['createdby'], 'username') : '';
 			$data[$k]['created'] = date('Y-m-d H:i:s', $value['created']);
 			$data[$k]['published'] = date('Y-m-d H:i:s', $value['published']);
 		}
		return $data;
	}
	
 	function total()
 	{
 		$total = $this->db->get($this->sql_total);
 		return  $total['count'];
 	}
 }