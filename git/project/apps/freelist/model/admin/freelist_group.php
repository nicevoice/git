<?php
/**
 * 自由列表页面 -- 分组管理
 */
class model_admin_freelist_group extends model
{
	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'freelist_group';
		$this->_primary = 'gid';
		$this->_fields = array('gid', 'name');
		$this->_readonly = array('gid');
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_validators = array(
				'name'=>array(
					'not_empty'=>array('分组名称不能为空'),
					'max_length' =>array(20, '分组名称不得超过20字节')
				)
			);
	}

	function add($data)
	{
		$data = $this->filter_array($data, $this->_fields);
		return $this->insert($data);
	}

	function edit($data)
	{
		$gid = intval($data['gid']);
		$data = $this->filter_array($data, $this->_fields);
		$this->update($data,"`gid`=$gid");
		return $gid;
	}

	function delete($gid)
	{
		return parent::delete(implode_ids($gid));
	}
}