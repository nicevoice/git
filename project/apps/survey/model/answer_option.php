<?php

class model_answer_option extends model 
{
	function __construct()
	{
        parent::__construct();
		$this->_table = $this->db->options['prefix'].'survey_answer_option';
		$this->_primary = null;
		$this->_fields = array('answerid', 'questionid', 'optionid');
		$this->_readonly = array('answerid');
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();
	}
	
	function vote($questionid, $answerid, $optionid)
	{
		if (is_array($optionid))
		{
			foreach ($optionid as $id)
			{
				$result = $this->add($questionid, $answerid, $id);
			}
			$votes = count($optionid);
		}
		else 
		{
			$result = $this->add($questionid, $answerid, $optionid);
			$votes = 1;
		}
		$question = loader::model('question','survey');
		$question->set_inc('votes', $questionid, $votes);
		return $votes;
	}
	
	private function add($questionid, $answerid, $optionid)
	{
		$result = $this->insert(array('questionid'=>$questionid, 'answerid'=>$answerid, 'optionid'=>$optionid));
		if ($result)
		{
			$question_option = loader::model('question_option','survey');
			$question_option->set_inc('votes', $optionid);
		}
	}
}