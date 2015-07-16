<?php
define('CMSTOP_START_TIME', microtime(true));
define('RUN_CMSTOP', true);

require '../../cmstop.php';

$action = isset($_GET['tag']) ? 'tag' : 'index';

$cmstop = new cmstop('frontend');
$cmstop->execute('system', 'tags', $action);