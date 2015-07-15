<?php
class model_admin_guestbook_type extends model
{
	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'guestbook_type';
		$this->_primary = 'typeid';
		$this->_fields = array('typeid','name','count','sort');
		
		$this->_readonly = array('typeid');
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array(
								'name' => array('not_empty' => array('留言类型不能为空'),'max_length' => array(20, '留言类型不得超过40字节')),
								'sort' => array('integer'=> array('留言序号只能为整数'),'max_length' => array(10, '留言序号不得超过10字节')),
		);
	}

	function add($data)
	{
		$data = $this->filter_array($data, $this->_fields);
		return $this->insert($data);
	}

	function edit($typeid,$data)
	{
		$data = $this->filter_array($data, $this->_fields);
		return $this->update($data,"`typeid`=$typeid");
	}

	function delete($typeid)
	{
		return parent::delete(implode_ids($typeid));
	}
}
?>