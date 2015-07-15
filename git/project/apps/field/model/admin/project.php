<?php
class model_admin_project extends model
{
	function __construct()
	{
		parent::__construct();

		$this->_table = $this->db->options['prefix'].'field_project';
		$this->_primary = 'projectid';
		$this->_fields = array(
			'projectid', 'name', 'description'
		);
		$this->_readonly = array('projectid');
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_validators = array();

	}

	public function page($where, $order, $page, $size)
	{
		$where && $where = "WHERE $where";
		$order && $order = "ORDER BY $order";
		$sql = "SELECT projectid as pid, name, description FROM #table_field_project
				$where $order";
		$data = $this->db->page($sql, $page, $size);
		return $this->output($data);
	}

	/**
	 * 方案修改页面获取数据
	 * 字段同 $this->page()
	 * @param ID 可以多个
	 */
	public function get_byid($id) 
	{
		$data = $this->db->get("SELECT projectid as pid, name, description FROM #table_field_project
				WHERE projectid IN (".implode_ids($id).")");
		return $this->output($data);
	}

	/**
	 *  添加数据
	 * 
	 */
	public function add($data)
	{
		$pid = $data['pid'];
		if($pid)
		{
			$this->update($data,"`projectid`=$pid");
			$return = $pid;
		}
		else
		{
			$return = $this->insert($data);
		}
		return $return;
	}

	/**
	 * 输出格式转换, $data是一条或多条记录
	 */
	private function output($data)
	{
		if(!$data) return array();
		if(!$data[0]) {
			$wei = 1;
			$data = array($data);
		}
		foreach ($data as & $r)
		{
			$r['created'] = $r['created'] ? date('Y-m-d H:i:s', $r['created']) : 'Unknow';
			$r['setting'] = unserialize($r['setting']);
			$r['fieldname'] = $r['setting']['fieldname'];
		}
		if($wei) $data = $data[0];
		return $data;
	}
}