<?php

class model_exam_post extends model {
    private $brand_member;
    function __construct() {
        parent::__construct();
        /**
         * 会计网
         *
         */
        $user =  login_member();
        $this->_userid = $this->_userid ? $this->_userid : $user['user_id'];
        $this->_username = $this->_username ? $this->_username : $user['name'];
        $this->_table = $this->db->options['prefix'] . 'exam_post';
        $this->_primary = 'id';
        $this->_fields = array('id', 'tid', 'uid', 'message','created');
        $this->_readonly = array('id');
        $this->_create_autofill = array('created'=>TIME);
        $this->_update_autofill = array();
        $this->_filters_input = array();
        $this->_filters_output = array();
        $this->_validators = array();
    }
}