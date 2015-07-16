<?php

class controller_admin_setting extends wechat_controller_abstract {

    private $wechat;
    private $pagesize = 15;

    function __construct(&$app) {
        parent::__construct($app);
        $this->wechat = loader::model('admin/wechat');
    }

    function index() {
        $this->view->assign('head', array('title'=>'微信配置管理'));
        $this->view->display('setting/index');
    }

    function page() {
        $page = max((isset($_GET['page']) ? intval($_GET['page']) : 1), 1);
        $size = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : $this->pagesize;
        $status = isset($_GET['status']) ? trim($_GET['status']) : 'all';
        $where = null;
        if ($state != 'all') {
            $where = 'status = ' . intval($status);
        }
        $fields = '*';
        $order = isset($_GET['orderby']) ? str_replace('|', ' ', $_GET['orderby']) : 'dateline DESC';
        $data = $this->wechat->ls($where, $order, $page, $size);
        $total = $this->wechat->count($where);
        $result = array('total' => $total, 'data' => $data);
        $result = json_encode($result);
        echo $result;
    }

    /**
     * 添加
     *
     * @aca 添加
     */
    function add() {
        if ($this->is_post()) {
            if ($this->wechat->add($_POST)) {
                exit('{"state":"true"}');
            } else {
                $error = $this->wechat->error?$this->wechat->error:'添加失败';
                exit($this->json->encode(array('state' => false, 'error' =>$error )));
            }
        }
        $this->view->display('setting/add');
    }

    /**
     * 编辑
     *
     * @aca 编辑
     */
    function edit() {
        $wechatid = intval($_GET['wechatid']);
        $wechatid > 0 || $this->showmessage('ID不存在');
        if ($this->is_post()) {
            if ($this->wechat->edit($_POST, $wechatid)) {
                exit('{"state":"true"}');
            } else {
                $error = $this->wechat->error?$this->wechat->error:'修改失败';
                exit($this->json->encode(array('state' => false, 'error' => $error)));
            }
        }
        $data = $this->wechat->get($wechatid);
        $this->view->assign('type', $type);
        $this->view->assign('data', $data);
        $this->view->display('setting/edit');
    }

    /**
     * 删除
     *
     * @aca 删除
     */
    function delete() {
        $wechatid = intval($_GET['wechatid']);
        $wechatid > 0 || $this->showmessage('ID不存在');
        if ($this->wechat->count("tid=$tid")) {
            $this->showmessage('有设置规则的CDN接口无法删除');
        }
        if ($this->wechat->delete("wechatid=$wechatid")) {
            exit('{"state":"true"}');
        } else {
            $error = $this->wechat->error?$this->wechat->error:'删除失败';
                exit($this->json->encode(array('state' => false, 'error' => $error)));
        }
    }

}