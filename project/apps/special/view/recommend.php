<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$CONFIG['charset']?>" />
<title>推送到专题模块</title>
<link rel="stylesheet" type="text/css" href="css/admin.css"/>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.js"></script>
<script type="text/javascript" src="<?php echo IMG_URL?>js/config.js"></script>
<script type="text/javascript" src="<?php echo IMG_URL?>js/cmstop.js"></script>
</head>
<body>
<div class="bk_5"></div>
<div class="spe-push">
    <div class="title_right bdr_r f_r">
        <h3 class="layout">
            <span class="f_l">推送区块</span>
        </h3>
    </div>
    <div class="title_left bdr_r f_l">
        <h3 class="layout">
            <span class="f_l">有推送区块的专题</span>
        </h3>
    </div>
    <div class="clear"></div>
	<div class="spe-recommend-cont">
		<div class="date-filter">
			<div class="date-list">
				<a href="" range="all" class="current">全部</a>
				<a href="" range="today">今日</a>
				<a href="" range="tomorrow">昨日</a>
				<a href="" range="week">本周</a>
				<a href="" range="month">本月</a>
			</div>
			<div class="search">
				<input type="text" name="" />
			</div>
		</div>
		<div class="clear"></div>
		<div class="checked-area"><ul></ul></div>
		<div class="clear"></div>
		<div class="check-area"><ul></ul></div>
	</div>
	<div class="spe-recommend-ctrl"></div>
	<div class="clear"></div>
</div>
<div class="btn_area">
	<button type="button" action="ok" class="button_style_1">确定</button>
	<button type="button" action="cancel" class="button_style_1">取消</button>
</div>
<script src="apps/special/js/pushctrl.js" type="text/javascript"></script>
<script type="text/javascript">
init(<?=$checkedPlace?$checkedPlace:'{}'?>,<?=$checkedContent?$checkedContent:'[]'?>);
</script>
</body>
</html>