<?php

class model_admin_dms_model extends model implements SplSubject {

    public $data = array();
    public $event, $where, $fields, $order, $page, $pagesize, $total;
    private $observers = array();

    function __construct() {
        parent::__construct();

        $this->_table = $this->db->options['prefix'] . 'dms_model';
        $this->_primary = 'modelid';
        $this->_fields = array('modelid', 'name', 'alias', 'mainindex', 'deltaindex');
        $this->_readonly = array('modelid');
        $this->_create_autofill = array();
        $this->_update_autofill = array();
        $this->_filters_input = array();
        $this->_filters_output = array();
        $this->_validators = array(
            'name' => array(
                'not_empty' => array('模型名称不能为空'),
            ),
            'alias' => array(
                'not_empty' => array('模型别名不能为空'),
                '/[a-z]\w*$/' => array('模型别名必须为英文字母'),
            ),
            'mainindex' => array(
                'not_empty' => array('主索引不能为空'),
            )
        );
    }

    function add($data) {
        return $this->insert($data);
    }

    function edit($modelid, $data) {
        return $this->update($data, $modelid);
    }

    function delete($modelid) {
        return parent::delete($modelid);
    }

    function form() {
        $result = array();
        foreach ($this->ls() as $data) {
            $result[$data['modelid']] = $data['name'];
        }
        return $result;
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

        if (isset($data['modelid'])) {
            $where['modelid'] = intval($data['modelid']);
        }

        return $where;
    }

    protected function _after_select(& $data, $multiple = false) {
        if ($multiple) {
            $data = array_map(array($this, 'output'), $data);
            return $data;
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