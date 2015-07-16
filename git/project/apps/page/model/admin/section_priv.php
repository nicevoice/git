<?php
class model_admin_section_priv extends object 
{
	private $db, $table;
	
	function __construct()
	{
		$this->db = & factory::db();
		$this->table = '#table_section_priv';
	}
	
	function add($sectionid, $userid)
	{
		if (!$userid)
		{
			$this->error = '该用户不存在';
			return false;
		}
		if ($this->exists($sectionid, $userid))
		{
			$this->error = '该用户已经是本区块管理员';
			return false;
		}
		return $this->db->insert("INSERT INTO `$this->table`(`sectionid`, `userid`) VALUES(?, ?)", array($sectionid, $userid));
	}
	
	function delete($sectionid, $userid)
	{
		return $this->db->delete("DELETE FROM `$this->table` WHERE `sectionid`=? AND `userid`=?", array($sectionid, $userid));
	}
	
	function ls($sectionid = null, $userid = null)
	{
		$where = array();
		if ($sectionid) $where[] = "`sectionid`=$sectionid";
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
	
	function ls_sectionid($userid)
	{
		$sectionids = array();
		$data = $this->db->select("SELECT `sectionid` FROM `$this->table` WHERE `userid`=?", array($userid));
		foreach ($data as $r)
		{
			$sectionids[] = $r['sectionid'];
		}
		return $sectionids;
	}
	
	function ls_userid($sectionid)
	{
		$userids = array();
		$data = $this->db->select("SELECT `userid` FROM `$this->table` WHERE `sectionid`=?", array($sectionid));
		foreach ($data as $r)
		{
			$userids[] = $r['userid'];
		}
		return $userids;
	}
	
	function exists($sectionid, $userid)
	{
		$sectionid = intval($sectionid);
		$userid = intval($userid);
		return $this->db->get("SELECT `sectionid` FROM `$this->table` WHERE `sectionid`=$sectionid AND `userid`=$userid");
	}
}