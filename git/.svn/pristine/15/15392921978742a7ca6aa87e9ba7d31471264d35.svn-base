<?php $this->display('header');?>
<!--tablesorter-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/tablesorter/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.tablesorter.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>
<!--contextmenu-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/contextMenu/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.contextMenu.js"></script>
<!--pagination-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/pagination/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.pagination.js"></script>
<script type="text/javascript" src="apps/video/js/vms.js"></script>

<style type="text/css">
.upload-progress {
	font-size:12px; font-family:Arial, Verdana,"宋体";
	padding:14px;
	border:1px solid #C3E1F0;
	background-color:#F2F8FD;
	position:fixed;
	left:50%;
	top:50%;
	margin-left:-100px;
	margin-top:-80px;
	-moz-border-radius:5px;
	-webkit-border-radius:5px;
	border-radius:5px;
	z-index:9999;
	text-align:center;
	display:none;
}
.upload-progress .bar {
	width:168px;height:28px;
	border:1px solid #ccc;
	background:#fff;text-align:left;position:relative;
}
.upload-progress .bar .percent {
	position:absolute; top:0; left:40px;
	width:80px;
	height:28px;
	line-height:28px;text-align:center;
	font-family:Tahoma, Verdana, Arial; font-size:14px;
	font-weight:bold; color:#f60;
}
.upload-progress .bar .progress {
	background-color:#DDEEF1;
	height:28px;width:0px;
	border-right:1px solid #9FCCE9;
}
td div.icon {background: url(<?php echo IMG_URL?>js/lib/dropdown/bg.gif) no-repeat scroll 0 -50px transparent;	margin-right: 3px;	width: 16px;height: 20px;float: left;}
td div.video {background-position: 0 -125px;}
</style>

<div class="bk_8"></div>
<div class="tag_1" style="margin-bottom:0;">
	<ul class="tag_list" id="pagetab">
		<li><a href="javascript:vms.where($('#search_f'),1);" class="s_3">已完成</a></li>
		<li><a href="javascript:vms.where($('#search_f'),0);">转码中</a></li>
	</ul>
	<div class="f_r" style="width:135px">
      <button style="float:right;display:block;margin-right:20px;" type="button" class="button_style_1" onclick="vms.reload();">刷新</button>
      <span id="up" class="button">上传视频</span>
    </div>
</div>
<div class="table_head operation_area" style="border-bottom:0;background:#F3F9FA;">
	<div class="f_l search_icon" style="width:470px;">
	  <form id="search_f" onsubmit="return false;">
	  <input type="hidden" id="status" name="status" value="1" />
      <table cellpadding="0" cellspacing="0">
        <tr>
          <td width="160"><input type="text" size="12" name="keywords" class="search_input_text" style="width:145px" value="<?=$keywords?>" /></td>
          <td width="90"><input id="addtime_from" name="addtime_from" type="text" class="input_calendar search_input_text" value="<?=$addtime_from?>" size="12" style="width:65px;"/></td>
          <td class="t_c" width="15">至</td>
          <td width="90"><input id="addtime_to" name="addtime_to" type="text" class="input_calendar search_input_text" value="<?=$addtime_to?>" size="12" style="width:65px;"/></td>
          <td class="t_c" width="60"><input type="button" value="搜索" class="button_style_1" onclick="vms.where($('#search_f'));return false;" /></td>
        </tr>
      </table>
	  </form>
    </div>
</div>
<div class="bk_8"></div>

<table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list" style="margin-left:6px;">
  <thead>
    <tr>
		<th width="30" class="t_l bdr_3">
			<input type="checkbox" />
		</th>
		<th class="sorter"><div>名称</div></th>
		<th class="sorter" width="100"><div>时长(m)</div></th>
		<th class="sorter" width="100"><div>状态</div></th>
		<th class="sorter" width="150"><div>时间</div></th>
		<th width="80">管理操作</th>
    </tr>
  </thead>
  <tbody></tbody>
</table>
<!--右键菜单-->
<ul id="right_menu" class="contextMenu">
   <li class="preview"><a href="#vms.preview">预览</a></li>
   <li class="view"><a href="#vms.view">查看</a></li>
   <li class="edit"><a href="#vms.edit">编辑</a></li>
   <li class="del"><a href="#vms.del">删除</a></li>
</ul>
<div class="table_foot">
	<div id="pagination" class="pagination f_r"></div>
	<p class="f_l">
		<button type="button" onclick="vms.del()" class="button_style_1">删除</button>
		<button type="button" onclick="vms.reload()" class="button_style_1">刷新</button>
	</p>
</div>
<script type="text/javascript">
$(document).ready(function() {
	vms.init(1, '<?=$upurl?>', '<?=$playerurl?>', '<?=$filetype?>');
});
</script>
<?php $this->display('footer');