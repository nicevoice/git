<?php

class model_vote_log extends model 
{	
	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'vote_log';
		$this->_primary = 'logid';
		$this->_fields = array('logid', 'contentid', 'created', 'createdby', 'ip');
		$this->_readonly = array('logid', 'contentid');
		$this->_create_autofill = array('created'=>TIME, 'createdby'=>$this->_userid, 'ip'=>IP);
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();
	}
}