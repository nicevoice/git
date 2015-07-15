<?php

class model_answer_record extends model 
{	
	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'survey_answer_record';
		$this->_primary = 'answerid';
		$this->_fields = array('answerid', 'questionid', 'content');
		$this->_readonly = array('answerid');
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();
	}
	
	function add($questionid, $answerid, $content)
	{
		$result = $this->insert(array('questionid'=>$questionid, 'answerid'=>$answerid, 'content'=>$content));
		if ($result)
		{
			$question = loader::model('question','survey');
			$question->set_inc('records', $questionid);
		}
	}
}