<?php
define('CMSTOP_START_TIME', microtime(true));
define('RUN_CMSTOP', true);

require '../../cmstop.php';

$controller = isset($_GET['controller'])?$_GET['controller']:'';
$action = isset($_GET['action'])?$_GET['action']:'';
if(empty($controller) || !in_array($controller,array('index','category','article','picture','video','comment','member')))
{
	$controller = 'index';
}
if(empty($action))
{
	$action = 'index';
}

$cmstop = new cmstop('mobile');
$cmstop->execute('mobile',$controller,$action);