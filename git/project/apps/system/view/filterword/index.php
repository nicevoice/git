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

<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/dropdown/style.css"/>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.dropdown.js"></script>

<div class="bk_10"></div>
<div class="table_head">
	<div class="search search_icon f_r">
		<form method="GET" name="search_f" id="search_f" action="" onsubmit="tableApp.load($('#search_f'));return false;">
		<input type="text" name="keywords" id="keywords" value="<?=$keywords?>" size="30"/>
		<a href="javascript:;" onclick="tableApp.load($('#search_f'));return false;" title="搜索">搜索</a>
		<a href="javascript:void(0);" class="more_search" onclick="return false;" title="高级搜索">高级搜索</a>
		</form>
	</div>
	<div class="f_l">
		<input type="button" name="add" id="add" value="添加" class="button_style_2 f_l" onclick="add_dialog();  return false;"/>
	</div>
</div>
<div class="bk_8"></div>
<table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list mar_l_8">
	<thead><tr>
		<th width="30" class="t_l bdr_3"><input type="checkbox" id="check_all" /></th>
		<th width="60" class="ajaxer"><div name="filterwordid">ID</div></th>
		<th class="ajaxer"><div name="pattern">不良词语</div></th>
		<th width="30%">替换字符</th>
		<th width="10%">管理操作</th>
	</tr></thead>
	<tbody ></tbody>
</table>
<div class="table_foot">
	<div id="pagination" class="pagination f_r"></div>
	<p class="f_l">
		<input type="button" name="button" id="button" onclick="del_row(); return false;" value="删 除" class="button_style_1"/>
	</p>
</div>
<!--右键菜单-->
<ul id="right_menu" class="contextMenu">
	<li class="new"><a href="#add_dialog">添加</a></li>
	<li class="edit"><a href="#common_edit">编辑</a></li>
	<li class="delete"><a href="#del_row">删除</a></li>
</ul>

<script type="text/javascript">
$.validate.setConfigs({
	xmlPath:'apps/system/validators/filterword/'
});
var row_template = '<tr id="row_{filterwordid}">';
	row_template +='	<td><input type="checkbox" class="checkbox_style" name="chk_row" id="chk_row_{filterwordid}" value="{filterwordid}" /></td>';
	row_template +='	<td class="t_r">{filterwordid}</td>';
	row_template +='	<td name="pattern" value="{pattern}" size="20">{pattern}</td>';
	row_template +='	<td name="replacement" value="{replacement}" size="30">{replacement}</td>';
	row_template +='	<td class="t_c"><img src="images/edit.gif" alt="编辑" width="16" height="16" class="manage common_edit"/> &nbsp;<img src="images/delete.gif" alt="删除" width="16" height="16" class="manage del_row"/></td>';
	row_template +='</tr>';
	
var tableApp = new ct.table('#item_list', {
	rowIdPrefix : 'row_',
	rightMenuId : 'right_menu',
	pageField : 'page',
	pageSize : 15,
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
	var url = '?app=<?=$app?>&controller=<?=$controller?>&action=edit&filterwordid='+id;
	tr.trigger('check');
	ct.form('编辑',url,360,180,function(json){
		tableApp.updateRow(id, json.data);
		return true;
	}).dialog('option','modal',true);
}
function add_dialog() {
	ct.form('添加','?app=<?=$app?>&controller=<?=$controller?>&action=add',360,180,function(json){
		tableApp.addRow(json.data);
		return true;
	});
}
</script>
<?php $this->display('footer', 'system');