<?php

class model_exam_wechat_messages_pm extends model {
    function __construct() {
        parent::__construct();
        $this->_table = $this->db->options['prefix'] . 'exam_wechat_messages_pm';
        $this->_primary = 'messageid';
        $this->_fields = array('messageid', 'openid', 'delstatus','created');
        $this->_readonly = array('messageid');
        $this->_create_autofill = array('created'=>TIME);
        $this->_update_autofill = array();
        $this->_filters_input = array();
        $this->_filters_output = array();
        $this->_validators = array();
    }

}