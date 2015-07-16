<?php
define('CMSTOP_START_TIME', microtime(true));
/* CMSTOP Setting */
define('RUN_CMSTOP', true);
require '../../../cmstop.php';
error_reporting(0);

$GLOBALS['uc_key'] = $GLOBALS['db_siteownerid'] = setting('member','pw_key');
$GLOBALS['charset'] = 'utf-8';

//常量 API_ROOT API的系统路径
define('API_ROOT',strpos(__FILE__, DIRECTORY_SEPARATOR) !== FALSE ? substr(__FILE__, 0, strrpos(__FILE__,DIRECTORY_SEPARATOR)).'/' : './');

$GLOBALS['db'] = & factory::db();
$GLOBALS['tablepre'] = $GLOBALS['db']->prefix();
require_once(API_ROOT.'pw_api/class_base.php');
$api = new api_client();
$response = $api->run($_POST + $_GET);
if ($response) {
	echo $api->dataFormat($response);
}