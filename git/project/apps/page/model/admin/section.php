<?php
class model_admin_section extends model
{
	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'section';
		$this->_primary = 'sectionid';
		$this->_fields = array('sectionid', 'pageid', 'type', 'name', 'origdata', 'data', 'url', 'method', 'args', 'template', 'output', 'width', 'rows', 'frequency', 'nextupdate', 'published', 'locked', 'lockedby', 'updated', 'updatedby', 'created', 'createdby', 'description');

		$this->_readonly = array('sectionid');
		$this->_create_autofill = array('createdby'=>$this->_userid, 'created'=>TIME, 'locked'=>0, 'lockedby'=>0, 'updated'=>0, 'updatedby'=>0, 'published'=>0, 'output'=>'html');
		$this->_update_autofill = array('updated'=>TIME, 'updatedby'=>$this->_userid);
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();
	}

	function get_section($pageid = null,$fields = '*')
	{
		if (is_null($pageid)) {
			$where = "pageid IS NULL";
		}
		else
		{
			$where = "pageid = $pageid";
		}
		$data = $this->select($where,$fields);
		return $data;
	}

	function add($data)
	{
		$data = $this->filter_array($data, array('pageid', 'type', 'name', 'origdata', 'data', 'url', 'method', 'args', 'template', 'output', 'width', 'rows', 'frequency', 'description'));
		return $this->insert($data);
	}

	function edit($sectionid, $data)
	{
		$data = $this->filter_array($data, array('name', 'origdata', 'data', 'rows', 'url', 'method', 'args', 'rows', 'template', 'output', 'width', 'rows', 'frequency', 'nextupdate', 'published', 'locked', 'lockedby',  'description'));

		return $this->update($data, $sectionid);
	}
	function published($sectionid,$data = array())
	{
	    $data['published'] = TIME;
	    return $this->_updateWithoutAuto($data, "sectionid = {$sectionid}");
	}
	function nextupdate($sectionid,$data)
	{
		return $this->_updateWithoutAuto($data, "sectionid = {$sectionid}");
	}
	function move($sectionid, $data)
	{
		return $this->_updateWithoutAuto($data, "sectionid = {$sectionid}");
	}

	function delete($sectionid)
	{
		return parent::delete(implode_ids($sectionid));
	}

	function ls()
	{
		return $this->select();
	}

	function search($pageid = null, $keywords = null, $page = 1, $pagesize = 20)
	{
		$where = "`type`='hand'";
		if ($pageid) $where .= " AND `pageid`=$pageid";
		if ($keywords) $where .= " AND `name` LIKE '%$keywords%'";
		$fields = '`sectionid`, `name`, `pageid`';
		$order = '`sectionid` ASC';
		$data = $this->page($where, $fields, $order, $page, $pagesize);
		$page = loader::model('admin/page','page');
		$uri = loader::lib('uri','system');
		foreach ($data as $k=>$r)
		{
			$p = $page->get($r['pageid'], '`name`, `path`');
			$data[$k]['pagename'] = $p['name'];
			$u = $uri->psn($p['path']);
			$data[$k]['pageurl'] = $u['url'];
		}
		return $data;
	}

	function search_total($pageid = null, $keywords = null)
	{
		$where = "`type`='hand'";
		if ($pageid) $where .= " AND `pageid`=$pageid";
		if ($keywords) $where .= " AND `name` LIKE '%$keywords%'";
		return $this->count($where);
	}

	function lock($sectionid,$sec)
	{
		$data = array('locked'=>(TIME+$sec), 'lockedby'=>$this->_userid);
		return $this->_updateWithoutAuto($data, "sectionid = {$sectionid}");
	}

	function unlock($sectionid)
	{
		$data = array('state'=>PAGE_UNLOCK);
		$data = array('locked' => 0, 'lockedby'=>0);
		return $this->_updateWithoutAuto($data, "sectionid = {$sectionid}");
	}
	protected function _updateWithoutAuto($data, $where)
	{
	    $sql = "UPDATE `$this->_table` SET `".implode('`=?,`', array_keys($data))."`=?";
		if ($where) $sql .= " WHERE $where ";
		return $this->db->update($sql, array_values($data));
	}
	function cron_publish()
	{
		return $this->select('frequency > 0 AND nextupdate <= '.TIME);
	}
	function unlock_sections($pageid)
	{
		if (!$pageid || !is_int($pageid))
		{
			return false;
		}

		$per_lock = TIME;
		return $this->_updateWithoutAuto(array('locked'=>0, 'lockedby'=> 0), " pageid = $pageid AND locked < $per_lock");
	}
}
