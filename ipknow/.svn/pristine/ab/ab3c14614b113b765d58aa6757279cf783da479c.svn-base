<?php

if ($_GET['token'] != 'pv_for_www.silen.com.cn')exit('x');
require_once(dirname(dirname(__FILE__)) . '/letter/lib/functions.php');


//访问量
$mmc=memcache_init();

$pv = memcache_get($mmc, "letter_pv");


if ($pv) {
	memcache_set($mmc,"letter_pv", null);
	$mysql = new SaeMysql();
	$pv = json_decode($pv, true);
	foreach($pv as $k=>$v) {
		$mysql->runSql("UPDATE wp_letter SET pv=pv+{$v} WHERE id={$k}");
	}
}
