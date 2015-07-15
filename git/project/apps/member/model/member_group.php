<?php
class model_member_group extends model 
{
	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'member_group';
		$this->_primary = 'groupid';
		$this->_fields = array('groupid', 'name', 'status', 'issystem', 'remarks');
		$this->_readonly = array('groupid');
		$this->_create_autofill = array('status'=>1, 'issystem'=>0);
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array('name'=>array('not_empty' =>array('用户组名不能为空'),
                                                      'max_length' =>array(20, '用户组名不得超过20个字节'),
                                                ),
									'remarks'=>array('max_length'=>array(255,'备注不能超过255个字节'))
                                  );
	}
	
	function ls()
	{
		return $this->select();
	}
	
	function groups()
	{
		$data = $this->select();
		$groups = array();
		foreach ($data as $value)
		{
			$groups[$value['groupid']] = $value['name'];
		}
		return $groups;
	}
	
	function add($data)
	{
		$data = $this->filter_array($data, array('name', 'status', 'issystem', 'remarks'));
		return $this->insert($data);
	}
	
	function edit($groupid, $data)
	{
		return $this->update($data, array('groupid' => $groupid));
	}

	function tabs()
	{
		$data = $this->select("`status`=1", '*');
		$return = array(0=>'全部');
		foreach ($data as $value)
		{
			$return[$value['groupid']] = $value['name'];
		}
		return $return;
	}
	
	function group_persons()
	{
		$sql = "SELECT groupid, COUNT(*) persons FROM #table_member GROUP BY groupid";
		$query = $this->db->query($sql);
		$return = array();
		foreach ($query as $value)
		{
			$return[$value['groupid']] = $value['persons'];
		}
		return $return;
	}
}