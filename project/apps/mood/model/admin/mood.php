<?php
class model_admin_mood extends model
{
	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'mood';
		$this->_primary = 'moodid';
		$this->_fields = array('moodid', 'name', 'image', 'sort');
		
		$this->_readonly = array('moodid');
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array(
			'name'=>array('not_empty'=>array('心情名称不能为空'), 'max_length'=>array(40, '心情名称不得超过40字节')),
			'image'=>array('not_empty'=>array('图标不能为空'), 'max_length'=>array(100, '图标路径不得超过100字节')),
		);
	}

	function add($data)
	{
		$data = $this->filter_array($data, array('name', 'image', 'sort'));
		$data['sort'] = $this->max('sort') + 1;
		if ($moodid = $this->insert($data))
		{
			$this->db->query("ALTER TABLE #table_mood_data ADD `m{$moodid}` int(8) UNSIGNED NOT NULL DEFAULT '0'");
		}
		return $moodid;
	}

	function edit($moodid, $data)
	{
		$data = $this->filter_array($data, array('name', 'image', 'sort'));
		return $this->update($data, "moodid=$moodid");
	}

	function sort_change($moodid,$sort='')
	{
	   if ($sort === 'up')
	   {
			$result=$this->get($moodid,'sort');
			$up=$this->get("`sort` < {$result['sort']}",'*','sort DESC');
			return $this->update( array('sort'=>$result['sort']), "`sort` = {$up['sort']}")&&
					$this->update( array('sort'=>$up['sort']),"`moodid` = {$moodid}");
		}
		elseif ($sort === 'down')
		{
			$result=$this->get($moodid,'sort');
			$down=$this->get("`sort` > {$result['sort']}",'*','sort ASC');
			return $this->update( array('sort'=>$result['sort']), "`sort` = {$down['sort']}")&&
					$this->update( array('sort'=>$down['sort']),"`moodid` = {$moodid}");
		}
		return false;
	}

	function delete($moodid)
	{
		$moodid = explode(",", $moodid);
		if (is_array($moodid))
		{
			$drop = "DROP m".implode(", DROP m", $moodid);
			$totaldel = "m".implode(" -m", $moodid);
		}
		else
		{
			$drop = "DROP m{$moodid}";
			$totaldel = " m{$moodid}";
		}
		$this->db->query("UPDATE #table_mood_data SET `total` = total - {$totaldel}");
		$this->db->query("ALTER TABLE #table_mood_data $drop");
		return parent::delete(implode_ids($moodid));
	}

	function by_sort()
	{
		$data = $this->select('1=1', '*', 'sort'); // 使用1=1是因为传空会使用上一次的条件。
		foreach ($data as $value)
		{
			$return[$value['moodid']] = $value;
		}
		return $return;
	}

	//待删除
	function get_nextid()
	{
		$dbname=config('db','dbname');
		$sql = "SHOW TABLE STATUS FROM {$dbname} LIKE '$this->_table' ";
		$result = $this->db->select($sql);
		return ($this->max('sort'))+1;
	}
}