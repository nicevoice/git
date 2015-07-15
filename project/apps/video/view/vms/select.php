<?php $this->display('header');?>
<!--tablesorter-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/tablesorter/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.tablesorter.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.ulist.js"></script>
<!--contextmenu-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/contextMenu/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.contextMenu.js"></script>
<!--pagination-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/pagination/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.pagination.js"></script>
<script type="text/javascript" src="apps/video/js/vms.js"></script>

<style type="text/css">
input.search_input_text {
    height: 15px;
    padding: 2px;
	margin:  0px;
	width: 120px;
}
input.button_style_1 {
	margin-top:-2px;
}
.piclist {
	width:100%;
	height:380px;
	background:#fff;
}
.piclist ul{margin-left:7px;}
.piclist ul li{float:left;height:115px;padding:5px 8px;text-align:center;border: 1px solid #FFF; border-radius: 3px 3px 3px 3px;}
.piclist ul li img{width:120px;height:90px;border:1px solid #ccc;padding:2px;}
.piclist p{width:125px;text-align:left;height:21px;line-height:21px;overflow:hidden;}
.piclist p span{display:block;width:500px;height:21px;line-height:21px;}
.piclist .trip{width:400px;height:30px;line-height:30px;color:red;text-align:center;}
.piclist .row_chked {background-color: #F3F7FD;border: 1px solid #BBD8FB; border-radius: 3px 3px 3px 3px;}
.piclist li{position:relative;}
.piclist li .duration{position:absolute;z-index:2;top:78px;left:11px;height:20px; width:120px; background:url(images/trans_bg.png) transparent;text-indent:5px;color:#fff;}
</style>

<div class="bk_8"></div>
<div class="tag_1" style="margin-bottom:0;">
	<ul class="tag_list" id="pagetab">
		<li><a href="javascript:vms.where($('#search_f'),1);" class="s_3">已完成</a></li>
		<li><a href="javascript:vms.where($('#search_f'),0);">转码中</a></li>
	</ul>
	<div class="f_r" style="width:125px">
      <button style="float:right;display:block;margin-right:10px;" type="button" class="button_style_1" onclick="vms.reload();">刷新</button>
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
          <td width="90"><input id="addtime_from" name="addtime_from" type="text" class="input_calendar search_input_text" value="<?=$addtime_from?>" size="12" style="width:85px;"/></td>
          <td class="t_c" width="15">至</td>
          <td width="90"><input id="addtime_to" name="addtime_to" type="text" class="input_calendar search_input_text" value="<?=$addtime_to?>" size="12" style="width:85px;"/></td>
          <td class="t_c" width="60"><input type="button" value="搜索" class="button_style_1" onclick="vms.where($('#search_f'));return false;" /></td>
        </tr>
      </table>
	  </form>
    </div>
</div>
<div class="bk_8"></div>

<div class="piclist" id="item_list">
	<ul></ul>
</div>
<div class="bk_8"></div>
<!--右键菜单-->
<ul id="right_menu" class="contextMenu">
   <li><a href="#vms.check">选择</a></li>
   <li class="preview"><a href="#vms.preview">预览</a></li>
   <li class="view"><a href="#vms.view">查看</a></li>
   <li class="edit"><a href="#vms.edit">编辑</a></li>
</ul>
<div class="table_foot">
	<div id="pagination" class="pagination f_r"></div>
	<p class="f_l">
		<button type="button" onclick="vms.check()" class="button_style_2" style="margin-right:10px;">确定</button>
		<button type="button" onclick="vms.reload()" class="button_style_1">刷新</button>
	</p>
</div>
<script type="text/javascript">
vms.init(0, '<?=$upurl?>', '<?=$playerurl?>', '<?=$filetype?>');
</script>
<?php $this->display('footer');