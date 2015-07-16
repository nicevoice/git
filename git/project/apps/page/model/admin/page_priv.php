<?php
class model_admin_page_priv extends object 
{
	private $db, $table;
	
	function __construct()
	{
		$this->db = & factory::db();
		$this->table = '#table_page_priv';
	}
	
	function add($pageid, $userid)
	{
		if (!$userid)
		{
			$this->error = '该用户不存在';
			return false;
		}
		if ($this->exists($pageid, $userid))
		{
			$this->error = '该用户已经是本页面管理员';
			return false;
		}
		return $this->db->insert("INSERT INTO `$this->table`(`pageid`, `userid`) VALUES(?, ?)", array($pageid, $userid));
	}
	
	function delete($pageid, $userid)
	{
		return $this->db->delete("DELETE FROM `$this->table` WHERE `pageid`=? AND `userid`=?", array($pageid, $userid));
	}
	
	function ls($pageid = null, $userid = null)
	{
		$where = array();
		if ($pageid) $where[] = "`pageid`=$pageid";
		if ($userid) $where[] = "`userid`=$userid";
		$where = implode(' AND ', $where);
		$data = $this->db->select("SELECT * FROM `$this->table` WHERE $where");
		foreach ($data as $k=>$r)
		{
			$data[$k]['roleid'] = table('admin', $r['userid'], 'roleid');
			$data[$k]['rolename'] = table('role', $data[$k]['roleid'], 'name');
			$data[$k]['username'] = username($r['userid']);
		}
		return $data;
	}
	
	function ls_pageid($userid)
	{
		$pageids = array();
		$data = $this->db->select("SELECT `pageid` FROM `$this->table` WHERE `userid`=?", array($userid));
		foreach ($data as $r)
		{
			$pageids[] = $r['pageid'];
		}
		return $pageids;
	}
	
	function ls_userid($pageid)
	{
		$userids = array();
		$data = $this->db->select("SELECT `userid` FROM `$this->table` WHERE `pageid`=?", array($pageid));
		foreach ($data as $r)
		{
			$userids[] = $r['userid'];
		}
		return $userids;
	}
	
	function exists($pageid, $userid)
	{
		$pageid = intval($pageid);
		$userid = intval($userid);
		return $this->db->get("SELECT `pageid` FROM `$this->table` WHERE `pageid`=$pageid AND `userid`=$userid");
	}
}