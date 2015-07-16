<?php $this->display('header','system');?>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/tablesorter/style.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/pagination/style.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/contextMenu/style.css"/>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.tablesorter.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.contextMenu.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.pagination.js"></script>
<div class="bk_10"></div>
<div class="table_head">
	<form method="GET" name="<?=$app?>_<?=$controller?>_add" id="<?=$app?>_<?=$controller?>_add" action="?app=<?=$app?>&controller=<?=$controller?>&action=add">
		<input type="button" name="add" id="add" value="添加" class="button_style_2 f_l" onclick="add_dialog();  return false;"/>
	</form>
</div>
<div class="bk_8"></div>
<table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list mar_l_8">
	<thead><tr>
		<th width="7%" class="sorter bdr_3"><div>ID</div></th>
		<th>类型名称</th>
		<th width="50%" class="sorter"><div>留言条数</div></th>
		<th width="80" class="sorter"><div>排序</div></th>
		<th width="120">管理操作</th>
	</tr></thead>
	<tbody id="list_body"></tbody>
</table>
<!--右键菜单-->
<ul id="right_menu" class="contextMenu">
	<li class="edit"><a href="#common_edit">编辑</a></li>
	<li class="delete"><a href="#del_row">删除</a></li>
</ul>

<script type="text/javascript">
var row_template = '<tr id="row_{typeid}">';
row_template +='	<td class="t_r">{typeid}</td>';
row_template +='	<td class="t_c">{name}</td>';
row_template +='	<td class="t_r">{count}</td>';
row_template +='	<td class="t_r">{sort}</td>';
row_template +='	<td class="t_c" ><img src="images/edit.gif" title="编辑" alt="编辑" width="16" height="16" class="manage common_edit" />&nbsp;<img src="images/delete.gif" title="删除"alt="删除" width="16" height="16" class="manage del_row"/></td>';
row_template +='</tr>';
var tableApp = new ct.table('#item_list', {
	rowIdPrefix : 'row_',
	rightMenuId : 'right_menu',
	pageField : null,
	pageSize : null,
	dblclickHandler : 'common_edit',
	rowCallback     : 'init_row_event',
	template : row_template,
	baseUrl  : '?app=<?=$app?>&controller=<?=$controller?>&action=page'
});

function init_row_event(id, tr) {
	tr.find('img.del_row').click(function(){
		del_row(id,tr);
	});
	tr.find('img.common_edit').click(function(){
		common_edit(id,tr);
	});
}
tableApp.load();
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
function common_edit(id,tr) {
	var url = '?app=<?=$app?>&controller=<?=$controller?>&action=edit&typeid='+id;
	tr.trigger('check');
	ct.form('编辑类别',url,360,180,function(json){
		tableApp.updateRow(id, json.data);
		return true;
	}).dialog('option','modal',true);
}
function add_dialog() {
	ct.form('添加类别','?app=<?=$app?>&controller=<?=$controller?>&action=add',360,180,function(json){
		tableApp.addRow(json.data);
		return true;
	});
}
</script>
<?php $this->display('footer', 'system');?>