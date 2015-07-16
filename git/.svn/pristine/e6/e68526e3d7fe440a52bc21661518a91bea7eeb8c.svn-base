<?php
//浏览器内运行的内容抓工具，（待废弃）
require_once 'common.php';


if(isset($_GET['lid'])) {  //当前执行列表的索引值
	$lid = $_GET['lid'];
} else {
	$lid = 0;
}

$li_total = count($lists);
$urls = cache_read($url_info['host'].'_list_url_'.$lists[$lid]['catid'].'.php');

$start = 0;
$end = count($urls)-1;

if(isset($_GET['current'])) {  //获取当前要抓取的页面在对应url列表的索引值
	$current = $_GET['current'];
} else {
	$current = $start;
}                              


$now_url = $urls[$current];

if(strpos($now_url, 'http://') === false) {
	$now_url = $domain.$now_url;
}


//抓取并缓存页面
$filename = CACHE_PATH.'cache/'.$url_info['host'].'_con_'.md5($now_url).'.html';

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

$data = array();
foreach($con_fileds as $k => $r){

	$regular_index = isset($r['regular_index']) ? $r['regular_index']: 0;
	if(isset($r['attr'])) {
		$data[$k] = $dom->find($r['regular'], $regular_index)->$r['attr'];
	} else {
		$data[$k] = $dom->find($r['regular'], $regular_index)->innertext();
	}
	
	if(isset($r['func'])) {
		$data[$k] = call_user_func(empty($r['func'])
		? "get_$k" : $r['func'], & $data[$k], & $data, $con, $dom, $r);
	}
	
}
set_data($data, $lists[$lid]['catid']);

if($debug) {
	return false;
}

$current = intval($current);
$current++;
if($current> $end) {
	$lid = intval($lid);
	$lid++;
	if($lid>$li_total-1) {
		echo 'Done.';
	} else {
		$current = 0;
		//$end = ;
		echo nextStr('?current='.$current.'&lid='.$lid.'&end='.$end, $delay);
	}
} else {
	echo nextStr('?current='.$current.'&lid='.$lid.'&end='.$end, $delay);
}
function nextStr($query_string, $delay=1000) {
	return '<script language="javascript">setTimeout("location.href=\''.$_SERVER['PHP_SELF'].$query_string.'\';", '.$delay.');</script>'.'<a href="'.$_SERVER['PHP_SELF'].$query_string.'">NEXT</a>';
}





