<?php $this->display('header', 'system');?>
<!--tablesorter-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/tablesorter/style.css" />
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>
<!--contextmenu-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/contextMenu/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.contextMenu.js"></script>

<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/navigator/style.css"/>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.navigator.js"></script>

<link rel="stylesheet" type="text/css" href="apps/system/css/template.css"/>
<script type="text/javascript" src="apps/system/js/template_selector.js"></script>
<div class="operation_area">
	<div class="f_l w_500" id="navigator"></div>
    <div class="clear"></div>
</div>
<div style="height:330px; overflow-x:hidden; overflow-y:auto;" class="center t_c">
	<table width="100%" id="item_list" class="table_list" cellspacing="0" cellpadding="0" style="empty-cells:show;table-layout: fixed;">
	  <thead>
	    <tr>
	      <th width="180" class="bdr_3">名称</th>
	      <th class="t_c">别名</th>
	      <th width="60" class="t_c">大小</th>
	      <th width="130" class="t_c">修改日期</th>
	    </tr>
	  </thead>
	  <tbody></tbody>
	</table>
</div>
<div class="btn_area t_c">
	<input type="button" value="确定" class="button_style_1" onclick="App.check()"/>
	<input type="button" value="取消" class="button_style_1" onclick="App.cancel()"/>
</div>
<!--右键菜单-->
<ul id="right_menu_folder" class="contextMenu">
   <li class="edit"><a href="#App.open">打开</a></li>
</ul>
<ul id="right_menu_file" class="contextMenu">
   <li class="check"><a href="#App.rcheck">选择</a></li>
</ul>
<script type="text/javascript">
App.dir = "<?=$dir?>";
App.file = "<?=$file?>";
App.init('?app=<?=$app?>&controller=<?=$controller?>');
</script>
<?php $this->display('footer', 'system');