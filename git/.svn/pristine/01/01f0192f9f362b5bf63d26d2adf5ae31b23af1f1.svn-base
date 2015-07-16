<?php

/*第一次抓取用，后台运行*/
require_once 'common.php';
import('helper.simple_html_dom');



$nowurl = 'http://bt.ktxp.com/index-{\d}.html'; //监听的url
$csspath =  '.item-box a[href^="/html/"]';                                       //取地址的规则

$status = 3;  //3为待审，6为通过


$all_new = true;
$page=1;
$urls = array();
while($all_new){

    $now_url = str_replace('{\d}', $page, $nowurl);
    $filename = CACHE_PATH.'cache/'.$url_info['host'].'_update_'.md5($now_url).'.html';
    echo "page $page\n";
 //   $content = file_get_contents($now_url);
    if(file_exists($filename)) {
       /*  $content_old = file_get_contents($filename);
        if($content == $content_old) {
           echo 'no new record.';
           break;
        } */
		
		$content = file_get_contents($filename);
    } else {
        if($charset != 'utf-8') {
            $content = str_charset($charset, 'utf-8', $content);
        }
        if(!$content) {
            echo $now_url;
            echo 'file get failed!'."\n";
        }
        file_put_contents($filename, $content);
    }

    $dom = str_get_html($content);

    $csspath = $csspath;
    $links = $dom->find($csspath);


    $btinfoModel = loader::model('admin/btinfo', 'article');
    $articleModel = loader::model('admin/article', 'article');

    foreach($links as $e) {
        $url = $e->href;
        $r = $btinfoModel->get("oldurl='$url'");
        if($r) {
            $all_new = false;
        } else {
            $urls[] = $url;
        }
    }
	$dom->__destruct();

	cache_write('urlstest.php',$urls);
    $page++;
}

exit;


/*抓取内页*/
$current = 0;
$end = count($urls)-1;
while($current<=$end) {
    $now_url = $urls[$current];
    if(strpos($now_url, 'http://') === false) {
        $now_url = $domain.$now_url;
    }
    $filename = CACHE_PATH.'cache/'.$url_info['host'].'_con_'.md5($now_url).'.html';

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

    $c = $dom->find('.infos');
    $c = $c->find('em',0);
    $c = $c->find('a',1)->href;
    $tmp_arr = explode('-', $c);

    $old_catid = $tmp_arr[1] ;
    $catid = 0;
    foreach($lists as $l){
        if($l['old_catid'] == $old_catid) {
            $catid = $l['catid'];
        }
    }


    set_data($data, $catid);

    $process_data['lid'] = $lid;
    $process_data['current'] = $current;
    $process_data['url'] = $now_url;
    cache_write('process_data', $process_data);
    if($debug) {
        exit("\nDEBUG MODE.");
    }

    $dom->__destruct();
    $current++;

}

echo 'done';

