<?php
define('CMSTOP_START_TIME', microtime(true));
define('RUN_CMSTOP', true);

require '../../cmstop.php';

$cmstop = new cmstop('frontend');
$cmstop->execute('article', 'article', 'printing');