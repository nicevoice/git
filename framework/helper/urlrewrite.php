<?php

class urlrewrite extends object {

    public $indexname;
    public $rootdir;

    function __construct($mode = '') {

//        import('helper.folder');
    }

    /**
     * 设置首页名称，一般是为空
     * @param string $indexname
     * @return \urlrewrite
     */
    function set_indexname($indexname) {
        $this->indexname = $indexname;
        return $this;
    }

    function set_subdir($subdir) {
        $this->subdir = $subdir;
        return $this;
    }

    function uri() {
        $PHP_SELF = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
        if (isset($_SERVER['REQUEST_URI'])) {
            $uri = $_SERVER['REQUEST_URI'];
            if ($this->indexname) {
                $rootdit = $this->subdir ? "/" . $this->subdir . "/" : "/";
                $urifix = $rootdit . $this->indexname . '/';
                if (strpos($_SERVER['REQUEST_URI'], $urifix) === 0) {
                    $uris = @explode($urifix, $_SERVER['REQUEST_URI']);
                    $uri = "/" . $uris[1];
                } else {
                    $urifix = $_SERVER['SCRIPT_NAME'] . '/';
                    $uris = @explode($urifix, $_SERVER['REQUEST_URI']);
                    $uri = "/" . $uris[1];
                }
            }
        } else {
            $urifix = $_SERVER['SCRIPT_NAME'] . '/';
            $uris = @explode($urifix, $PHP_SELF);
            $uri = "/" . $uris[1];
        }
        return $uri;
    }

    function parse($passurl = '') {
        $passurl = $passurl ? $passurl : self::uri();
        if (!$passurl)
            $passurl = "/";
        $reurl = $this->subdir ? $this->subdir . "/" . $this->indexname . "/" : $this->indexname . "/";
        if ($this->subdir)
            $passurl = str_replace($reurl, '', $passurl);
        $point = strripos($passurl, '/') ? 1 : 0;
        $queryString = array();
        $thegid = '';
        if (isset($passurl)) {
            $aPathInfo = strpos($passurl, '/', 1) ? explode('/', substr($passurl, $point, strrpos($passurl, '.') - 1)) : explode('/', substr($passurl, $point, strrpos($passurl, '.')));
            $thegid = $aPathInfo[0];
            if ($aPathInfo[1]) {
                $pathinfo = explode('-', $aPathInfo[1]);
                $count = count($pathinfo);
                for ($foo = 2; $foo <= $count + 2; $foo+=2) {
                    if (!empty($pathinfo[$foo])) {
                        $_GET[$pathinfo[$foo]] = ($foo + 2) == $count ? array_shift(explode('.', $pathinfo[$foo + 1])) : $pathinfo[$foo + 1];
                    }
                }
                $_GET['controller'] = $pathinfo[0] ? $pathinfo[0] : 'index'; //传递controller参数
                $_GET['action'] = $pathinfo[1] ? $pathinfo[1] : 'index'; //传递action参数
            }
        }
        $ret['gid'] = strtolower($thegid);
        $ret['_get'] = $_GET;
        return $ret;
    }

}