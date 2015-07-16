<?php
class model_content_rss extends model
{
	public $category;

	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'content';
		$this->_primary = 'contentid';
		$this->_fields = array('contentid', 'catid', 'modelid', 'title', 'color', 'thumb', 'tags', 'sourceid', 'url', 'weight', 'status', 'created', 'createdby', 'published', 'publishedby', 'unpublished', 'unpublishedby', 'modified', 'modifiedby', 'checked', 'checkedby', 'locked', 'lockedby', 'noted', 'notedby', 'note', 'workflow_step', 'workflow_roleid', 'iscontribute', 'allowcomment', 'comments', 'pv', 'related');
		$this->_readonly = array();
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();
		$this->category = table('category');
	}
	
	function ls($catid, $count = 10)
	{
		$catid = intval($catid);
		if (!isset($this->category[$catid])) return array();

		$where = '`status`=6';
		$where  .= $this->category[$catid]['childids'] ? " AND `catid` IN (".$this->category[$catid]['childids'].")" : " AND `catid`=$catid";
		return $this->select($where, '*', '`published` desc', $count, 0);
	}
	
	function ls_rss($catid, $count = 35, $weight = null)
	{
		$catid = intval($catid);
		$where = '`status`=6';

		if (isset($this->category[$catid]))
		{
			$where  .= $this->category[$catid]['childids'] ? " AND `catid` IN (".$this->category[$catid]['childids'].")" : " AND `catid`=$catid";
		}
		
		if ($weight)
		{
			if ($weight['min']) $where .= ' AND weight>='.$weight['min'];
			if ($weight['max']) $where .= ' AND weight<='.$weight['max'];
		}
		return $this->select($where, '*',  '`published` desc', $count, 0);
	}
}