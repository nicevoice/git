<?php

class model_exam_wechat_member extends model {
    function __construct() {
        parent::__construct();
        $this->_table = $this->db->options['prefix'] . 'exam_wechat_member';
        $this->_primary = 'openid';
        $this->_fields = array('openid', 'gold', 'level','exper', 'subjectid', 'logtime');
        $this->_readonly = array('openid');
        $this->_create_autofill = array('logtime'=>TIME);
        $this->_update_autofill = array();
        $this->_filters_input = array();
        $this->_filters_output = array();
        $this->_validators = array();
    }

}