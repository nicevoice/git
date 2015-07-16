<?php
class model_admin_field extends model
{
	function __construct()
	{
		parent::__construct();

		$this->_table = $this->db->options['prefix'].'field';
		$this->_primary = 'fieldid';
		$this->_fields = array(
			'fieldid', 'field', 'projectid', 'setting', 'sort', 'created', 'createdby'
		);
		$this->_readonly = array('projectid');
		$this->_create_autofill = array('created'=>TIME, 'createdby'=>$this->_userid);
		$this->_update_autofill = array();
		$this->_validators = array();

		loader::import('lib.fieldEngine');
	}

	public function getHtml($pid)
	{
		$fields = parent::gets_by('projectid', $pid, '*', 'sort');

		foreach($fields as & $r)
		{
			$data[] = fieldEngine::render($r['field'], $r);
		}
		return $data;
	}

    public function sort($fieldid, $sort)
    {
    	$fieldid = intval($fieldid);
    	$sort = intval($sort);
    	return $this->set_field('sort', $sort, $fieldid);
    }

	public function insert($post)
	{
		$post	= $this->filter_array($post, array('field', 'projectid', 'setting', 'created', 'createdby'));
		$sort	= $this->get("projectid=$post[projectid]", 'max(sort) as sort');		
		$sort	= $sort['sort'];;
		if (is_null($sort))
		{
			$post['sort']	= 0;
		}
		else
		{
			$post['sort'] = $sort + 1;
		}
		return parent::insert($post);
	}
}