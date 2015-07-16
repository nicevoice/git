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

<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/tree/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.tree.js"></script>

<script type="text/javascript" src="apps/push/js/scrollfeed.js"></script>
<script type="text/javascript" src="apps/push/js/tasklist.js"></script>
<script type="text/javascript" src="apps/push/js/push.js"></script>
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
<style type="text/css">
	div.ctrl_area {
		height:38px;line-height:40px;padding:0;margin:0;border:0;
		text-align:left;
	}
	div.ctrl_area button.button_style_1,
	div.ctrl_area select {
		margin:0;
		margin-left:6px;
	}
	h3.navi {
		height:23px;
		line-height:23px;
		border:0;
		padding:0 5px;
		margin:0;
	}
	h3.tag_span {
		padding-left:15px;
	}
	h3.tag_span span.active {
		background-color:#fff;
		border-left:1px solid #9AC4DC;
		border-right:1px solid #9AC4DC;
		padding:0 9px;
	}
	#leftList, #centerList, #rightList {
		overflow-x:hidden;
		overflow-y:auto;
	}
	
	.bdr_r {
		border-right:1px solid #C2CFF1;
	}
	
	#centerList {
		background-color:#EBEFF9;
		overflow-y:scroll;
	}
	
	.channel {
		margin-top:5px;
	}
	.channel li {
		padding-left:16px;
		cursor:pointer;
		height:22px;
		line-height:22px;
	}
	.channel li:hover,
	.channel li.hover {
		background-color:#fffddd;
	}
	.channel li.focus {
		background-color:#C2CFF1;
	}
	
	.viewer-container {
		margin-left:5px;
		display:none;
	}
	.viewer-container li {
		background-color:white;
		border:2px solid white;
	}
	.viewer-container li.focus {
		border-color:#6688EE;
	}
	.viewer-container li .item-header {
		font-weight:bold;
		cursor:pointer;
		background-color:transparent;
		background-image:none;
		border-bottom:1px solid #E0E0E8;
		height:28px;
		line-height:28px;
	}
	.viewer-container li.expanded .item-header {
		background-color:#F3F5FC;
	}
	.viewer-container li .item-header a {
		padding-left:18px;
	}
	.viewer-container li.pushed .item-header,
	.viewer-container li.viewed .item-header {
		font-weight:normal;
	}
	.viewer-container li.pushed .item-header a {
		background:url(images/sh.gif) no-repeat left center;
	}
	.item-header .header-left {
		float:left;
		display:inline-block;
		width:65%;
		overflow:hidden;
		height:25px;
	}
	.item-header .header-right {
		float:right;
		display:inline-block;
	}
	.header-right img {
		margin:auto 2px;
	}
	.viewer-container li.hover,
	.viewer-container li:hover {
		background-color:#fffddd;
	}
	.viewer-container li div.item-desc {
		clear:both;
		color:#2F2F2F;
		font-size:13px;
		line-height:125%;
		margin-top:10px;
		overflow:auto;
		padding-left:15px;
		padding-right:15px;
		table-layout:fixed;
		display:none;
	}
	.viewer-container li.expanded div.item-desc {
		display:block;
	}
	
	.table_list td span.catname {
		cursor:pointer;
	}
	
	#saveTempBtn {
		display:none;
	}
</style>
<div class="bk_8"></div>
<div class="mar_l_8 clear">
  <input type="button" name="add" id="add" value="添加规则" class="button_style_4 f_l" onclick="ct.assoc.open('?app=push&controller=push&action=add','newtab')"/>
</div>
<div class="bk_5"></div>
<div class="box_10 mar_l_8 layout" style="width:950px;height:500px;" id="box">
	<div class="f_l bdr_r" style="width:179px;" id="leftBox">
		<h3 class="navi">
			<a href="javascript:App.addTask();" class="new f_r dis_b mar_5">
				<img src="images/space.gif" title="添加监听任务" height="16" width="16" />
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
			<span class="f_l" index="0">所有(<em>0</em>)</span>
			<span class="f_l" index="1">已查看(<em>0</em>)</span>
			<span class="f_l" index="2">已推送(<em>0</em>)</span>
		</h3>
		<div style="height:435px;" id="centerList">
			<ul class="viewer-container"></ul>
			<ul class="viewer-container"></ul>
			<ul class="viewer-container"></ul>
		</div>
		<div class="btn_area ctrl_area">
			<button type="button" class="button_style_1" onclick="App.viewedAll()">全部标记已读</button>
			<button type="button" class="button_style_1" onclick="App.addAlltask()">全选</button>
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
			<?=element::status('status', 'status', $memstatus)?>
			<button type="button" class="button_style_1" onclick="App.run()">推送</button>
			<button type="button" class="button_style_1 mar_l_8" onclick="App.clearTask()">清空</button>
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