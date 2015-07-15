<?php

class controller_admin_index extends wechat_controller_abstract {

    private $wechat;
    private $pagesize = 15;

    function __construct(&$app) {
        parent::__construct($app);
        $this->wechat = loader::model('admin/wechat');
    }

    function index() {
        $this->view->display('index');
    }

    function page() {
        $page = max((isset($_GET['page']) ? intval($_GET['page']) : 1), 1);
        $size = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : $this->pagesize;
        $state = isset($_GET['state']) ? trim($_GET['state']) : 'all';
        $where = null;
        if ($state != 'all') {
            $where = 'state = ' . intval($state);
        }
        $fields = '*';
        $order = isset($_GET['orderby']) ? str_replace('|', ' ', $_GET['orderby']) : 'addtime DESC';
        $data = $this->wechat->ls($where, $fields, $order, $page, $size);
        $total = $this->wechat->count($where);
        $result = array('total' => $total, 'data' => $data);
        $result = json_encode($result);
        echo $result;
    }

    function view() {
        $id = intval($_GET['id']);
        if (!$id) {
            $this->showmessage('指定的ID错误');
        }
        $data = $this->wechat->get($id);
        if (!$data) {
            $this->showmessage('指定的ID错误');
        }
        $this->view->assign($data);
        $this->view->display('view');
    }

    function reply() {
        $id = intval($_POST['id']);
        if (!$id) {
            $result = array('state' => false, 'error' => '指定的ID错误');
            exit(json_encode($result));
        }
        $reply = $_POST['reply'];
        $replytime = time();
        $data = array('reply' => $reply, 'replytime' => $replytime, 'state' => 2);
        if ($this->wechat->reply($id, $data)) {
            $result = array('state' => true, 'info' => '回复成功');
            echo json_encode($result);
        } else {
            $result = array('state' => false, 'error' => $this->wechat->error());
            echo json_encode($result);
        }
    }

    function check() {
        $num = 0;
        $id = isset($_GET['id']) ? $_GET['id'] : 0;
        if (!$id) {
            $id = isset($_POST['id']) ? $_POST['id'] : 0;
        }
        $num = $this->wechat->check($id);
        $result = array('state' => true, 'info' => '成功审核' . $num . '条');
        echo json_encode($result);
    }

    function delete() {
        $num = 0;
        $id = isset($_GET['id']) ? $_GET['id'] : 0;
        if (!$id) {
            $id = isset($_POST['id']) ? $_POST['id'] : 0;
        }
        $num = $this->wechat->delete($id);
        $result = array('state' => true, 'info' => '成功删除' . $num . '条');
        echo json_encode($result);
    }

}