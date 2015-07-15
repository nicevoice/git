<?php

class model_admin_exam_qtype_question extends model {

    function __construct() {
        parent::__construct();
        /**
         * 会计网
         */
        $user =  login_member();
        $this->_userid = $this->_userid ? $this->_userid : $user['user_id'];
        $this->_username = $this->_username ? $this->_username : $user['name'];
        $this->_table = $this->db->options['prefix'] . 'exam_qtype_question';
        $this->_primary = 'id';
        $this->_fields = array('id', 'qtypeid','questionid');
        $this->_readonly = array('id');
        $this->_update_autofill = array();
        $this->_filters_input = array();
        $this->_filters_output = array();
        $this->_validators = array();
    }
}