<?php
define('CMSTOP_START_TIME', microtime(true));
define('RUN_CMSTOP', true);

require '../../cmstop.php';


$uri = $_SERVER['REQUEST_URI'];
if ( (bool)strpos($uri, 'exam/') && (bool)strpos($uri, '.html')){

    //进入项目页面
    preg_match("/exam\/([a-zA-Z]+)?/" , $uri , $arr);
    $url_cp = config::get('exam','url_cp');
    if (in_array($arr[1], array_keys($url_cp))){
        $cmstop = new cmstop();
        $cmstop->execute('exam', 'exam', 'project', array('cp'=>$arr[1]));
        exit;
    }

    $cmstop = new cmstop();

    $cmstop->execute('exam', 'exam', 'create_html2exam');
    die();
}
// ini_set('display_errors', true);
// error_reporting(E_ALL);
$cmstop = new cmstop('frontend');
$cmstop->execute();
?>