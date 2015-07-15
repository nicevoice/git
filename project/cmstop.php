<?php

/**
 * @Rev   $Id: cmstop.php 5407 2012-04-27 08:42:17Z liyawei $
 */
define('PROJECT_START_TIME', microtime(true));
define('DS', '/');
define('CMSTOP_PATH', str_replace('\\', '/', dirname(__FILE__)) . DS);
define('ROOT_PATH', CMSTOP_PATH);
define('FW_PATH', dirname(CMSTOP_PATH).DS."framework".DS);
//echo FW_PATH ;
set_include_path(FW_PATH);


require CMSTOP_PATH . 'config/define.php';
require 'framework.php';
if(file_exists(app_dir('system').'lib/common.php')){
    require app_dir('system').'lib/common.php';
}
final class cmstop extends object {

    public $app, $controller, $action, $args, $client, $app_dir, $class, $userid, $username, $groupid, $roleid, $departmentid;
    static $cacheid, $cachettl;

    public function __construct($client = 'frontend') {
        $this->client = $client;
        $this->cache();
    }

    function __destruct() {
        if (!is_null(self::$cachettl))
            cmstop::cache_end();
    }

    public function set_app($app) {
        if (!preg_match("/^[0-9a-z_]+$/i", $app)) {
            $app = strip_tags($app);
            $this->showmessage("$app 非法参数");
        }

        $this->app_dir = app_dir($app);
        if (!is_dir($this->app_dir))
            $this->showmessage("$app 应用不存在");

        $r = table('app', $app);
        if (!$r)
            $this->showmessage("$app 应用未安装");
        if ($r['disabled'])
            $this->showmessage("$app 应用已禁用");
        $this->app = $app;
        loader::set_app($this->app);
    }

    public function set_controller($controller) {
        if (!preg_match("/^[0-9a-z_]+$/i", $controller)) {
            $controller = strip_tags($controller);
            $this->showmessage("$controller 非法参数");
        }

        if ($this->client === 'admin') {
            $this->class = 'controller_admin_' . $controller;
            $file = $this->app_dir . 'controller' . DS . 'admin' . DS . $controller . '.php';
        } else {
            $this->class = 'controller_' . $controller;
            $file = $this->app_dir . 'controller' . DS . $controller . '.php';
        }

        if (!file_exists($file))
            $this->showmessage("$controller 控制器不存在");

        $abstract = $this->app_dir . 'controller' . DS . 'abstract.php';
        if (is_file($abstract)) {
            require_once ($abstract);
        }
        require_once ($file);
        if (!class_exists($this->class, false)) {
            $this->class = $this->app . '_' . $this->class;
        }

        $this->controller = $controller;
    }

    public function set_action($action) {
        if (!preg_match("/^[0-9a-z_]+$/i", $action)) {
            $action = strip_tags($action);
            $this->showmessage("$action 非法参数");
        }

        $this->action = $action;
    }

    public function set_args($args) {
        $this->args = $args;
    }

    public function execute($app = null, $controller = null, $action = null, $args = array()) {

        $router = & factory::router();
        if ($this->client == 'admin')
            $router->set_mode('standard');
        $router->execute();
        if (empty($app)) {
            $app = $router->app;
        }
        if (empty($controller)) {
            $controller = $router->controller;
        }
        if (empty($action)) {
            $action = $router->action;
        }
        if (empty($args)) {
            $args = $router->args;
        }

        $this->set_app($app);
        $this->set_controller($controller);

        $this->set_action($action);
        $this->set_args($args);

        $this->_before_execute();

        $obj = new $this->class($this);

        $response = $obj->execute();

        $this->_after_execute($response);

        return $response;
    }

    protected function _before_execute() {
        if (defined('INTERNAL')) {
            $login = online();
            if (!empty($login)) {
                $this->userid = $login['userid'];
                $this->username = $login['username'];
                $this->groupid = $login['groupid'];
            }
            return true;
        }

        $setting = setting('system');
        if ($setting['ipbanned']) {
            $ipbanned = str_replace(array('*', '.'), array('[0-9]{1,3}', '\.'), $setting['ipbanned']);
            $ipbanned = array_map('trim', explode("\n", $ipbanned));
            foreach ($ipbanned as $ip) {
                if (preg_match("/^$ip$/", IP))
                    $this->showmessage("Access Denied");
            }
        }

        if ($this->client == 'admin') {
            // 基础授权
            if (!license()) {
                self::licenseFailure();
            }

            if ($setting['ipaccess']) {
                $access = false;
                $ipaccess = str_replace(array('*', '.'), array('[0-9]{1,3}', '\.'), $setting['ipaccess']);
                $ipaccess = array_map('trim', explode("\n", $ipaccess));
                foreach ($ipaccess as $ip) {
                    if (preg_match("/^$ip$/", IP))
                        $access = true;
                }
                if (!$access)
                    $this->showmessage("Access Denied");
            }
        }
        else {
            if ($setting['closed'])
                $this->showmessage($setting['closedreason']);

            if ($setting['minrefreshsecond'] && !$this->cc($setting['minrefreshsecond'])) {
                exit('Please not refresh soo often');
            }
        }

        if (stristr($_SERVER['HTTP_USER_AGENT'], ' flash')) {
            // resotre headers
            foreach ($_REQUEST as $key => $val) {
                if (preg_match('/^HTTP(?:_[A-Z]+)+$/', $key)) {
                    $_SERVER[$key] = $val;
                }
            }

            // resotre cookie
            $cookie = empty($_SERVER['HTTP_COOKIE']) ? (empty($_REQUEST['Auth-Cookie']) ? null : $_REQUEST['Auth-Cookie']) : $_SERVER['HTTP_COOKIE'];
            if ($cookie) {
                foreach (explode(';', $cookie) as $pair) {
                    $pair = explode('=', $pair, 2);
                    $_COOKIE[trim($pair[0])] = urldecode(trim($pair[1]));
                }
            }
        }

        $login = online(); 
        $this->userid = $login['userid'];
        $this->username = $login['username'];
        $this->groupid = $login['groupid'];
        if ($this->client == 'admin') {
            if (config('safemode', 'status')) {
                $aca = "{$this->app}/{$this->controller}/{$this->action}";
                $safemode_aca = config('safemode', 'aca');
                if (array_key_exists($aca, $safemode_aca)) {
                    $aca_config = $safemode_aca[$aca];
                    if ($aca_config === '*' || strtolower(value($_SERVER, 'REQUEST_METHOD', '')) === $aca_config) {
                        $this->showmessage(config('safemode', 'message'));
                    }
                }
            }

            $aca = $this->app . '/' . $this->controller . '/' . $this->action;
            if (in_array($aca, array(
                        'system/admin/login',
                        'system/seccode/image',
                        'system/seccode/valid'
                    ))) {
                if (!empty($login)) {
                    header('Location:' . ADMIN_URL);
                    exit;
                }
                return true;
            } elseif (empty($login)) {
                loader::model('member', 'member')->logout();
                $refer = 'http://' . $_SERVER['HTTP_HOST'] . '/?' . $_SERVER['QUERY_STRING'];
                header('Location:?app=system&controller=admin&action=login&refer=' . urlencode($refer));
                exit;
            }

            //if ($this->groupid != 1) $this->showmessage('您不是管理员！');
            // 管理权限二次验证
            $admin = loader::model('admin/admin', 'system');
            $a = $admin->login($this->userid);
            if (!$a)
                $this->showmessage($admin->error());

            // 管理员数量授权
            $admins = factory::db()->get("SELECT COUNT(*) AS `total` FROM `#table_admin`");
            if ($admins && !license('system', array('admins' => intval($admins['total'])))) {
                self::licenseFailure('系统中的管理员数超出了您的授权数量');
            }

            if ($setting['enableadminlog']) {
                $log = loader::model('admin/admin_log', 'system');
                $log->insert(array('aca' => $this->app . '/admin/' . $this->controller . '/' . $this->action));
                unset($log);
            }

            $this->roleid = $login['roleid'];
            $this->departmentid = $login['departmentid'];

            $this->priv();
        }
        return true;
    }

    protected function _after_execute(& $response) {
        
    }

    protected function priv() {
        require_once app_dir('system').'lib/priv.php';
        priv::init($this->userid, $this->roleid);
        if (!priv::aca($this->app, $this->controller, $this->action))
            $this->showmessage('您没有此操作权限！');
    }

    function cache() {
        // 文件缓存
        if (!is_dir(CACHE_PATH)) {
            @mkdir(CACHE_PATH, 0777);
            @mkdir(CACHE_PATH . 'setting', 0777);
            @mkdir(CACHE_PATH . 'table', 0777);
            @mkdir(CACHE_PATH . 'templates', 0777);

            $setting = & factory::setting();
            $setting->cache();
        }

        // cache类
        $cache = & factory::cache();
        if (!$cache->get('cmstop_cache')) {
            table_cache();
        }
    }

    static function cache_start($ttl, $id = NULL) {
        if (!$id) {
            $id = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : (isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : preg_replace("/(.*)\.php(.*)/i", "\\1.php", $_SERVER['PHP_SELF'])) . ($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : $_SERVER['PATH_INFO']);
            $id = md5($id);
        }
        self::$cacheid = $id;
        $cache = & factory::cache();
        $contents = $cache->get(self::$cacheid);
        if ($contents == TRUE) {
            echo $contents;
            exit;
        } else {
            ob_start();
            self::$cachettl = $ttl;
            return true;
        }
    }

    static function cache_end() {
        if (is_null(self::$cachettl))
            return false;
        $cache = & factory::cache();
        return $cache->set(self::$cacheid, ob_get_contents(), self::$cachettl);
    }

    function showmessage($message, $url = null, $ms = 2000, $success = false) {
        $accept = value($_SERVER, 'HTTP_ACCEPT', '');
        if (stripos($accept, 'application/json') !== false || stripos($accept, 'text/javascript') !== false) {
            $result = array('state' => $success);
            $result[$success ? 'message' : 'error'] = $message;
            $json = & factory::json();
            $result = $json->encode($result);
            exit(isset($_GET['jsoncallback']) ? $_GET['jsoncallback'] . "($result);" : $result);
        }
        $template = & factory::template('system');
        $template->assign('CONFIG', config::get('config'));
        $template->assign('message', $message);
        $template->assign('url', $url);
        $template->assign('ms', $ms);
        $template->assign('success', $success);
        $template->display('system/showmessage.html');
        exit;
    }

    function cc($ttl) {
        if (!$ttl)
            return;

        $cache = & factory::cache();
        $ccid = 'cc_' . IP;
        $lastvisit = $cache->get($ccid);
        $time = microtime(true);
        if ($lastvisit && $time - $lastvisit <= $ttl) {
            return false;
        } else {
            $cache->set($ccid, $time, 60);
            return true;
        }
    }

    static function encode($data, $key) {
        $klen = strlen($key);
        $len = strlen($data);
        $args = array(str_repeat('v', $len + 1), $len);
        while (--$len >= 0) {
            $k = $len % $klen;
            $args[] = ord($data[$len]) ^ ord($key[$k]);
        }
        return base64_encode(call_user_func_array('pack', $args));
    }

    static function decode($data, $key) {
        $data = base64_decode($data);
        $len = unpack('vlen', $data);
        $len = $len['len'];
        $blen = strlen(pack('v', 0));
        $klen = strlen($key);
        $slen = strlen($data);
        $code = '';
        while (--$len >= 0) {
            $k = $len % $klen;
            $chr = unpack('vchr', substr($data, -(1 + $len) * $blen, $blen));
            $chr = chr(intval($chr['chr']) ^ ord($key[$k]));
            $code = $chr . $code;
        }
        return $code;
    }

    static function licenseEncode($string, $key) {
        return self::encode($string, $key);
    }

    static function licenseDecode($license, $key) {
        return self::decode($license, $key);
    }

    static function licenseFailure($message = null) {
        global $cmstop;
        if ($cmstop instanceof cmstop) {
            $cmstop->showmessage($message ? $message : '请联系 <a href="http://www.cmstop.com/" target="_blank">CmsTop</a> 官方购买授权');
        }
        exit;
    }

}

function license($app = 'system', array $opt = array()) {
    return true;
    // 内部，跳过验证（如计划任务）
    if (defined('INTERNAL'))
        return true;

    // 获取证书信息
    $file = ROOT_PATH . 'license';
    if (!is_file($file))
        return false;
    $key = 'xeKuzm58hmwipQwR@cmstop.com';
    $license = file_get_contents($file);
    $license = strrev($license);
    $license = base64_decode($license);
    $license = substr($license, 10, -15 - strlen($key));
    $license = cmstop::licenseDecode($license, $key);
    list($apps, $domain, $ip, $admindomain, $specials, $admins, $expires) = explode('|', $license);

    // 过期时间
    if ($expires && time() > $expires) {
        return false;
    }

    // 后台域名
    $host = $_SERVER['SERVER_NAME'];
    if (empty($host)) {
        $host = $_SERVER['HTTP_HOST'];
    }
    if (md5($host) !== $admindomain) {
        return false;
    }

    // IP
    $ip = explode(',', $ip);
    $addr = $_SERVER['SERVER_ADDR'];
    if (empty($addr)) {
        $addr = $_SERVER['LOCAL_ADDR'];
    }
    if (!in_array($addr, $ip)) {
        return false;
    }

    // 应用
    $apps = explode(',', $apps);
    array_unshift($apps, 'system'); // system 代指不需要特殊授权的所有应用
    if (!in_array($app, $apps)) {
        return false;
    }

    // 专题数量
    if ($specials && isset($opt['specials']) && $opt['specials'] > $specials) {
        return false;
    }

    // 管理员数量
    if ($admins && isset($opt['admins']) && $opt['admins'] > $admins) {
        return false;
    }

    return true;
}
