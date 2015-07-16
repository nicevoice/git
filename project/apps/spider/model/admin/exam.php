<?php
/**
 * cmstop_exam
 *   spiderid
 *   taskid
 *   contentid
 *   title
 *   url
 *   status
 *   created
 *   createdby
 *   spiden
 *   spidenby
 */
class model_admin_exam extends model
{
	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'spider';
		$this->_primary = 'spiderid';
		$this->_fields = array('spiderid','guid','taskid','contentid','title','url','status','created','createdby','spiden','spidenby');
		
		$this->_readonly = array('spiderid', 'guid', 'created', 'createdby');
		$this->_create_autofill = array('created'=>TIME, 'createdby'=>$this->_userid);
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();
	}
	
	function setViewed($id)
	{
		$data = array('status'=>'viewed');
		return $this->update($data, "spiderid=$id AND status='new'");
	}
	function setSpiden($id, $data)
	{
		$data = array_merge($data, array(
			'status'=>'spiden',
			'spiden'=>TIME,
			'spidenby'=>$this->_userid
		));
		$this->update($data, $id);
	}
	function spider($id)
	{
		$sql = "SELECT s.*, r.* FROM #table_spider as s
				LEFT JOIN #table_spider_task as t ON t.taskid = s.taskid
				LEFT JOIN #table_spider_rules as r ON r.ruleid = t.ruleid
				WHERE spiderid='$id'";
		$rs = $this->db->select($sql);
		$rs = reset($rs);
		$rule = unserialize($rs['content_rule']);
		$engine = loader::lib('spider', 'spider');
		return $engine->getDetails($rs['url'], $rule, $rs['charset']);
	}
}