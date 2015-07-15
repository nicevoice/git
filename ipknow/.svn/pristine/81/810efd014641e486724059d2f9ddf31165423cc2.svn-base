<?php
require_once(dirname(dirname(__FILE__)) . '/letter/lib/functions.php');
if(!online())exit('0');
$mysql = new SaeMysql();

if ($_GET['type'] == 'reply') {
	$id = isset($_GET['id']) ? intval($_GET['id']) : exit(json_encode(array('x'=>'x')));
	$pwd = trim($_GET['pwd']);
	$sql = "SELECT r.* FROM wp_letter l , wp_letter_reply r WHERE l.pwd='{$pwd}' AND l.id={$id} AND r.letter_id=l.id ORDER BY r.addtime DESC";
}else{
	$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
	$size = isset($_GET['size']) ? $_GET['size'] : 10;
	$offset = ($page - 1) * $size;
	$sql = "SELECT id,title,reply,pv,addtime,thumb FROM `wp_letter` ORDER BY addtime DESC LIMIT {$offset}, {$size}";
	
}
$data = $mysql->getData( $sql );
foreach($data as $k=>$v) {
	$data[$k]['addtime'] = date('Y-m-d H:i:s', $v['addtime']);
	$v['content'] && $data[$k]['content'] = nl2br($v['content']);
	$v['title'] && $data[$k]['thumb'] = $v['thumb'] ? $v['thumb'] : '/letter/img/nophoto.jpg';
}
$s = false;
if (!$data)$s = true;
$mysql->closeDb();
exit(json_encode(array('data'=>$data, 's'=>$s, 'isPhone'=>isPhone())));



?>