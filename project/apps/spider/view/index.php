<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$CONFIG['charset']?>" />
<title><?=$head['title']?></title>
<link rel="stylesheet" type="text/css" href="css/admin.css"/>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.js"></script>
<script type="text/javascript" src="<?php echo IMG_URL?>js/config.js"></script>
<script type="text/javascript" src="<?php echo IMG_URL?>js/cmstop.js"></script>


<!--dialog-->
<link rel="stylesheet" type="text/css" href="<?=IMG_URL?>js/lib/jquery-ui/dialog.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.ui.js"></script>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.dialog.js"></script>

<!--validator-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/validator/style.css"/>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.validator.js"></script>

<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/contextMenu/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.contextMenu.js"></script>

<link rel="stylesheet" type="text/css" href="<?=IMG_URL?>js/lib/autocomplete/style.css" />
<script src="<?=IMG_URL?>js/lib/cmstop.autocomplete.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/contextMenu/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.contextMenu.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/tree/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.tree.js"></script>
<link rel="stylesheet" type="text/css" href="apps/spider/css/style.css" />
<script type="text/javascript" src="apps/spider/js/scrollfeed.js"></script>
<script type="text/javascript" src="apps/spider/js/tasklist.js"></script>
<script type="text/javascript" src="apps/spider/js/spider.js"></script>
<script type="text/javascript">
$.validate.setConfigs({
    xmlPath:'<?=ADMIN_URL?>apps/<?=$app?>/validators/'
});
<?php if ($catid):?>
$(function(){
	App.init({catid:<?=$catid?>,catname:'<?=$catname?>'});
});
<?php else:?>
$(App.init);
<?php endif;?>
</script>
<div class="bk_10"></div>
<div class="mar_l_8">
    <div class="f_r mar_r_8"><button class="button_style_1" onclick="ct.assoc.open('?app=spider&controller=manager&action=addrule','newtab')">添加规则</button></div>
	<input type="text" id="keyword" class="f_l bdr_6 w_400" url="?app=spider&controller=spider&action=suggest&keyword=%s" />
	&nbsp;<button type="button" onclick="App.tempGrap()" class="button_style_1">获取</button>
    &nbsp;<button type="button" id="saveTempBtn" onclick="App.tempSave()" class="button_style_1">保存</button>
</div>
<div class="bk_8"></div>
<div class="box_10 mar_l_8 layout" style="width:950px;height:500px;" id="box">
	<div class="f_l bdr_r" style="width:179px;" id="leftBox">
		<h3 class="navi">
			<a href="javascript:App.addTask();" class="new f_r dis_b mar_5">
				<img src="images/space.gif" title="增加列表" height="16" width="16" />
			</a>
			<a href="javascript:App.refreshNav();" style="margin-top: 4px;" class="refresh f_r">
				<img width="16" height="18" src="images/space.gif" alt="刷新" title="刷新">
			</a>
		    <span class="f_l dis_b b">监听列表</span>
		</h3>
		<div id="leftList" style="height:475px;">
			<ul class="channel"></ul>
		</div>
	</div>
	<div class="f_l bdr_r" style="width:469px;" id="centerBox">
		<h3 class="navi tag_span">
			<a href="javascript:App.refreshItem();" style="margin-top: 4px;" class="refresh f_r">
				<img width="16" height="18" src="images/space.gif" alt="刷新" title="刷新">
			</a>
			<span class="f_l" index="0">最新(<em>0</em>)</span>
			<span class="f_l" index="1">已查看(<em>0</em>)</span>
			<span class="f_l" index="2">已采集(<em>0</em>)</span>
		</h3>
		<div style="height:435px;" id="centerList">
			<ul class="viewer-container"></ul>
			<ul class="viewer-container"></ul>
			<ul class="viewer-container"></ul>
		</div>
		<div class="btn_area ctrl_area">
			<button type="button" class="button_style_1" onclick="App.addAlltask()">全选</button>
			<button type="button" class="button_style_1" onclick="App.viewedAll()">全部标记已读</button>
		</div>
	</div>
	<div class="f_l" style="width:300px;" id="rightBox">
		<h3 class="navi"><span>已选择(<em>0</em>)</span></h3>
		<div style="height:435px;" id="rightList">
			<table width="100%" cellspacing="0" cellpadding="0" class="table_list">
				<tbody></tbody>
			</table>
		</div>
		<div class="btn_area ctrl_area">
			<button type="button" class="button_style_1" style="float:right" onclick="App.clearTask()">清空</button>
			<?=element::status('status', 'status', $memstatus)?>
			<button type="button" class="button_style_1" onclick="App.run()">采集</button>
			<button type="button" class="button_style_1" style="display:none" onclick="App.pause(this)">暂停</button>
		</div>
	</div>
</div>
<ul id="right_menu" class="contextMenu">
   <li class="edit"><a href="#editTask">编辑</a></li>
   <li class="delete"><a href="#delTask">删除</a></li>
</ul>
</body>
</html>