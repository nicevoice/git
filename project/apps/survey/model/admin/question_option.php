<?php

class model_admin_question_option extends model 
{	
	function __construct()
	{
        parent::__construct();
		$this->_table = $this->db->options['prefix'].'survey_question_option';
		$this->_primary = 'optionid';
		$this->_fields = array('optionid', 'questionid', 'name', 'image', 'sort', 'votes','isfill');
		$this->_readonly = array('optionid', 'questionid');
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array('questionid'=>array('not_empty' =>array('题目ID不能为空'),
                                                       'is_numeric' =>array('题目ID必须是数字'),
                                                       'max_length' =>array(8, '题目ID不得超过8个字节'),
                                                      ),
                                   'name'=>array('not_empty' =>array('选项不能为空'),
                                                 'max_length' =>array(100, '选项不得超过30个字节'),
                                                ),
                                  );
	}

	function add($data)
	{
		$data = $this->filter_array($data, array('questionid', 'name', 'image', 'sort','isfill'));
		return $this->insert($data);
	}
	
	function edit($optionid, $data)
	{
		$data = $this->filter_array($data, array('name', 'image', 'sort'));
		return $this->update($data, $optionid);
	}
	
	function ls($questionid)
	{
		return $this->gets_by('questionid', $questionid, '*', '`sort` ASC, `optionid` ASC');
	}
	
    function delete($optionid)
    {
    	$r = $this->get($optionid);
    	if (!$r) return false;
    	if ($r['votes'])
    	{
            loader::model('admin/question','survey')->set_dec('total', $r['questionid'], $r['votes']);
    	}
    	return parent::delete($optionid);
    }
    
    function delete_by($questionid, $optionid = array(),$allowfill = 0)
    {
    	$count = 0;
    	$haveother = false;
    	$maxsort = 0;
    	$otheropid = 0;
    	$data = $this->gets_by('questionid', $questionid);
    	foreach ($data as $r)
    	{
			$maxsort = $r['sort']>$maxsort?$r['sort']:$maxsort;
    		if (!in_array($r['optionid'], $optionid))
    		{
    			$r['isfill'] && $haveother = true;
    			if($r['isfill'] && $allowfill)
    			{
    				$otheropid = $r['optionid'];
    				continue;
    			}
    			$this->delete($r['optionid']);
    			$count++;
    		}
    	}
    	if($haveother && $allowfill) $this->edit($otheropid,array('sort'=>$maxsort+1));
    	if(!$haveother && $allowfill) $this->add(array('questionid'=>$questionid,'optionid'=>'','name'=>'其他','image'=>'','sort'=>$maxsort+1,'isfill'=>1));
    	return $count;
    }
}