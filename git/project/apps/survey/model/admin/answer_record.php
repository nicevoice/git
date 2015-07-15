<?php

class model_admin_answer_record extends model 
{
	function __construct()
	{
        parent::__construct();
		$this->_table = $this->db->options['prefix'].'survey_answer_record';
		$this->_primary = 'answerid';
		$this->_fields = array('answerid', 'questionid', 'content');
		$this->_readonly = array('answerid', 'questionid');
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();
	}
	
	function ls($questionid, $page, $pagesize, $keywords)
	{
		import('helper.iplocation');
		$iplocation = new iplocation();
		$where = "r.questionid=$questionid";
		!empty($keywords) && $where .=' AND '.where_keywords('r.content',$keywords);
		$data = $this->db->page("SELECT * FROM #table_survey_answer_record r LEFT JOIN #table_survey_answer a ON r.answerid=a.answerid WHERE $where ORDER BY r.answerid DESC", $page, $pagesize);
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