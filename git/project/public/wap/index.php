<?php
define('CMSTOP_START_TIME', microtime(true));
define('RUN_CMSTOP', true);

require '../../cmstop.php';

$action = isset($_GET['action'])?$_GET['action']:'';
if(empty($action) || !in_array($action,array('index','category','comment','image','show')))
{
	$action = 'index';
}

$cmstop = new cmstop('wap');
$cmstop->execute('wap','wap',$action);