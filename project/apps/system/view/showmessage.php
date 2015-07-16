<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$CONFIG['charset']?>" />
<meta http-equiv="cache-control" content="no-cache" />
<title>提示信息</title>
<style type="text/css">
<!--
body, h1, p, a, img {margin:0;padding:0;}
body{text-align:center;font-family:Arial, Helvetica, sans-serif, "宋体";}
h1,p{padding-left:100px; text-align:left;}
h1{font-size:14px; margin-top:12px;}
p{font-size:12px; line-height:150%;padding:10px 10px 10px 100px;line-height:18px;}
a:link,a:visited{color:#454545;text-decoration: underline;}
a:hover,a:active{color: #0066FF;}
.box_border{margin:100px auto 0;width:420px; padding:20px 0;}
#right{border:1px solid #A6DFA6; background:#EEF9EE url(<?=IMG_URL?>images/bg_right.gif) no-repeat 10px center;}
 #right h1{color:#237E29;}
#wrong{border:1px solid #FDBD77; background:#FFFDD7 url(<?=IMG_URL?>images/bg_wrong.gif) no-repeat 10px center;}
 #wrong h1{color:#f30;}
.c_red{color:#FF0000;}
#right a:hover,#right a:active{color: #237E29;}
#wrong a:hover,#wrong a:active{color: #f30;}
-->
</style>
</head>
<body>
<div class="box_border" <?php if ($success){ ?>id="right" <?php }else{?> id="wrong" <?php } ?>>
  <div class="content">
     <h1><?=$message?></h1>
    <p><?php if ($url == null){ ?>
	<a href="javascript:history.back();" >[点这里返回上一页]</a>
	<?php }elseif($url == 'close'){ ?>
	<input type="button" name="close" value=" 关闭 " onClick="window.close();">
	<?php }elseif($url == 'back'){ ?>
	<script language="javascript">setTimeout(function(){history.back();}, <?=$ms?>);</script>
	<?php }else{?>
	<a href="<?=$url?>">如果您的浏览器没有自动跳转，请点击这里</a>
	<script language="javascript">setTimeout("location.href='<?=$url?>';", <?=$ms?>);</script>   
	<?php } ?>
  </div>
</div>
</body>
</html>