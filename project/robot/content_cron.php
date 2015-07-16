<?php

   /*第一次抓取用，后台运行*/


require_once 'common.php';
import('helper.simple_html_dom');

$will_num = 0; //此程序每次运行要抓取的内容数量
$will_total = 30;


$li_total = count($lists)-1;


$process_data = array();
if($process_data = cache_read('process_data')) {

} else {
	$process_data = array(
		'lid'=>0,
		'current'=>0,
		'url'=>'',
        'filename'=>''
	);
	cache_write('process_data', $process_data);
}

$lid = 	$process_data['lid'];
$current = 	$process_data['current']+1;

while($lid <= $li_total) {
	$urls = cache_read($url_info['host'].'_list_url_'.$lists[$lid]['catid'].'.php');

	$end = count($urls)-1;

	while($current<=$end) {
        if($will_num>$will_total) {
            exit('this done.');
        }
		$now_url = $urls[$current];
		if(strpos($now_url, 'http://') === false) {
			$now_url = $domain.$now_url;
		}
		$filename = CACHE_PATH.'cache/'.$url_info['host'].'_con_'.md5($now_url).'.html';
        $process_data['filename'] = $filename;
        cache_write('process_data', $process_data);

		if(file_exists($filename)) {
			$content = file_get_contents($filename);
		} else {
			$content = file_get_contents($now_url);
			if($charset != 'utf-8') {
				$content = str_charset($charset, 'utf-8', $content);
			}
			if(!$content) {
				echo $now_url;
				echo 'file get failed!'."\n";
				$current++;
				continue;
			}
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
					? "get_$k" : $r['func'], $data[$k], & $data , $con, $dom, $r, $now_url);
			}

		}

		set_data($data, $lists[$lid]['catid']);

		$process_data['lid'] = $lid;
		$process_data['current'] = $current;
		$process_data['url'] = $now_url;
		cache_write('process_data', $process_data);
		if($debug) {
			exit("\nDEBUG MODE.");
		}

		$dom->__destruct();
		$current++;
        $will_num++;


	}
	$current =0;
	$lid++;

}

echo 'done';



