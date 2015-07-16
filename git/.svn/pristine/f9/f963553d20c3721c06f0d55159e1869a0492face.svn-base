<?php
require_once 'common.bak.20120926.php';

if(isset($_GET['lid'])) {  //当前执行列表的索引值
	$lid = $_GET['lid'];
} else {
	$lid = 0;
}

$catid =  $lists[$lid]['catid'];
$regular = array_key_exists('domain',$lists[$lid]) ? $lists[$lid]['domain'].$lists[$lid]['regular'] : $domain.$lists[$lid]['regular'];
$csspath = $lists[$lid]['csspath'];
$start = $lists[$lid]['start'];
$end = $lists[$lid]['end'];
$li_total = count($lists);
$list_url = $lists[$lid]['url'];

if(isset($_GET['current'])) {  //当前执行列表的分页索引值
	$current = $_GET['current'];
} else {
	$current = $start;
}

//取到当前url
$now_url = str_replace('{\d}', $current, $regular);



//抓取并缓存页面
$filename = CACHE_PATH.'cache/'.$url_info['host'].'_list_'.md5($now_url).'.html';

if(file_exists($filename)) {
	$content = file_get_contents($filename);
} else {
	$content = curl_get_contents(array($now_url));
	if(!$content) die('file get failed!');
	if($cacheFile) file_put_contents($filename, $content);
}


$dom = str_get_html($content);

$links = $dom->find($csspath);
$listname = $url_info['host'].'_list_url_'.$catid.'.php';

$tmp = cache_read($listname) ;
$urls = $tmp ? $tmp : array(); unset($tmp);

foreach($links as $e) {
	$urls[] = $e->href;
}

$urls = array_unique(array_values($urls));//去重

echo 'have fetched '.count($urls).' links';
cache_write($listname, $urls);

$current = intval($current);
$current++;
if($current> $end) {
	$lid = intval($lid);
	$lid++;
	if($lid>$li_total-1) {
		echo 'Done.';
	} else {
		$current = $lists[$lid]['start'];
		$end = $lists[$lid]['end'];
		echo nextStr('?current='.$current.'&lid='.$lid.'&end='.$end, $delay);
	}
} else {
	echo nextStr('?current='.$current.'&lid='.$lid.'&end='.$end, $delay);
}
function nextStr($query_string, $delay=1000) {
	return '<script language="javascript">setTimeout("location.href=\''.$_SERVER['PHP_SELF'].$query_string.'\';", '.$delay.');</script>'.'<a href="'.$_SERVER['PHP_SELF'].$query_string.'">NEXT</a>';
}

