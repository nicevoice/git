<?php
require_once(dirname(dirname(__FILE__)) . '/letter/lib/functions.php');
$users = online();
if (!$users)redirect('/letter/login.php?redirect='.urlencode(SILEN_URL.'letter/edit.php'));




if ($_POST['dosubmit']){
    if($_FILES['isfile']){
        $storage = new SaeStorage();
        $domain = 'wordpress';
        $destFileName = 'uploads/'.date('Y', time()) .'/'. date('m', time()).'/' . time().'.'.getFileType($_FILES['isfile']['name']);
        $srcFileName = $_FILES['isfile']['tmp_name'];
        $attr = array('encoding'=>'gzip');
        $thumb = $storage->upload($domain,$destFileName, $srcFileName, $attr, true);
	}
        $title = trim($_POST['title']);
        $password = $_POST['password'];
        $userid = $users['userid'];
		$editid = $_POST['editid'];
        $time = time();
        $author = $_POST['author'];
        $content = addslashes($_POST['editorValue']);
        if(!$title || !$content)printR('参数错误');
        if ($editid){
        		$thumbsql = '';
			if ($thumb)$thumbsql = ", thumb='{$thumb}'";
        		$sql = "UPDATE wp_letter SET title='{$title}', author='{$author}' {$thumbsql}, content='{$content}', pwd='{$password}' WHERE id={$editid} AND userid={$userid}";
        	}else{
        		$sql = "INSERT INTO wp_letter(`title`, `pwd`, `author`, `thumb`, `userid`, `addtime`, `content`) VALUES('{$title}', '{$password}', '{$author}', '{$thumb}', '{$userid}', '{$time}', '{$content}')";
					
		}
        $mysql = new SaeMysql();
        $mysql->runSql($sql);
        $id = $mysql->lastId();
		$id = $id ? $id : $editid;
		if (!$editid) {
			/*$sms = apibus::init( "sms"); //创建短信服务对象
		    $mobile = "15815819314";
		    $msg = "溯索.为打开的情书有更新了 密码你知道". bdUrlAPI(1, SILEN_URL.'letter/post.php?id='.$id);
		    $obj = $sms->send( $mobile, $msg , "UTF-8");
			printR($msg);*/
		}
		
        redirect(SILEN_URL.'letter/post.php?id='.$id);
   
}else{
	$id = isset($_GET['id']) ? intval($_GET['id']) : null;
	if ($id) {
		$sql = "SELECT * FROM wp_letter WHERE id={$id} AND userid={$users['userid']} LIMIT 1";
		$mysql = new SaeMysql();
    		$info = $mysql->getLine($sql);
	}
}
if (is_mobile()){
?>


<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>发布器</title>
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
		<input type="hidden" name="dosubmit" value="1" />
		<div class="mui-input-row mui-content-padded">
			<input type="text" name="title" placeholder="标题">
		</div>
		
		<div class="mui-input-row mui-content-padded">
			<input type="text" name="author" placeholder="作者">
		</div>
		<div class="mui-input-row mui-content-padded">
			<input type="text" name="password" placeholder="密码">
		</div>
		<div class="mui-input-row mui-content-padded">
			<textarea id="textarea" name="editorValue" rows="5" placeholder="内容"></textarea>
		</div>
		<div class="mui-button-row">
			<input type="submit" class="mui-btn mui-btn-primary"></input>&nbsp;&nbsp;
			<button type="button" class="mui-btn mui-btn-danger" onclick="return false;">取消</button>
		</div>
	</form>
</div>
</body>
</html>
<?php

}else{		

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>情书编辑器</title>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
	    <!-- 新 Bootstrap 核心 CSS 文件 -->
	<link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.3.2/css/bootstrap.min.css">
	
	<!-- 可选的Bootstrap主题文件（一般不用引入） -->
	<link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">
	
	<!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
	<script src="http://cdn.bootcss.com/jquery/1.11.2/jquery.min.js"></script>
	
	<!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
	<script src="http://cdn.bootcss.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="/letter/ueditor/ueditor.config.js"></script>
    <script type="text/javascript" charset="utf-8" src="/letter/ueditor/ueditor.all.min.js"> </script>
    <!--建议手动加在语言，避免在ie下有时因为加载语言失败导致编辑器加载失败-->
    <!--这里加载的语言文件会覆盖你在配置项目里添加的语言类型，比如你在配置项目里配置的是英文，这里加载的中文，那最后就是中文-->
    <script type="text/javascript" charset="utf-8" src="/letter/ueditor/lang/zh-cn/zh-cn.js"></script>

    <style type="text/css">
        div{
            width:100%;
        }
    </style>
</head>
<body >
<div class="page-header">
    <h1 style="margin-left: 10px;">情书编辑器</h1>
</div>
<div class="container" style="margin-bottom: 30px;">
    <form action="" method="post" enctype="multipart/form-data">
    	<input  type="hidden" value="<?=$id?>" name="editid" />
    <table class="table table-hover">
        <tr>
            <td width="80">标题：</td>
            <td  colspan="3"><input type="text" name="title" value="<?=$info['title']?>" class="form-control"> </td>
        </tr>
        <tr>
            <td width="80">封面：</td>
            <td colspan="3"><input type="file" name="isfile" value="" class="form-control"> </td>
        </tr>
        <tr>
            <td width="80">密码：</td>
            <td ><input type="text" name="password" value="<?=$info['pwd']?>" class="form-control form-group-sm"> </td>
            <td width="80">作者：</td>
            <td ><input type="text" name="author" value="<?=$info['author']?>" class="form-control form-group-sm"> </td>
        </tr>
        <tr>
            <td colspan="4"><textarea id="editor" style="height: 300px;"><?=$info['content']?></textarea></td>
        </tr>
        <tr>
            <td colspan="4"><input class="btn" type="submit" value="发布" name="dosubmit"></td>
        </tr>
    </table>
    </form>

</div>

<script type="text/javascript">

    //实例化编辑器
    //建议使用工厂方法getEditor创建和引用编辑器实例，如果在某个闭包下引用该编辑器，直接调用UE.getEditor('editor')就能拿到相关的实例
    var ue = UE.getEditor('editor');


</script>

</body>
</html>
<?php }?>