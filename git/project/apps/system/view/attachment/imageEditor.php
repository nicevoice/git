<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$CONFIG['charset']?>" />
<title>图片编辑器</title>
<link href="apps/system/css/image-editor.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="<?=IMG_URL?>js/lib/Jcrop/style.css" type="text/css" />
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/config.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.Jcrop.js"></script>
<script type="text/javascript" src="apps/system/js/image-editor.js"></script>
<script type="text/javascript">
$(function(){
	Editor.init('<?=$imageurl?>');
});
</script>
</head>
<body>
<div id="header">
	<div class="ctrl-button">
		<span></span>
		<b></b>
	</div>
	<div class="ctrl-button">
		<span></span>
		<b></b>
	</div>
	<div class="ctrl-button">
		<span></span>
		<b></b>
	</div>
	<div class="ctrl-button">
		<span></span>
		<b></b>
	</div>
	<div class="ctrl-button">
		<span></span>
		<b></b>
	</div>
	<div class="ctrl-button">
		<span></span>
		<b></b>
	</div>
</div>
<div id="center">
	<div id="zoom"></div>
	<div id="workarea">
		<div id="bound"></div>
	</div>
</div>
<div id="bottom"></div>
</body>
</html>