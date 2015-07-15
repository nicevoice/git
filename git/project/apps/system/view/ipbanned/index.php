<?php $this->display('header', 'system');?>
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
<!-- 时间选择器 -->
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.datepicker.js"></script>
<link href="<?=IMG_URL?>js/lib/datepicker/style.css" rel="stylesheet" type="text/css" />

<div class="bk_10"></div>
<div class="table_head">
	<form method="GET" name="<?=$app?>_<?=$controller?>_add" id="<?=$app?>_<?=$controller?>_add" action="?app=<?=$app?>&controller=<?=$controller?>&action=add">
		<input type="button" name="add" id="add" value="添加" class="button_style_2 f_l" onclick="add_dialog();  return false;"/>
	</form>
</div>
<div class="bk_8"></div>
<table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list mar_l_8">
	<thead><tr>
		<th width="30" class="t_l bdr_3"><input type="checkbox" id="check_all" /></th>
		<th class="ajaxer"><div name="ip">IP</div></th>
		<th><div name="iplocation">IP所在地</div></th>
		<th class="ajaxer"><div name="expires">解封时间</div></th>
		<th width="150">管理操作</th>
	</tr></thead>
	<tbody></tbody>
</table>
<div class="table_foot">
	<div id="pagination" class="pagination f_r"></div>
	<p class="f_l"><input type="button" id="button" onclick="del_row(); return false;" value="删除" class="button_style_1"/></p>
</div>
<!--右键菜单-->
<ul id="right_menu" class="contextMenu">
	<li class="edit"><a href="#common_edit">编辑</a></li>
	<li class="del"><a href="#del_row">删除</a></li>
</ul>
<script type="text/javascript">
$.validate.setConfigs({
	xmlPath:'apps/system/validators/ipbanned/'
});

var row_template = '<tr id="row_{ip}">';
row_template +='	<td><input type="checkbox" class="checkbox_style" name="chk_row" id="chk_row_{ip}" value="{ip}"/></td>';
row_template +='	<td name="ip" value="{ip}">{ip}</td>';
row_template +='	<td name="ip" value="{iplocation}">{iplocation}</td>';
row_template +='	<td class="t_c"  name="expires" value="{expires}">{expires}</td>';
row_template +='	<td class="t_c" id="manage_{ip}" name="manage" value="manage"><img src="images/edit.gif" alt="编辑" width="16" height="16" class="manage common_edit" /> &nbsp;<img src="images/delete.gif" alt="删除" width="16" height="16" class="hand del_row" /></td>';
row_template +='</tr>';
var tableApp = new ct.table('#item_list', {
	rowIdPrefix : 'row_',
	rightMenuId : 'right_menu',
	pageField : 'page',
	pageSize : 15,
	dblclickHandler : 'common_edit',
	rowCallback     : 'row_init',
	template : row_template,
	baseUrl  : '?app=<?=$app?>&controller=<?=$controller?>&action=page'
});
tableApp.load();

function common_edit(id,tr) {
	var url = '?app=<?=$app?>&controller=<?=$controller?>&action=edit&ip='+id;
	tr.trigger('check');
	ct.form('编辑ip',url,360,200,function(json){
		tableApp.updateRow(id, json.data);
		return true;
	}).dialog('option','modal',true);
}
function row_init(id,tr) {
	tr.find('img.del_row').click(function(){
		del_row(id,tr);
	});
	tr.find('img.common_edit').click(function(){
		common_edit(id,tr);
	});
}
function del_row(id) {
	var msg;
	if (id === undefined) {
		id = tableApp.checkedIds();
		if (!id.length) {
			ct.warn('请选中要删除项');
			return;
		}
		msg = '确定删除选中的<b style="color:red">'+id.length+'</b>条记录吗？';
	} else {
		msg = '确定删除编号为<b style="color:red">'+id+'</b>的记录吗？';
	}
	ct.confirm(msg,function(){
		var data = 'id='+id;
		$.getJSON('?app=<?=$app?>&controller=<?=$controller?>&action=delete',data,function(json){
			json.state
			 ? (ct.warn('删除完毕'), tableApp.deleteRow(id))
			 : ct.warn(json.error);
		});
	});
}

function add_dialog() {
	ct.form('添加行','?app=<?=$app?>&controller=<?=$controller?>&action=add',360,180,function(json) {
		if(json.state)
			tableApp.addRow(json.data);
		else
			ct.error(json.error);
		return true;
	});
}

function search_dialog() {
	ct.ajax('高级搜索', '?app=<?=$app?>&controller=<?=$controller?>&action=search', 360, 180, null, function(dialog){
		tableApp.load(dialog.find('form'));
		return true;
	});
}
</script>
<?php $this->display('footer', 'system');