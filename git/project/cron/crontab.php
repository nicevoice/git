<?php
define('CMSTOP_START_TIME', microtime(true));
define('RUN_CMSTOP', true);
define('IN_ADMIN', 1);
define('INTERNAL', 1);

require '../cmstop.php';

$cmstop = new cmstop('admin');
$cmstop->execute('system', 'cron', 'interval');