<?php

class model_exam_wechat_member_log extends model {
    function __construct() {
        parent::__construct();
        $this->_table = $this->db->options['prefix'] . 'exam_wechat_member_log';
        $this->_primary = 'id';
        $this->_fields = array('id', 'from_openid', 'to_openid', 'gold', 'ranking','exper', 'achievement', 'answerid', 'to_answerid', 'logtime');
        $this->_readonly = array('id');
        $this->_create_autofill = array('logtime'=>TIME);
        $this->_update_autofill = array();
        $this->_filters_input = array();
        $this->_filters_output = array();
        $this->_validators = array();
    }

}