<?php
require_once(dirname(dirname(__FILE__)) . '/letter/lib/functions.php');
//$users = online();
if (online())redirect(SILEN_URL.'letter');
//printR($users);

if ($_POST['login']):

    $password = $_POST['password'];
    $username = $_POST['username'];
    require_once( dirname(dirname(__FILE__)).'/wp-includes/class-phpass.php');
    $wp_hasher = new PasswordHash(8, TRUE);
    //$password = $wp_hasher->HashPassword($password);
    $header = $_SERVER['HTTP_USER_AGENT'];
    $mysql = new SaeMysql();
    $sql = "SELECT * FROM wp_users WHERE user_login='{$username}' LIMIT 1";
    $data = $mysql->getLine( $sql );
    if ($wp_hasher->CheckPassword($password, $data['user_pass']))
    {
    		$time = time();
    		$sql = "INSERT INTO wp_login_logs(`username`, `password`, `addtime`, `content`) VALUES('{$username}', '{$password}', '{$time}', '{$header}')";
		$mysql->runSql($sql);
			
        setcookie('letter_login_auth_cc', is_authcode($data['user_login'] . '\t' . $data['ID'] .'\t'. 'user_nicename' . '\t'.$data['user_pass'], 'ENCODE'), 3*24*60+time(), '/');
        $url = $_POST['redirect'] ? $_POST['redirect'] : SILEN_URL . 'letter/';
        redirect($url);
    }
endif;

if (is_mobile()){
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <title>登陆</title>
    <script src="js/mui.min.js"></script>
    <link href="css/mui.min.css" rel="stylesheet"/>
    <script src="http://apps.bdimg.com/libs/zepto/1.1.4/zepto.min.js"></script>
</head>

<body>
<header class="mui-bar mui-bar-nav">
	<a class="mui-action-back mui-icon mui-icon-left-nav mui-pull-left"></a>
	<h1 class="mui-title">登陆</h1>
</header>
<div class="mui-content" style="padding: 10; margin-top: 10px;">
	
	<form class="mui-input-group" method="post" id="subFrom">
		<input type="hidden" name="login" value="1">
        <input type="hidden" name="redirect" value="<?=$_GET['redirect']?>">
		<div class="mui-input-row">
            <input type="text" class="mui-input-clear" name="username" placeholder="请输入用户名">
			
		</div>
		<div class="mui-input-row">
		<input type="password" style="border: none;" class="mui-input-clear" name="password" placeholder="请输入密码">
		</div>
		<div class="mui-button-row">
			<button class="mui-btn mui-btn-primary" onclick="$('#subFrom').submit();">登录</button>
		</div>
	</form>
</div>
</body>
</html>
<?php }else{?>
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
    <script>
        $(window).load(function() {
            $('#login_modal').modal('show');
        })
    </script>
</head>
<body>
<div id="login_modal" class="modal" data-backdrop="static" aria-hidden="false" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <!-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>-->
                <h4 class="modal-title">登录</h4>
            </div>
            <div class="modal-body" style="padding-top:0px;">
                <form class="form-horizontal" method="post" style="width:540px;height:130px;margin: 20px auto 0;" >
                    <input type="hidden" name="login" value="1">
                    <input type="hidden" name="redirect" value="<?=$_GET['redirect']?>">
                    <div class="form-group">
                        <label class="col-lg-3 control-label" for="login_email">用户名</label>
                        <div class="col-lg-7" style="display: inline-block;">
                            <div id="the-email">
                                <input type="text" name="username" class="form-control typeahead" placeholder="用户名" autocomplete="on">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label" for="login_password">密码</label>
                        <div class="col-lg-7" style="display: inline-block;">
                            <input style="width: 302px;" name="password" type="password" class="form-control" placeholder="密码">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-offset-3 col-lg-7">
                            <button type="button" onclick="$('.form-horizontal').submit()" class="btn btn-primary">登录</button>

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
<?php }?>