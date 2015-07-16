<?php 
define('CMSTOP_START_TIME', microtime(true));
define('RUN_CMSTOP', true);

require '../../cmstop.php';

$cmstop = new cmstop('frontend');

$action = 'index';
if(isset($_GET['action']) && in_array($_GET['action'],array('homepage', 'page','rss','listing')))
	$action = $_GET['action'];
	
$cmstop->execute('space','index',$action);

