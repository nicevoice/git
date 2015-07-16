<?php
class model_admin_widget extends model
{
	function __construct()
	{
        parent::__construct();
		$this->_table = $this->db->options['prefix'].'widget';
		$this->_primary = 'widgetid';
		$this->_fields = array('widgetid', 'name', 'engine', 'data', 'setting', 'skin', 'status', 'updated', 'updatedby', 'published', 'frequency', 'created', 'createdby', 'description');

		$this->_readonly = array('widgetid');
		$this->_create_autofill = array('created'=>TIME,'createdby'=>$this->_userid);
		$this->_update_autofill = array('updated'=>TIME,'updatedby'=>$this->_userid);
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();
	}
	function shared($widgetid, array $data)
	{
		$data = $this->filter_array($data, array('name', 'skin', 'description'));
		if (! $data)
		{
			return false;
		}
		$data['name'] = trim(strip_tags($data['name']));
		if (empty($data['name']))
		{
			return false;
		}
		$data['status'] = 1;
		return $this->_updateWithoutAuto($data, "widgetid={$widgetid}");
	}
	function unshare($widgetid)
	{
		return $this->_updateWithoutAuto(array(
			'status'=>0
		), "widgetid={$widgetid}");
	}
	function published($widgetid)
	{
		return $this->_updateWithoutAuto(array(
	    	'published'=>TIME
	    ), "widgetid={$widgetid}");
	}
	protected function _updateWithoutAuto($data, $where)
	{
	    $sql = "UPDATE `$this->_table` SET `".implode('`=?,`', array_keys($data))."`=?";
		if ($where) $sql .= " WHERE $where ";
		return $this->db->update($sql, array_values($data));
	}
}
