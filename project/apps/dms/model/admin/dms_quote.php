<?php

class model_admin_dms_quote extends model implements SplSubject {

    public $data = array();
    public $event, $where, $fields, $order, $page, $pagesize, $total, $apps;
    private $observers = array();

    function __construct() {
        parent::__construct();
        $this->_table = $this->db->options['prefix'] . 'dms_quote';
        $this->_primary = 'quoteid';
        $this->_fields = array('quoteid', 'target', 'modelid', 'appid', 'time', 'operator', 'status', 'disable');

        $this->_readonly = array();
        $this->_create_autofill = array();
        $this->_update_autofill = array();
        $this->_filters_input = array();
        $this->_filters_output = array();
        $this->_validators = array();

        $this->apps = loader::model('admin/dms_app');
    }

    public function select($where = null, $fields = '*', $order = null, $size = null, $offset = null, $data = array(), $multiple = true, $model = 'article') {
        $m = get_model(null, $model);
        $modelid = $m['modelid'];
        $offset = is_null($offset) ? 0 : $offset;
        $limit = $size ? ' LIMIT ' . ($multiple ? "$offset, $size" : 1) : '';
        $sql = "SELECT $fields
		FROM #table_dms_quote dq
		LEFT JOIN #table_dms_" . $model . " dc ON dq.target=dc." . $model . "id
		WHERE dq.modelid = $modelid $where
		ORDER BY dq.time DESC" . $limit;
        $data = $this->db->select($sql);
        if (!$multiple) {
            $data = array_pop($data);
        }
        $data = $this->_after_select($data, $multiple);
        return $data;
    }

    public function page($page = 1, $pagesize = 15, $where = '') {
        $page = intval($page);
        $page = max(1, $page);
        $where = empty($where) ? '' : ' AND ' . $where;
        $total = $this->count();
        $data = parent::page($where, 'dq.quoteid, dq.target, dq.appid, dq.time, dq.operator, dq.status, dq.disable, dc.title', $order, $page, $pagesize);
        return array('total' => $total, 'data' => $data);
    }

    protected function _after_select(& $data, $multiple) {
        if (!$data) {
            return;
        }
        if ($multiple) {
            $_data = array();
            foreach ($data as & $item) {
                $_data[] = $this->_after_select($item, false);
            }
            $data = $_data;
            return $data;
        }
        $data['shorttitle'] = str_cut($data['title'], 80);
        $data['app'] = $this->apps->apps[$data['appid']]['name'];
        $data['long_time'] = date('Y-m-d H:i:s', $data['time']);
        $data['time'] = date('Y-m-d', $data['time']);
        $data['status'] = ($data['disable'] == 1) ? '禁用' : $data['status'];
        return $data;
    }

    public function attach(SplObserver $observer) {
        $this->observers[] = $observer;
    }

    public function detach(SplObserver $observer) {
        if ($index = array_search($observer, $this->observers, true))
            unset($this->observers[$index]);
    }

    public function notify() {
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }

}