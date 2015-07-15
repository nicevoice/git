<?php

$id = isset($_GET['id']) ? intval($_GET['id']) : exit('骗鬼呢');
$mysql = new SaeMysql();

$sql = "SELECT ID as id,post_date,post_title,post_content FROM `wp_posts` WHERE post_password = '' AND post_status='publish' AND post_type='post' AND ID={$id} LIMIT 1";
$data = $mysql->getLine( $sql );

$mysql->closeDb();

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <title><?=$data['post_title']?></title>
   
    <script src="js/mui.js"></script>
    <link href="css/mui.css" rel="stylesheet"/>
    <script src="http://apps.bdimg.com/libs/zepto/1.1.4/zepto.min.js"></script>
    <script type="text/javascript" charset="utf-8">
     mui.init({
	  gestureConfig:{
	   tap: true, //默认为true
	   doubletap: true, //默认为false
	   longtap: true, //默认为false
	   swipe: true, //默认为true
	   drag: true, //默认为true
	   hold:false,//默认为false，不监听
	   release:false//默认为false，不监听
	  }
	});
	//window.addEventListener("swipeleft",function(){
     //alert("你正在向左滑动");
	//});
	
	
    </script>
</head>

<body>
<header class="mui-bar mui-bar-nav">
	<a class="mui-action-back mui-icon mui-icon-left-nav mui-pull-left"></a>
	<h1 class="mui-title"><?=$data['post_title']?></h1>
</header>
<div class="mui-content">
	
	<article style="padding: 10px;">
		<?=nl2br($data['post_content'])?>
	</article>
</div>
</body>
</html>