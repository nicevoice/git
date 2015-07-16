<?php

class model_question_answer extends model {

    function __construct() {
        parent::__construct();
        /**
         * 会计网
         */
        $user =  login_member();
        $this->_userid = $this->_userid ? $this->_userid : $user['user_id'];
        $this->_username = $this->_username ? $this->_username : $user['name'];
        $this->_table = $this->db->options['prefix'] . 'exam_question_answer';
        $this->_primary = 'answerid';
        $this->_fields = array('answerid', 'type', 'answer', 'analysis');
        $this->_readonly = array('answerid');
        $this->_update_autofill = array();
        $this->_filters_input = array();
        $this->_filters_output = array();
        $this->_validators = array();
    }
}