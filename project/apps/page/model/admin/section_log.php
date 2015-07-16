<?php
class model_admin_section_log extends model
{
	private $actions = array('add'=>'添加', 'delete'=>'删除', 'update'=>'更新', 'publish'=>'发布', 'edit'=>'修改属性', 'unknown'=>'未知', 'catch'=>'抓取');
	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'section_log';
		$this->_primary = 'logid';
		$this->_fields = array('logid', 'sectionid', 'action', 'createdby', 'created');
		$this->_readonly = array('logid');
		$this->_create_autofill = array('createdby'=>$this->_userid, 'created'=>TIME);
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array('sectionid'=>array('not_empty'=>array('sectionid不能为空')
		)
		);
	}

	function getByPageid($pageid, $from = 1, $page = 0, $num = 10)
	{
		$start = $page * $num;
		$from = strtotime(date('Y-m-d')) - ($from -1)*86400;
		$end = $from + 86400;
		$data = array();
		$sql = "SELECT pl.*, s.name FROM #table_section_log pl
			LEFT JOIN #table_section s ON pl.sectionid = s.sectionid
			WHERE s.pageid = $pageid AND pl.created > {$from} AND pl.created < {$end}
			LIMIT $start, $num";
		$query = $this->db->query($sql);
		foreach ($query as $value)
		{
			$value['sectionname'] = $value['name'];
			$value['action'] = $this->actions[$value['action']];
			$data[] = $value;
		}
		return $data;
	}
	
	function getNum($pageid, $from = 1)
	{
		$from = strtotime(date('Y-m-d')) - ($from -1)*86400;
		$end = $from + 86400;
		$sql = "SELECT count(*) num FROM #table_section_log pl
			LEFT JOIN #table_section s ON pl.sectionid = s.sectionid
			WHERE s.pageid = $pageid AND pl.created > {$from} AND pl.created < {$end} ";
		$query = $this->db->query($sql);
		foreach ($query as $value) 
		{
			return $value['num'];
		}
	}

	function add($data)
	{
		$data = $this->filter_array($data, array('sectionid', 'action'));
		return $this->insert($data);
	}

	function ls()
	{
		return $this->select();
	}
}