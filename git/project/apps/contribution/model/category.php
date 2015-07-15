<?php

class model_category extends model
{
	private $tree, $category;

	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'category';
		$this->_primary = 'catid';
		$this->_fields = array('catid', 'parentid', 'name', 'alias', 'parentids', 'childids', 'workflowid', 'model', 'template_index', 'template_list', 'template_date', 'path', 'url', 'iscreateindex', 'urlrule_index', 'urlrule_list', 'urlrule_date', 'urlrule_show', 'enablecontribute', 'allowcomment', 'keywords', 'description', 'posts', 'pv', 'sort', 'disabled');
		$this->_readonly = array('catid', 'parentids', 'childids');
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();

		import('helper.tree');
		$this->tree = new tree('#table_category', 'catid');
		$this->category = table('category');
	}
	
	function get_child_front($catid = null)
	{
		return $this->tree->get_child($catid, '`catid`,`parentids`,`childids`,`name`,`path`,`sort`,`url`',0,'enablecontribute =1');
	}

}