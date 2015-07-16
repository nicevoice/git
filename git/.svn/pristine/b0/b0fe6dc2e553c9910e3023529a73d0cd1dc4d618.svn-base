<?php

class model_admin_wechat extends model {

    public $wechat, $sql_total;

    function __construct() {
        parent::__construct();
        if (class_exists('Wechat')) {
            $this->cl = new Wechat();
        } else {
            $this->cl = loader::lib('Wechat');
        }
        $this->api	= Loader::import('config.api');
        $this->cache = factory::cache();
        $this->_table = $this->db->options['prefix'] . 'wechat';
        $this->_primary = 'wechatid';
        $this->_fields = array('wechatid', 'wxname', 'account', 'password', 'token', 'dateline', 'updateline', 'authmeta', 'authdateline', 'updatline', 'usernum', 'msgnum', 'lastcheck', 'status');
        $this->_readonly = array('wechatid');
        $this->_create_autofill = array();
        $this->_update_autofill = array();
        $this->_filters_input = array();
        $this->_filters_output = array();
        $this->_validators = array();
    }

    function ls($where, $order = 'wechatid DESC', $page, $pagesize) {
        $condition = array();
        if (isset($where['status']) && intval($where['status']))
            $condition[] = "`status`='" . $where['status'] . "'";
        if (isset($where['published']) && $where['published'])
            $condition[] = where_mintime('published', $where['published']);
        if (isset($where['unpublishd']) && $where['unpublishd'])
            $condition[] = where_maxtime('published', $where['unpublishd']);
        if (isset($where['createdby']) && $where['createdby'])
            $condition[] = "`createdby`='" . $where['createdby'] . "'";

        if (is_array($condition))
            $wheresql = implode(' AND ', $condition);
        if ($wheresql)
            $wheresql = ' WHERE ' . $wheresql;
        $sql = "SELECT *
 		FROM #table_wechat $wheresql ORDER BY $order";
        $this->sql_total = "SELECT COUNT(*) as count FROM  #table_wechat $wheresql";
        $data = $this->db->page($sql, $page, $pagesize);
        $data = $this->output($data);
        return $data;
    }
    
    function _update_cache(){
        $data = $this->ls('1=1','wechatid DESC',1,1000);
        import("helper.arrays");
        $data = arrays::multi_array($data,'account');
        $this->cache->set('wechae_servers',$data);
    }

    function add($data) {
        $data['dateline'] = $data['updateline'] = TIME;
        $data['wxname'] = htmlspecialchars($data['wxname']);
        if ($this->_same_user_is_exists($data['account'])) {
            $this->error = '相同的微信号已存在！';
            return false;
        }
        $data['status'] = 1;
        $data = $this->filter_array($data, array('wxname', 'account', 'password', 'token', 'dateline', 'updateline', 'status'));
        $ret =  $this->insert($data);
        $this->_update_cache();
        return $ret;
    }

    function _same_user_is_exists($code, $wechatid = 0) {
        if (!$code) {
            return true;
        }
        $count = $this->count("account='{$code}' AND wechatid<>'$wechatid' ");
        return $count;
    }

    function edit($data, $wechatid) {
        $data['updateline'] = TIME;
        $data['wxname'] = htmlspecialchars($data['wxname']);
        if ($this->_same_user_is_exists($data['account'], $wechatid)) {
            $this->error = '相同的微信号已存在！';
            return false;
        }
        if ($data['repassword']) {
            $data['password'] = $data['repassword'];
        }
        $data['status'] = $data['status'] ? 1 : 0;
        $data = $this->filter_array($data, array('wxname', 'account', 'password', 'token', 'updateline', 'status'));
        $ret =  $this->update($data, "wechatid=$wechatid", 1);
        $this->_update_cache();
        return $ret;
    }

    function output($data) {
        foreach ($data as $k => $value) {
            $data[$k]['url'] = APP_URL . "?app=wechat&controller=server&action=index&account=" . $data[$k]['account'];
            //避免密码泄露
            unset($data[$k]['password']);
            $data[$k]['dateline'] = date('Y-m-d H:i:s', $value['dateline']);
            $data[$k]['updateline'] = date('Y-m-d H:i:s', $value['updateline']);
            $data[$k]['lastcheck'] = date('Y-m-d H:i:s', $value['lastcheck']);
//            $data[$k]['title'] = htmlspecialchars($value['title']);
//            $data[$k]['modelname'] = table('model', $value['modelid'], 'name');
//            $data[$k]['catname'] = table('category', $value['catid'], 'name');
//            $data[$k]['caturl'] = table('category', $value['catid'], 'caturl');
//            $data[$k]['createdbyname'] = ($value['createdby']) ? table('member', $value['createdby'], 'account') : '';
//            $data[$k]['created'] = date('Y-m-d H:i:s', $value['created']);
//            $data[$k]['published'] = date('Y-m-d H:i:s', $value['published']);
        }
        return $data;
    }

    function total() {
        $total = $this->db->get($this->sql_total);
        return $total['count'];
    }

}