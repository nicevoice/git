<?php
class model_admin_page extends model
{
	protected $tree;
	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'page';
		$this->_primary = 'pageid';
		$this->_fields = array('pageid', 'parentid', 'parentids', 'childids', 'name', 'template', 'path', 'url', 'frequency', 'nextpublish','published', 'updated', 'updatedby', 'created', 'createdby', 'sort');
		
		$this->_readonly = array('pageid');
		$this->_create_autofill = array('createdby'=>$this->_userid, 'created'=>TIME);
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();
		
		import('helper.tree');
		$this->tree = new tree('#table_page', 'pageid');
	}
	
	function get_child($pageid = null, $format = 'array')
	{
		$where = $pageid ? "parentid=$pageid" : "(parentid IS NULL OR parentid=0)";
		$data = $this->select($where);
		if ($format == 'array') return $data;
		$json = & factory::json();
		return $data ? $json->encode($data) : null;
	}
	

	function add($data)
	{
		$data = $this->filter_array($data, array('parentid', 'parentids', 'name', 'template', 'path', 'url', 'frequency', 'nextpublish', 'updated', 'updatedby', 'created', 'createdby', 'sort'));
		$data['createdby'] = $this->_userid;
		$data['created'] = TIME;
		$data = $this->tree->set($data);
		$this->update_cache();
		return $data;
	}

	function edit($pageid, $data)
	{
		$data = $this->filter_array($data, array('pageid', 'name', 'template', 'path', 'url', 'frequency', 'nextpublish', 'updated', 'updatedby', 'sort'));
		return $this->update($data, "pageid=$pageid");
	}
	
	function delete($pageid)
	{
		$data = $this->tree->rm($pageid);
		$this->update_cache();
		return $data;
	}

    function select($where = null, $fields = '*', $order = null, $limit = null, $offset = null, $data = array(), $multiple = true)
    {
        $order = $order ? $order : '`sort` DESC, `created` ASC';
        return parent::select($where, $fields, $order, $limit, $offset, $data, $multiple);
    }
	
	function ls()
	{
		return $this->select();
	}
	
	function childpages($pageid)
	{
		$where = $pageid ? "parentid = {$pageid}" : "parentid = NULL";
		return $this->select($where, '*');
	}
	
	function lock($pageid)
	{
		$data = array('state'=>PAGE_LOCK);
		$this->update($data, "pageid = {$pageid}");
	}
	
	function unlock($pageid)
	{
		$data = array('state'=>PAGE_UNLOCK);
		$this->update($data, "pageid = {$pageid}");
		$this->page->unlock();
	}
	
	function update_cache()
	{
		table_cache('page');
	}
	
	protected function _updateWithoutAuto($data, $where)
	{
	    $sql = "UPDATE `$this->_table` SET `".implode('`=?,`', array_keys($data))."`=?";
		if ($where) $sql .= " WHERE $where ";
		return $this->db->update($sql, array_values($data));
	}
	function published($pageid, $data = array())
	{
	    $data['published'] = TIME;
	    return $this->_updateWithoutAuto($data, "pageid = {$pageid}");
	}
	function nextpublish($page)
	{
		$data = array(
			'nextpublish'=>TIME + intval($page['frequency'])
		);
		$pageid = $page['pageid'];
		return $this->_updateWithoutAuto($data, "pageid = {$pageid}");
	}
	function cron_publish()
	{
		return $this->select('nextpublish > published AND nextpublish <= '.TIME);
	}
}