<?php

define('CMSTOP_START_TIME', microtime(true));
define('RUN_CMSTOP', true);
define('IN_ADMIN', 1);
require '../../cmstop.php';

// ini_set('display_errors', true);
// error_reporting(E_ALL);
$_ENV['extapp'] ='';
$cmstop = new cmstop('admin');

$cmstop->execute();