<?php



define('CMSTOP_START_TIME', microtime(true));
define('RUN_CMSTOP', true);
define('IN_ADMIN', 1);
define('INTERNAL', 1);
define('CACHE_PATH', 'D:/items/www.3536.cc/robot/data');

require '../cmstop.php';

set_time_limit(0);
$charset = 'utf-8';
date_default_timezone_set('Asia/Chongqing');


$debug = false;
$domain = 'http://meinv.top005.com';

$lists = array(
array('catid'=>1,'old_catid'=>null,'start'=>1,'end'=>100,'regular'=>$domain.'/p{\d}','csspath'=>'.pic_a'),
);
$delay = 0;

/* 当存在fun 且设置为0的时候，将调用 get_字段名  的函数来处理，如果 不为 0 则调用该值对应的函数来处理 */
$con_fileds = array(   //内容对应的抓取规则
'title'=>array('regular'=>'h1','func'=>0), //同时抓取manhuaid
'content'=>array('regular'=>'.intro','func'=>0),
'provider'=>array('regular'=>'.author'),
'unionid'=>array('regular'=>'.infos', 'func'=>0),
'size'=>array('regular'=>'.infos', 'func'=>0),
'published'=>array('regular'=>'.infos em:eq(3)', 'func'=>0),
'bzurl'=>array('regular'=>'#down-box .cmbg:eq(0)', 'func'=>0),
'wturl'=>array('regular'=>'#down-box .cmbg:eq(5)', 'func'=>0),
'dxurl'=>array('regular'=>'#down-box .cmbg:eq(3)', 'func'=>0),
'magnetlink'=>array('regular'=>'.magnet', 'attr'=>'href'),
'oldurl'=>array('regular'=>'.magnet', 'func'=>0),
);



$manhuas = table('manhua');
function get_title($c, $d){
	global $manhuas;
//	$c = zhconversion_hans('刀劍神域'); //繁2简
	$c = zhconversion_hans($c);

	foreach($manhuas as $m) {

		if(strpos($c, $m['manhuaname']) !== false) {
			$d['manhuaid'] = $m['manhuaid'];
			break;
		}

	}

	return $c;
}

function get_unionid($c, $d, $con, $dom, $l){
	$c1 =str_get_html($c);
	$c = $c1->find('em',1);
	$c = $c->find('a',2);
	$return = null;
	if($c) {
		$c = $c->innertext();

		$unionModel = loader::model('admin/union','article');
		$r = $unionModel->get("unionname LIKE '%$c%'");
		$return = $r['unionid'];

	}
	$c1->__destruct();
	return $return;
}

function get_size($c, $d, $con, $dom, $l){
	$c1 =str_get_html($c);
	$c = $c1->find('em',3);
	$return  = substr($c->innertext(),0,strpos($c->innertext,'MB'));
	$c1->__destruct();
	return $return;
}


$attachment = loader::model('admin/attachment', 'system');
function get_bzurl($c, $d, $con, $dom, $l){
	global $url_info,$attachment;

	$c1 =str_get_html($c);
	$c = $c1->find('.cmbg',1);
	$c = $c->href;
	$c = 'http://'.$url_info['host'].$c;
	$return  = $attachment->download_by_file($c, null, 'torrent', UPLOAD_URL);
	$c1->__destruct();
	return $return;
}

function get_wturl($c, $d, $con, $dom, $l){
	$c1 =str_get_html($c);
	$c = $c1->find('.cmbg',5);
	$return= $c->href;
	$c1->__destruct();
	return $return;
}

function get_dxurl($c, $d, $con, $dom, $l){
	$c1 =str_get_html($c);
	$c = $c1->find('.cmbg',3);
	$return = $c->href;
	$c1->__destruct();
	return $return;
}

function get_published($c, $d, $con, $dom, $l){
	$c1 =str_get_html($c);
	$c = $c1->find('em',4);
	$return = $c->innertext();

	$c1->__destruct();
	return $return;
}

function get_content($c, $d, $con, $dom, $l){
	$c = zhconversion_hans($c); //繁2简
	$c = strip_a_tags($c);
	return $c;
}

function get_oldurl($c, $d, $con, $dom, $l, $u){
    return $u;
}


/* 文章入库 */

function set_data($data, $catid) {

	$articleModel = loader::model('admin/article', 'article');
	$data['catid'] = $catid;
    $data['modelid'] = 1;
	$data['torrentnum'] = rand(100,500);
	$data['downloadnum'] = rand(300,1000);
	$data['donenum'] = rand(300,500);

	if(!$articleModel->add($data)) {
		var_dump($articleModel->error());
		exit;
	} else {
		echo $data['title']."inserted.\n";
	}
}

$url_info = parse_url($domain);

