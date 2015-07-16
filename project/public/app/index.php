<?php
define('CMSTOP_START_TIME', microtime(true));
define('RUN_CMSTOP', true);

require '../../cmstop.php';

// ini_set('display_errors', true);
// error_reporting(E_ALL);
$cmstop = new cmstop('frontend');
$cmstop->execute();
?>