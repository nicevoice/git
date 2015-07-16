<?php

class model_exam_wechat_friend extends model {
    function __construct() {
        parent::__construct();
        $this->_table = $this->db->options['prefix'] . 'exam_wechat_friend';
        $this->_primary = 'openid';
        $this->_fields = array('user_id', 'from_openid', 'to_openid');
        $this->_readonly = array('openid');
        $this->_create_autofill = array();
        $this->_update_autofill = array();
        $this->_filters_input = array();
        $this->_filters_output = array();
        $this->_validators = array();
    }
}