<?php

class controller_server extends wechat_controller_abstract {

    private $wechat;
    private $pagesize = 15;

    function __construct(&$app) {
        parent::__construct($app);
        $this->wechat = loader::model('wechat');
    }

    function index() {
        $account = $_GET['account'];
        $servers = $this->cache->get('wechae_servers');
        $server = $servers[$account];
        dump($server);
        $options = array(
            'etoken'=> $server['token'],  //微信公共平台设置的接口token
            'account'=>$server['account'], //微信公共平台账号，您不需要主动发送消息不需要设置
            'password'=>$server['password'],  //微信公共平台账号密码，您不需要主动发送消息不需要设置
        );
        $wechatObj = new Wechat($options);  //创建Wechat的实例并初始化参数
        $wechatObj->setCookiefilepath(CACHE_PATH."wechat".DS);  //设置cookie文件保存目录
        $wechatObj->setWebtokenStoragefile("./app/Runtime/webtoken.txt");  //设置webtoken的保存路径（包括文件名），您不需要主动发送消息不需要设置
        $wechatObj->login2(); //验证请求来源是否合法，在通过平台验证后可以去掉，但是不安全啊。
        $debug = $wechatObj->getDebug();
        dump($debug);
        $msgtype = $weObj->getRev()->getRevType();
    }

    function page() {
        $page = max((isset($_GET['page']) ? intval($_GET['page']) : 1), 1);
        $size = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : $this->pagesize;
        $where = 'state > 0';
        $fields = '*';
        $order = 'addtime DESC';
        $data = $this->wechat->ls($where, $fields, $order, $page, $size);
        $total = $this->wechat->count($where);
        $result = json_encode($data);
        echo $result;
    }

    function add() {
        if ($this->setting['iscode']) {
            import('helper.seccode');
            $seccode = new seccode();
            if (!$seccode->valid()) {
                $result = array('state' => false, 'error' => '验证码不正确');
                exit(json_encode($result));
            }
        }
        if ($this->setting['ischeck']) {
            $_POST['state'] = 0;
        } else {
            $_POST['state'] = 1;
        }
        if ($id = $this->wechat->add($_POST)) {
            $data = $this->wechat->get($id);
            $result = array('state' => true, 'data' => $data);
        } else {
            $result = array('state' => false, 'error' => $this->wechat->error());
        }
        echo json_encode($result);
    }

}