<?php

class model_question_option extends model
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
		$this->_table = $this->db->options['prefix'].'exam_question_option';
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


	function ls($questionid)
	{
		return $this->gets_by('questionid', $questionid, '*', '`sort` ASC, `optionid` ASC');
	}
	

}