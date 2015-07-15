<?php

class model_report extends model
{	
	function __construct()
	{
		parent::__construct();
        /**
         * 会计网
         */
        $user =  login_member();
        $this->_userid = $this->_userid ? $this->_userid : $user['user_id'];
        $this->_username = $this->_username ? $this->_username : $user['name'];
		$this->_table = $this->db->options['prefix'].'exam_report';
		$this->_primary = 'id';
		$this->_fields = array('id', 'uid', 'days', 'learntime', 'answers', 'ctime', 'lore', 'correct', 'logtime');
		$this->_readonly = array('id');
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();
	}
}