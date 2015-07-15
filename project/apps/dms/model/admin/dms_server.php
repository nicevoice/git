<?php

class model_admin_dms_server extends model implements SplSubject {

    public $data = array();
    public $event, $where, $fields, $order, $page, $pagesize, $total;
    private $observers = array();

    function __construct() {
        parent::__construct();

        $this->_table = $this->db->options['prefix'] . 'dms_server';
        $this->_primary = 'serverid';
        $this->_fields = array('serverid', 'name', 'url');
        $this->_readonly = array('serverid');
        $this->_create_autofill = array();
        $this->_update_autofill = array();
        $this->_filters_input = array();
        $this->_filters_output = array();
        $this->_validators = array(
            'name' => array(
                'not_empty' => array('服务器标识不能为空')
            ),
            'url' => array(
                'url' => array('服务器网址格式不正确')
            )
        );
    }

    function add($data) {
        return $this->insert($data);
    }

    function edit($serverid, $data) {
        return $this->update($data, $serverid);
    }

    function delete($modelid) {
        return parent::delete($modelid);
    }

    function ls($where = null, $fields = '*', $order = null, $page = null, $pagesize = null) {
        $order = $order ? $order : $this->_primary;

        $this->where = $this->where($where);
        $this->fields = $fields;
        $this->order = $order;
        $this->page = $page;
        $this->pagesize = $pagesize;

        $this->event = 'before_ls';
        $this->notify();

        $this->total = $this->count($this->where);
        $this->data = $this->page($this->where, $this->fields, $this->order, $this->page, $this->pagesize);

        $this->event = 'after_ls';
        $this->notify();

        return $this->data;
    }

    function where($data) {
        $where = array();

        if (isset($data['serverid'])) {
            $where['serverid'] = intval($data['serverid']);
        }

        return $where;
    }

    protected function _after_select(& $data, $multiple = false) {
        if ($multiple) {
            return array_map(array($this, 'output'), $data);
        } else {
            return $this->output($data);
        }
    }

    function output(& $data) {
        if (!$data)
            return;
        return $data;
    }

    function attach(SplObserver $observer) {
        $this->observers[] = $observer;
    }

    function detach(SplObserver $observer) {
        if ($index = array_search($observer, $this->observers, true))
            unset($this->observers[$index]);
    }

    function notify() {
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }

}