<?php
require_once(dirname(dirname(__FILE__)) . '/letter/lib/functions.php');
$id = isset($_GET['id']) ? intval($_GET['id']) : redirect(SILEN_URL.'letter/');
$users = online();
if (!$users)redirect('/letter/login.php?redirect='.urlencode(SILEN_URL.'letter/post.php?id='.$id));

$mysql = new SaeMysql();
if ($_POST['password']) {
	$password = trim($_POST['password']);
	$id = intval($_POST['id']);
	$sql = "SELECT * FROM `wp_letter` WHERE id='{$id}' AND pwd='{$password}' LIMIT 1";
	
} else {
	$sql = "SELECT * FROM `wp_letter` WHERE id='{$id}' LIMIT 1";
}

$data = $mysql->getLine( $sql );
if (!$data)redirect(SILEN_URL.'letter/post.php?id='.$id.'&password=error');

if ($data['pwd'] && $password != $data['pwd']) {
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <title>密码保护</title>
    <script src="js/mui.min.js"></script>
    <link href="css/mui.min.css" rel="stylesheet"/>
    <script src="http://apps.bdimg.com/libs/zepto/1.1.4/zepto.min.js"></script>
</head>

<body>
<header class="mui-bar mui-bar-nav">
	<a class="mui-action-back mui-icon mui-icon-left-nav mui-pull-left"></a>
	<h1 class="mui-title">密码保护</h1>
</header>
<div class="mui-content" style="padding: 10; margin-top: 10px;">
	<?php if($_GET['password']){?>
		<script>
			mui.init();
			mui.toast('输入的密码错误');
		</script>
		
	<?php }?>
	<form class="mui-input-group" method="post" id="subFrom">
		<div class="mui-input-row">
            <input type="hidden" value="<?=$_GET['id']?>" name="id">
			<input type="password" class="mui-input-clear" name="password" placeholder="请输入密码">
		</div>
		<div class="mui-button-row">
			<button class="mui-btn mui-btn-primary" onclick="$('#subFrom').submit();">确认</button>&nbsp;&nbsp;
			<button class="mui-btn mui-btn-primary" onclick="return false;">取消</button>
		</div>
	</form>
</div>
</body>
</html>
<?php

}else{
	//访问量
	$mmc=memcache_init();
	$pv = memcache_get($mmc,"letter_pv");
	$pv = $pv ? json_decode($pv, true) : array($id=>0);
	$pv[$id] = $pv[$id] + 1;
	memcache_set($mmc,"letter_pv", json_encode($pv));
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <title><?=$data['title']?></title>
   
    <script src="/letter/js/mui.min.js"></script>
    <link href="/letter/css/mui.min.css" rel="stylesheet"/>
    <script src="http://apps.bdimg.com/libs/zepto/1.1.4/zepto.min.js"></script>
    <script>
    $(function(){
    		$('.clear-view').on('click', function(){
    			$('.article-view').css('background', 'none')
    		})
    		
    })
    function replyView(id, pwd)
	{
		$.get('/letter/api.php?type=reply', {id:id, pwd:pwd},function(json){
			var data = json.data
			var html = ''
			for (i in data) {
				html += '<article style="padding: 10px; margin-bottom: 10px;">'+
					'<div class="mui-col-xs-11 mui-text-center mui-content-padded">'+data[i].author+' for <span class="mui-badge">'+data[i].addtime+'</span></div>'+
					data[i].content+
					'</article>';
			}
			$('.reply-view').html(html)
		},'json')
	}
    		
    </script>
    <style>.mui-btn{ margin-top: 10px; }</style>
</head>

<body>
<header class="mui-bar mui-bar-nav">
	<a class="mui-action-back mui-icon mui-icon-left-nav mui-pull-left"></a>
	<h1 class="mui-title"><?=$data['title']?></h1>
	<a href="javascript:void(0)" class="mui-btn mui-btn-link mui-pull-right clear-view">清晰模式</a>
</header>
<div class="mui-content" >
	<!--background:url(<?=$data['thumb']?>) no-repeat;background-size:cover; -->
	<article class="article-view"style="padding: 10px;">
		<?=nl2br($data['content'])?>
		<button type="button" onclick="location.href='http://www.silen.com.cn/letter/reply.php?id=<?=$data[id]?>'"  class="mui-btn mui-btn-success mui-btn-block" >回复</button>
		
		<button type="button" onclick="replyView(<?=$id?>, '<?=$password?>')" class="mui-btn mui-btn-success mui-btn-block">显示回信</button>
	</article>
	
	<div class="reply-view">
		
	</div>
	
</div>
</body>
</html>
<?php }?>