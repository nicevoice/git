<?php
require_once(dirname(dirname(__FILE__)) . '/letter/lib/functions.php');
$id = isset($_GET['id']) ? intval($_GET['id']) : redirect(SILEN_URL.'letter/');

$users = online();
if (!$users)redirect('/letter/login.php?redirect='.urlencode(SILEN_URL.'letter/reply.php?id='.$id));

if($_POST['content']) {
	
	$author = $_POST['author'];
	$content = addslashes($_POST['content']);
	$letter_id = $_POST['letter_id'];
	$time = time();
	if ($content){
		$sql = "INSERT INTO wp_letter_reply(`letter_id`, `author`, `addtime`, `content`) VALUES('{$letter_id}', '{$author}', '{$time}', '{$content}')";
		$mysql = new SaeMysql();
		$mysql->runSql($sql);
		
		$mysql->runSql("UPDATE wp_letter SET reply=reply+1 WHERE id={$letter_id}");
		//回复量
		$mmc=memcache_init();
		$pv = memcache_get($mmc,"letter_reply");
		$pv = $pv ? json_decode($pv, true) : array($id=>0);
		$pv[$id] = $pv[$id] + 1;
		memcache_set($mmc,"letter_reply", json_encode($pv));
	}
	
	redirect(SILEN_URL.'letter/post.php?id='.$letter_id);		
}




?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>回信</title>
	<meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1,user-scalable=no">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">

	<link rel="stylesheet" href="css/mui.min.css">
	<script src="js/mui.min.js"></script>
	<style>
		html,body {
			background-color: #efeff4;
		}
		h5 {
			margin: 5px 7px;
		}
	</style>
</head>

<body>
<div class="mui-content">
	<form action="" id="is_post" method="post" >
		<input type="hidden" name="letter_id" value="<?=$id?>" />
		<div class="mui-input-row mui-content-padded">
			<input type="text" name="author" placeholder="昵称">
		</div>
	
		<div class="mui-input-row mui-content-padded">
			<textarea id="textarea" name="content" rows="5" placeholder="内容"></textarea>
		</div>
		<div class="mui-button-row">
			<input type="submit" class="mui-btn mui-btn-primary"></input>&nbsp;&nbsp;
			<button type="button" class="mui-btn mui-btn-danger" onclick="return false;">取消</button>
		</div>
	</form>
</div>
</body>
</html>