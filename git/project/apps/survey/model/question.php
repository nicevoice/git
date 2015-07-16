<?php
class model_question extends model
{
	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'survey_question';
		$this->_primary = 'questionid';
		$this->_fields = array('questionid', 'contentid', 'subject', 'description', 'image', 'type', 'width', 'height', 'maxlength', 'validator', 'required', 'maxoptions', 'allowfill', 'sort', 'votes', 'records');
		$this->_readonly = array('questionid', 'contentid');
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();
	}
	
	function ls($contentid)
	{
		$contentid = intval($contentid);
		$data = $this->gets_by('contentid', $contentid, '*', '`sort` ASC, `questionid` ASC');
		$question = array();
		foreach ($data as $r)
		{
			$question[$r['questionid']] = $r;
		}
		return $question;
	}
}