<?php

class model_admin_option extends model 
{	
	function __construct()
	{
        parent::__construct();
		$this->_table = $this->db->options['prefix'].'vote_option';
		$this->_primary = 'optionid';
		$this->_fields = array('optionid', 'contentid', 'name', 'sort', 'votes');
		$this->_readonly = array('optionid', 'contentid');
		$this->_create_autofill = array('votes'=>0);
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array('contentid'=>array('not_empty' =>array('内容ID不能为空'),
                                                      'is_numeric' =>array('内容ID必须是数字'),
                                                      'max_length' =>array(8, '内容ID不得超过8个字节'),
                                                     ),
                                   'name'=>array('not_empty' =>array('选项不能为空'),
                                                 'max_length' =>array(100, '选项不得超过30个字节'),
                                                     ),
                                  );
	}

	function add($data)
	{
		$data = $this->filter_array($data, array('contentid', 'sort', 'name', 'votes'));
		return $this->insert($data);
	}
	
	function edit($optionid, $data)
	{
		$data = $this->filter_array($data, array('contentid', 'sort', 'name', 'votes'));
		return $this->update($data, $optionid);
	}
	
    function delete($optionid)
    {
    	$r = $this->get($optionid);
    	if (!$r) return false;
    	if ($r['votes'])
    	{
            loader::model('admin/vote')->set_dec('total', $r['contentid'], $r['votes']);
    	}
    	return parent::delete($optionid);
    }
    
    function delete_by($contentid, $optionid = array())
    {
    	$count = 0;
    	$data = $this->gets_by('contentid', $contentid);
    	foreach ($data as $r)
    	{
    		if (!in_array($r['optionid'], $optionid))
    		{
    			$this->delete($r['optionid']);
    			$count++;
    		}
    	}
    	return $count;
    }
	
	function ls($contentid)
	{
		return $this->gets_by('contentid', $contentid, '*', '`sort` ASC, `optionid` ASC');
	}
	
	function _after_select($data, $multiple = false)
    {
    	if (!$data) return ;
		if ($multiple)
		{
			$ret = array_map(array($this, 'output'), $data);
		}
		else 
		{
			$ret = $this->output($data);
		}
		return $ret;
    }
    
    public function output(& $r)
	{
		$r['name'] = htmlspecialchars($r['name']);
	}
}