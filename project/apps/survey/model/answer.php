<?php

class model_answer extends model
{	
	function __construct()
	{
        parent::__construct();
		$this->_table = $this->db->options['prefix'].'survey_answer';
		$this->_primary = 'answerid';
		$this->_fields = array('answerid', 'contentid', 'created', 'createdby', 'ip');
		$this->_readonly = array('answerid');
		$this->_create_autofill = array('created'=>TIME, 'createdby'=>$this->_userid, 'ip'=>IP);
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();
	}

	function add($contentid, $data)
	{
		$contentid = intval($contentid);
		$answerid = $this->insert(array('contentid'=>$contentid));
		if ($answerid)
		{
			$question = loader::model('question','survey');
			$option = loader::model('answer_option','survey');
			$record = loader::model('answer_record','survey');
			
			$questions = $question->ls($contentid);
			foreach ($questions as $questionid=>$q)
			{
				if ($q['type'] == 'checkbox' || $q['type'] == 'radio' || $q['type'] == 'select')
				{
					if (isset($data[$questionid]['optionid']))
					{
						$option->vote($questionid, $answerid, $data[$questionid]['optionid']);
					}
					if ($q['allowfill'] && !empty($data[$questionid]['content']))
					{
						$record->add($questionid, $answerid, $data[$questionid]['content']);
					}
				}
				elseif (($q['type'] == 'text' || $q['type'] == 'textarea') && !empty($data[$questionid]))
				{
					$record->add($questionid, $answerid, $data[$questionid]);
				}
			}
		}
		return $answerid;
	}
}