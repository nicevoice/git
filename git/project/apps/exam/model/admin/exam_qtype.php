<?php


class model_admin_exam_qtype extends model {

    function __construct() {
        parent::__construct();
        /**
         * 会计网
         */
        $user =  login_member();
        $this->_userid = $this->_userid ? $this->_userid : $user['user_id'];
        $this->_username = $this->_username ? $this->_username : $user['name'];
        $this->_table = $this->db->options['prefix'] . 'exam_qtype';
        $this->_primary = 'qtypeid';
        $this->_fields = array('qtypeid', 'contentid', 'qid', 'num', 'alias', 'created', 'createdby');
        $this->_readonly = array('qtypeid');
        $this->_update_autofill = array();
        $this->_filters_input = array();
        $this->_filters_output = array();
        $this->_validators = array();
    }
}