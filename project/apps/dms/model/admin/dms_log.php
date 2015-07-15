<?php

class model_admin_dms_log extends model implements SplSubject {

    public $data = array();
    public $event, $where, $fields, $order, $page, $pagesize, $total;
    private $api = array();
    private $observers = array();

    function __construct() {
        parent::__construct();

        $this->_table = $this->db->options['prefix'] . 'dms_log';
        $this->_primary = 'logid';
        $this->_fields = array(
            'logid', 'appid', 'operator', 'modelid', 'target', 'action',
            'data', 'time', 'ip', 'app', 'var', 'value'
        );
        $this->_readonly = array('logid');
        $this->_create_autofill = array(
            'appid' => 0,
            'time' => TIME,
            'ip' => ip2long(IP),
            'app' => 'dms'
        );
        $this->_update_autofill = array();
        $this->_filters_input = array();
        $this->_filters_output = array();
        $this->_validators = array(
            'appid' => array(
                'integer' => array('应用ID必须是数字'),
            ),
            'modelid' => array(
                'integer' => array('操作模块ID必须是数字'),
            ),
            'target' => array(
                'integer' => array('操作对象ID必须是数字'),
            ),
            'action' => array(
                'not_empty' => array('操作类型不能为空')
            ),
            'ip' => array(
                'not_empty' => array('操作IP不能为空'),
                'ip' => array('操作IP格式不对')
            )
        );
    }

    function add($data) {
        return $this->insert($data);
    }

    function edit($logid, $data) {
        return $this->update($data, $logid);
    }

    function delete($logid) {
        return parent::delete($logid);
    }

    function ls($where = null, $fields = '*', $order = null, $page = null, $pagesize = null) {
        $api_file = Loader::import('config.api');
        $api = array();
        foreach ($api_file as $item) {
            foreach ($item['data'] as $v) {
                $api[$v['api']] = $v['name'];
            }
        }
        $this->api = $api;
        unset($api);
        $order = $order ? $order : $this->_primary;

        $this->where = $this->_where($where);
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

    private function _where($data) {
        $where = array();

        if (isset($data['logid']) && $data['logid']) {
            $where[] = "`logid` = " . intval($data['logid']);
        }

        if (isset($data['appid']) && $data['appid'] != 'all') {
            $where[] = "`appid` = " . intval($data['appid']);
        }

        if (isset($data['operator']) && trim($data['operator'])) {
            $where[] = "`operator` LIKE '" . mysql_escape_string(trim($data['operator'])) . "%'";
        }

        if (isset($data['modelid']) && $data['modelid']) {
            $where[] = "`modelid` = " . intval($data['modelid']);
        }

        if (isset($data['target']) && $data['target']) {
            $where[] = "`target` = " . intval($data['target']);
        }

        if (isset($data['time']) && $data['time']) {
            $where[] = "`time` = " . strtotime($data['time']);
        }

        if (isset($data['time_start']) && $data['time_start']) {
            $where[] = where_mintime('`time`', $data['time_start']);
        }

        if (isset($data['time_end']) && $data['time_end']) {
            $where[] = where_maxtime('`time`', $data['time_end']);
        }

        if (isset($data['ip']) && trim($data['ip'])) {
            is_numeric($data['ip']) or $data['ip'] = ip2long($data['ip']);
            $where[] = "`ip` LIKE '" . mysql_escape_string($data['ip']) . "%'";
        }

        return implode(' AND ', $where);
    }

    protected function _after_select(& $data, $multiple = false) {
        if ($multiple) {
            $data = array_map(array($this, 'output'), $data);
            return $data;
        } else {
           return  $this->output($data);
        }
    }

    function output(& $data) {
        if (!$data)
            return;

        if (isset($data['appid']) && !isset($data['appname'])) {
            if (intval($data['appid'])) {
                $data['appname'] = table('dms_app', intval($data['appid']), 'name');
                if (!$data['appname'])
                    $data['appname'] = '';
            }
            else {
                $data['appname'] = '系统应用';
            }
        }

        if (isset($data['operator'])) {
            $data['operator'] = htmlspecialchars($data['operator']);
        }

        if (isset($data['modelid']) && intval($data['modelid'])) {
            $model_arr = table('dms_model', intval($data['modelid']));
            $data['modelname'] = $model_arr['name'];
            $data['modelalias'] = $model_arr['alias'];
            $target_table = $model_arr['alias'];
            if (!$data['modelname'])
                $data['modelname'] = '';
            if (!$data['modelalias'])
                $data['modelalias'] = '';
        }

        if (isset($data['time']) && !isset($data['timename'])) {
            $data['timename'] = date('Y-m-d H:i:s', $data['time']);
        }
        if (isset($data['ip']) && is_numeric($data['ip'])) {
            $data['ip'] = long2ip($data['ip']);
        }
        if ($data['target'] && $target_table) {
            $model = loader::model("admin/dms_$target_table", 'dms');
            $data['targetname'] = str_cutword(value($model->get($data['target']), 'title', '--'), 12, "utf-8", '...');
        } else {
            $data['targetname'] = "--";
        }
        $data['actionintro'] = value($this->api, $data['action'], $data['action']);
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