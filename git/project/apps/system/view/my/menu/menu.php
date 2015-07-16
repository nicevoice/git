<?php $this->display('header', 'system');?>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/tablesorter/style.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/pagination/style.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/contextMenu/style.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/dropdown/style.css"/>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.dropdown.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.tablesorter.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.contextMenu.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.pagination.js"></script>

<div class="bk_10"></div>
<div class="table_head">
  <div class="search search_icon f_r">
  	<form method="GET" name="search_f" id="search_f" action="" onsubmit="tableApp.load($('#search_f'));parentid = '';return false;">
        <input type="text" name="keywords" id="keywords" value="<?=$keywords?>" size="30"/>
        <a href="javascript:;" onclick="tableApp.load($('#search_f'));parentid = '';return false;" title="搜索">搜索</a><a href="javascript:;" class="more_search" onclick="search_dialog();" title="高级搜索">高级搜索</a>
      </form>
  </div>
  <form method="GET" name="<?=$app?>_<?=$controller?>_add" id="<?=$app?>_<?=$controller?>_add" action="?app=<?=$app?>&controller=<?=$controller?>&action=add">
  <input type="button" name="add" id="add" value="添加" class="button_style_2 f_l" onclick="add_dialog();  return false;"/></form>
</div>
<div class="clear"></div>
<table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list mar_l_8 mar_t_5">
	<thead><tr>
		<th width="30" class="t_l bdr_3"><input type="checkbox" id="check_all" /></th>
		<th width="7%" class="ajaxer"><div name="sort">排序</div></th>
		<th class="ajaxer"><div name="name">菜单名称</div></th>
		<th width="50%">链接地址</th>
		<th width="10%">管理操作</th>
	</tr></thead>
	<tbody></tbody>
</table>
<div class="table_foot">
	<div id="pagination" class="pagination f_r"></div>
	<div class="f_r"> 共有<span id="pagetotal">0</span>条记录&nbsp;&nbsp;&nbsp;每页
	<input type="text" name="pagesize" size=3 id="pagesize" value=""/> 条&nbsp;&nbsp;</div>
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
	xmlPath:'apps/system/validators/my/menu/'
});

var row_template = '<tr id="row_{id}">';
row_template +='	<td><input type="checkbox" class="checkbox_style" name="chk_row" id="chk_row_{id}" value="{id}" /></td>';
row_template +='	<td name="sort" value="{sort}" size="3">{sort}</td>';
row_template +='	<td name="name" value="{name}" id="name" size="15"><a href="{url}">{name}</a></td>';
row_template +='	<td name="url" value="{url}" size="60">{url}</td>';
row_template +='	<td class="t_c" ><img src="images/edit.gif" alt="编辑" width="16" height="16" class="manage common_edit"/> &nbsp;<img src="images/delete.gif" alt="删除" width="16" height="16" class="manage del_row");"/></td>';
row_template +='</tr>';

var tableApp = new ct.table('#item_list', {
	rowIdPrefix : 'row_',
	rightMenuId : 'right_menu',
	pageField : 'page',
	pageSize : 15,
	dblclickHandler : 'common_edit',
	rowCallback     : 'init_row_event',
	jsonLoaded : 'json_loaded',
	template : row_template,
	baseUrl  : '?app=<?=$app?>&controller=<?=$controller?>&action=menupage'
});


function init_row_event(id, tr) {
	tr.find('img.del_row').click(function(){
		del_row(id,tr);
	});
	tr.find('img.common_edit').click(function(){
		common_edit(id,tr);
	});
}
function json_loaded(json) {
	$('#pagetotal').html(json.total);
}

$(function() {
	tableApp.load();
	$('#pagesize').val(tableApp.getPageSize());
	$('#pagesize').blur(function(){
		var p = $(this).val();
		tableApp.setPageSize(p);
		tableApp.load();
	});
});
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
	var url = '?app=<?=$app?>&controller=<?=$controller?>&action=edit&menuid='+id;
	tr.trigger('check');
	ct.form('编辑常用操作',url,400,180,function(json){
		tableApp.updateRow(id, json.data);
		return true;
	}).dialog('option','modal',true);
}
function add_dialog() {
	ct.form('添加常用操作','?app=<?=$app?>&controller=<?=$controller?>&action=add',400,180,function(json){
		tableApp.addRow(json.data);
		return true;
	});
}
function search_dialog() {
	ct.ajax('高级搜索', '?app=<?=$app?>&controller=<?=$controller?>&action=search', 400, 180, null, function(dialog){
		tableApp.load(dialog.find('form'));
		return true;
	});
}
</script>
<?php $this->display('footer', 'system');