<?php
//php 繁简转换
$a1 = '【極影字幕社】 ★7月新番 刀劍神域 SWORD ART ONLINE 第12話v2 BIG5 MP4_480P';
$a2 = '【极影字幕社】 ★7月新番 刀剑神域 SWORD ART ONLINE 第12话v2 BIG5 MP4_480P';




//require_once( dirname(__FILE__) . '/chinese_conversion/ZhConversion.php');






define('CMSTOP_START_TIME', microtime(true));
define('RUN_CMSTOP', true);
define('IN_ADMIN', 1);
define('INTERNAL', 1);

require '../../../cmstop.php';
import('helper.ZhConversion');


function zhconversion_hans($str) {
	global $zh2Hans;
	var_dump($zh2Hans);exit;
	return strtr($str, $zh2Hans);
}
$name = '刀劍神域';
//$name = zhconversion_hans($name);

//echo $name;
$name = '刀剑神域';
import('helper.pinyin');
$initial = pinyin::get($name,'utf-8',0);

echo $initial;