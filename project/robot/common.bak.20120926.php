<?php
define('ROOT_PATH', str_replace('\\', '/', dirname(__FILE__)) . '/'); //根目录
define('CACHE_PATH', ROOT_PATH.'data'.'/');
define('INCLUDE_PATH', ROOT_PATH.'include'.'/');

set_time_limit(0);
$charset = 'utf-8';
date_default_timezone_set('Asia/Chongqing');

include (INCLUDE_PATH.'simple_html_dom.php');
include (INCLUDE_PATH.'functions.php');

$debug = false;  //是否是调试模式
$cacheFile = false; //是否缓存html
$delay = 0; //请求延迟时间
$domain = 'http://meinv.top005.com';

$lists = array(
array('catid'=>1,'start'=>1,'end'=>1167,'regular'=>'/p{\d}','csspath'=>'.pic_a'),
array('catid'=>2,'start'=>1,'end'=>5,'regular'=>'/p{\d}','csspath'=>'.pic_a','domain'=>'http://shehui.top005.com/'),
array('catid'=>3,'start'=>1,'end'=>841,'regular'=>'/p{\d}','csspath'=>'.pic_a','domain'=>'http://baoxiao.top005.com/'),
array('catid'=>14,'start'=>1,'end'=>1491,'regular'=>'/p{\d}','csspath'=>'.pic_a','domain'=>'http://tucao.top005.com/'),
array('catid'=>17,'start'=>1,'end'=>618,'regular'=>'/p{\d}','csspath'=>'.pic_a','domain'=>'http://baozou.top005.com/'),
array('catid'=>18,'start'=>1,'end'=>656,'regular'=>'/p{\d}','csspath'=>'.pic_a','domain'=>'http://xiee.top005.com/'),

);


$url_info = parse_url($domain);

