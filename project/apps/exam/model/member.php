<?php

class model_member extends Model {

    public function __construct() {
        parent::__construct();
        $brand_db = config('brand_db');
        $this->db = & factory::db($brand_db);
        $this->_table = $this->db->options['prefix'] . 'member';
        $this->_primary = 'user_id';
        $this->_fields = array('user_id', 'name', 'email', 'mobile', 'real_name', 'sex', 'is_email', 'is_mobile', 'is_sina_weibo', 'is_qq_connect', 'reg_time', 'log_time');
        $this->_readonly = array('user_id', 'name');
    }
    function ifeedback($data){
        $this->_table = $this->db->options['prefix'] . 'kefu_feedback';
        $this->_fields = array('id', 'description', 'email', 'file', 'remark', 'ip', 'type', 'referer', 'created', 'createdby', 'modified', 'modifiedby', 'status');
        return $this->insert($data);

    }
}