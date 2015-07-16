<?php
class model_admin_widgetEngine extends model
{
	function __construct()
	{
        parent::__construct();
		$this->_table = $this->db->options['prefix'].'widget_engine';
		$this->_primary = 'engineid';
		$this->_fields = array('engineid', 'name', 'description', 'version', 'author', 'updateurl', 'installed', 'disabled');
		
		$this->_readonly = array('engineid');
		$this->_create_autofill = array('installed'=>TIME);
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();
	}
}