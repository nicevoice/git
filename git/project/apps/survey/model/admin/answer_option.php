<?php

class model_admin_answer_option extends model 
{
	function __construct()
	{
        parent::__construct();
		$this->_table = $this->db->options['prefix'].'survey_answer_option';
		$this->_primary = 'answerid';
		$this->_fields = array('answerid', 'questionid', 'optionid');
		$this->_readonly = array('answerid');
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();
	}
	
	function ls($optionid, $page, $pagesize)
	{
		import('helper.iplocation');
		$iplocation = new iplocation();
		
		$data = $this->db->page("SELECT * FROM #table_survey_answer_option r LEFT JOIN #table_survey_answer a ON r.answerid=a.answerid WHERE r.optionid=$optionid ORDER BY r.answerid DESC", $page, $pagesize);
		foreach ($data as $k=>$r)
		{
			$r['createdbyname'] = $r['createdby'] ? username($r['createdby']) : '';
			$r['created'] = date('Y-m-d H:i:s', $r['created']);
			$r['iparea'] = $iplocation->get($r['ip']);
			$data[$k] = $r;
		}
		return $data;
	}
}