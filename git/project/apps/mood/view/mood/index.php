<?php $this->display('header', 'system');?>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/tablesorter/style.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/pagination/style.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/contextMenu/style.css"/>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.tablesorter.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.contextMenu.js"></script>
<script type="text/javascript" src="apps/mood/js/mood.js"></script>

<div class="bk_8"></div>
<div class="table_head">
	<div class="f_l">
		<input type="button" name="add" id="add" value="添加" class="button_style_2" onclick="mood.add();  return false;"/>
	</div>
</div>
<div class="bk_8"></div>
<table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list mar_l_8">
	<thead><tr>
		<th width="25%" class="t_c bdr_3"><span>表情名</span></th>
		<th width="25%" class="t_c">图标</th>
		<th width="10%" class="t_c">操作</th>
	</tr></thead>
	<tbody id="list_body"></tbody>
</table>
<!--右键菜单-->
<ul id="right_menu" class="contextMenu">
	<li class="edit"><a href="#mood.edit">编辑</a></li>
	<li class="delete"><a href="#mood.del">删除</a></li>
</ul>
<script type="text/javascript">
var manage_td  = '<img src="images/up.gif" alt="上移" title="上移" onclick="mood.sort_up({moodid});return false;" width="16" height="16" class="manage hand up"/>';
	manage_td += '&nbsp;<img src="images/down.gif" alt="下移" title="下移" onclick="mood.sort_down({moodid});return false;" width="16" height="16" class="hand down"/>';
	manage_td += '&nbsp;<img src="images/edit.gif" alt="编辑" width="16" height="16" class="manage" onclick="mood.edit({moodid})"/>';
	manage_td += '&nbsp;<img src="images/delete.gif" alt="删除" width="16" height="16" class="manage" onclick="mood.del(\'{moodid}\');"/>';

var row_template = '<tr id="row_{moodid}" height="38">';
	row_template +='	<td name="name" value="{name}" class="t_c" size="20">{name}<\/td>';
	row_template +='	<td name="image" value="{image}"  class="t_c"  size="30"><img src="<?=IMG_URL?>apps/mood/{image}" /><\/td>';
	row_template +='	<td class="t_c i_data" id="manage_{moodid}" name="manage" value="manage">'+manage_td+'<\/td>';
	row_template +='<\/tr>';

var tableApp = new ct.table('#item_list', {
	rowIdPrefix : 'row_',
	rightMenuId : 'right_menu',
	pageField : 'page',
	pageSize : 15,
	dblclickHandler : 'mood.edit',
	rowCallback     : 'init_row_event',
	template : row_template,
	baseUrl  : '?app=<?=$app?>&controller=<?=$controller?>&action=page'
});

function init_row_event(id, tr) { 
	tr.is(':first-child') && tr.find('.up').css("visibility","hidden");
	tr.is(':last-child') && tr.find('.down').css("visibility","hidden");
}

$(function() {
	tableApp.load();
});

</script>
<?php $this->display('footer','system');?>