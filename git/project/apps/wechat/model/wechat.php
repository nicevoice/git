<?php

class model_wechat extends model implements SplSubject {

    public $wechatid;

    /**
     * 通知服务器
     *
     * @var array
     */
    private $observers = array();

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
        $this->_primary = 'id';
        $this->_fields = array('id', 'username', 'email', 'content', 'ip', 'addtime', 'state', 'reply', 'replytime');
        $this->_readonly = array('id');
        $this->_create_autofill = array('addtime' => TIME, 'ip' => IP);
        $this->_update_autofill = array();
        $this->_filters_input = array();
        $this->_filters_output = array();
        $this->_validators = array(
            'username' => array(
                'not_empty' => array('留言者姓名不能为空！'),
                'min_length' => array(5, '留言者姓名不能少于5个字符！'),
            ),
            'email' => array(
                'not_empty' => array('留言者邮箱不能为空！'),
                'email' => array('留言者邮箱格式不正确！'),
            ),
            'content' => array(
                'min_length' => array(5, '留言内容不能少于5个字符！'),
            ),
            'reply' => array(
                'min_length' => array(5, '回复内容不能少于5个字符！'),
            ),
        );
    }

    function ls($where, $fields, $order, $page, $size) {
        $data = $this->page($where, $fields, $order, $page, $size);
        return $data;
    }

    function add($data) {
        $data = $this->filter_array($data, $this->_fields);
        if ($id = $this->insert($data)) {
            return $id;
        }
        return false;
    }

    /**
     * 查询后置方法
     */
    function _after_select(&$result, $multiple = true) {
        if ($multiple) {
            for ($i = 0; $i < count($result); $i++) {
                $result[$i]['state'] = $result[$i]['state'] ? '已审核' : '未审核';
                $result[$i]['addtime'] = $result[$i]['addtime'] ? date('Y-m-d H:i:s', $result[$i]['addtime']) : '';
                $result[$i]['replytime'] = $result[$i]['replytime'] ? date('Y-m-d H:i:s', $result[$i]['replytime']) : '';
            }
        } else {
            $result['state'] = $result['state'] ? '已审核' : '未审核';
            $result['addtime'] = $result['addtime'] ? date('Y-m-d H:i:s', $result['addtime']) : '';
            $result['replytime'] = $result['replytime'] ? date('Y-m-d H:i:s', $result['replytime']) : '';
        }
    }

    /**
     * 审核留言
     */
    function check($ids) {
        if (is_array($ids)) {
            $ids = implode($ids);
        }
        return $this->set_field('state', 2, $ids);
    }

    /**
     * 删除留言
     */
    function delete($ids) {
        if (is_array($ids)) {
            $ids = implode($ids);
        }
        return parent::delete($ids);
    }

    /**
     * 回复留言
     */
    function reply($id, $data) {
        $this->wechatid = $id;
        $this->event = 'before_reply';
        $this->notify();
        if ($this->update($data, $this->wechatid)) {
            $this->event = 'after_reply';
            $this->notify();
            return true;
        } else {
            return false;
        }
    }

    /**
     * SplSubject接口实现
     */
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