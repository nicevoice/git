<?php

define('CMSTOP_START_TIME', microtime(true));
define('RUN_CMSTOP', true);
define('IN_ADMIN', 1);
require '../../cmstop.php';

import('helper.simple_html_dom');

$domain = 'http://bt.ktxp.com';
$now_url = 'http://bt.ktxp.com/team.php';
$url_info = parse_url($domain);
//抓取并缓存页面
$filename = CACHE_PATH.$url_info['host'].'_con_'.md5($now_url).'.html';
$charset = 'utf-8';


if(file_exists($filename)) {
	$content = file_get_contents($filename);
} else {
	$content = file_get_contents($now_url);
	if($charset != 'utf-8') {
		$content = str_charset($charset, 'utf-8', $content);
	}
	if(!$content) die('file get failed!');
	file_put_contents($filename, $content);
}
$dom = str_get_html($content); $con = $content; unset($content);

$tags = $dom->find('div.container a');
$tagsModel = loader::model('admin/union', 'article');
import('helper.pinyin');

require_once ROOT_PATH.'framework/helper/ZhConversion.php';

function zhconversion_hans($str) {
	global $zh2Hans;
	return strtr($str, $zh2Hans);
}

foreach($tags as $t) {
	$unionname =zhconversion_hans($t->text()); //繁体转简体
	$initial = pinyin::get($unionname,'utf-8',0);//提取拼音
	$tagsModel->insert(array('unionname'=>$unionname,'initial'=>$initial));
} 
echo 'done';
