<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$CONFIG['charset']?>" />
<title><?=$head['title']?></title>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/config.js"></script>
<link rel="stylesheet" type="text/css" href="<?=IMG_URL?>js/cmstop/style.css" />
<script type="text/javascript" src="<?=IMG_URL?>js/cmstop.js"></script>

<link rel="stylesheet" type="text/css" href="<?=IMG_URL?>js/lib/jquery-ui/dialog.css" />
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.ui.js"></script>

<link rel="stylesheet" type="text/css" href="<?=IMG_URL?>js/lib/contextMenu/style.css" />
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.contextMenu.js"></script>

<link rel="stylesheet" type="text/css" href="<?=IMG_URL?>js/lib/navigator/style.css" />
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.navigator.js"></script>

<link rel="stylesheet" type="text/css" href="apps/system/css/psn-selector.css" />
<script type="text/javascript" src="apps/system/js/psn-selector.js"></script>
</head>
<body>
<div id="head">
	<div id="ctrl">
		<span action="mkdir" class="mkdir" title="新建目录"></span>
		<span action="mkfile" class="mkfile" title="新建文件"></span>
	</div>
	<div id="navigator"></div>
</div>
<div id="center">
	<ul id="container">
		<li class="dir" path="" title="dddd">
			<div></div>
			<span>名称啊</span>
		</li>
		<li class="file" path="" title="dddd">
			<div></div>
			<span>名称啊</span>
		</li>
	</ul>
</div>
<div id="bottom">
	<button type="button" action="check">确定</button>
	<button type="button" action="cancel">取消</button>
</div>
<ul id="master-dir-menu" class="contextMenu">
	<li class="check"><a href="#check">选择</a></li>
	<li class="open"><a href="#open">打开</a></li>
	<li class="rename"><a href="#rename">重命名</a></li>
	<li class="delete"><a href="#remove">删除</a></li>
</ul>
<ul id="slave-dir-menu" class="contextMenu">
	<li class="open"><a href="#open">打开</a></li>
	<li class="rename"><a href="#rename">重命名</a></li>
	<li class="delete"><a href="#remove">删除</a></li>
</ul>
<ul id="master-file-menu" class="contextMenu">
	<li class="check"><a href="#check">选择</a></li>
	<li class="rename"><a href="#rename">重命名</a></li>
	<li class="delete"><a href="#remove">删除</a></li>
</ul>
<ul id="slave-file-menu" class="contextMenu">
	<li class="rename"><a href="#rename">重命名</a></li>
	<li class="delete"><a href="#remove">删除</a></li>
</ul>
<ul id="main-menu" class="contextMenu">
	<li class="mkdir"><a href="#mkdir">新建目录</a></li>
	<li class="mkfile"><a href="#mkfile">新建文件</a></li>
</ul>
<script type="text/javascript">
PSN.init('<?=$type?>', '<?=$path?>');
</script>

</body>
</html>