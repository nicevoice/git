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

<div class="bk_10"></div>
<div class="table_head">
  <div class="search search_icon f_r">
	<form id="search_f" onsubmit="tableApp.load($('#search_f'));parentid = '';return false;">
		<input type="text" name="keywords" id="keywords" value="<?=$keywords?>" size="30"/>
		<a href="javascript:;" style="outline:none" onfocus="this.blur()" onclick="tableApp.load($('#search_f'));parentid = '';return false;" title="搜索">搜索</a>
		<a href="javascript:;" style="outline:none" onfocus="this.blur()" class="more_search" onclick="search_dialog();" title="高级搜索">高级搜索</a>
	</form>
  </div>
  <div class="f_l">
	<input type="button" name="add" id="add" value="添加" class="button_style_2 f_l" onclick="add_dialog();  return false;"/>
    <input type="button" name="repair" id="repair" value="修复" class="button_style_2 f_l" onclick="repair();  return false;"/>
    <button id="returnUp" class="button_style_1 f_l dis_n" onclick="returnUp();">返回上一级</button>
  </div>
</div>
<div class="bk_8"></div>
<table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list mar_l_8">
	<thead><tr>
		<th width="30" class="t_l bdr_3"><input type="checkbox" id="check_all" /></th>
		<th width="7%" class="sorter"><div>排序</div></th>
		<th class="sorter"><div>菜单名称</div></th>
		<th width="50%">链接地址</th>
		<th width="10%">管理操作</th>
	</tr></thead>
	<tbody></tbody>
</table>
<div class="table_foot">
	<p class="f_l">
		<input type="button" name="button" id="button" onclick="del_row(); return false;" value="删 除" class="button_style_1"/>
	</p>
</div>

<!--右键菜单-->
<ul id="right_menu" class="contextMenu">
	<li class="edit"><a href="#common_edit">编辑</a></li>
	<li class="delete"><a href="#del_row">删除</a></li>
</ul>

<script type="text/javascript">
$.validate.setConfigs({
	xmlPath:'apps/system/validators/menu/'
});
var parentid = '';
var row_template = '<tr id="row_{menuid}">';
row_template +='	<td><input type="checkbox" class="checkbox_style" name="chk_row" id="chk_row_{menuid}" value="{menuid}" /></td>';
row_template +='	<td name="sort" value="{sort}" size="3">{sort}</td>';
row_template +='	<td name="name" value="{name}" id="name" size="15"><a href="javascript:;">{name}</a></td>';
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
	baseUrl  : '?app=<?=$app?>&controller=<?=$controller?>&action=page'
});

function init_row_event(id, tr) {
	tr.find('img.del_row').click(function(){
		del_row(id,tr);
	});
	tr.find('img.common_edit').click(function(){
		common_edit(id,tr);
	});
	tr.find('#name a').click(function(){
		tableApp.load('parentid='+id);
		parentid = id;
	});
}
function json_loaded(json) {
	parentid = json.parentid;
	if(!parentid) $('#returnUp').hide();
	else  $('#returnUp').show();
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
function returnUp() {
	tableApp.load('menuid='+parentid);
}
function common_edit(id,tr) {
	var url = '?app=<?=$app?>&controller=<?=$controller?>&action=edit&menuid='+id;
	tr.trigger('check');
	ct.form('编辑菜单',url,400,180,function(json){
		tableApp.updateRow(id, json.data);
		return true;
	}).dialog('option','modal',true);
}
function add_dialog() {
	ct.form('添加菜单','?app=<?=$app?>&controller=<?=$controller?>&action=add&parentid='+parentid,400,180,function(json){
		tableApp.addRow(json.data);
		return true;
	});
}
function search_dialog() {
	ct.ajax('高级搜索', '?app=<?=$app?>&controller=<?=$controller?>&action=search', 400, 180, null, function(dialog){
		tableApp.load(dialog.find('form'));
		parentid = '';
		return true;
	});
}
function repair() {
    ct.startLoading('center', '修复中，请稍后...');
    $.getJSON('?app=system&controller=menu&action=repair', function(json) {
        ct.endLoading();
        if (json.state) {
            ct.alert("操作成功", 'ok');
        } else {
            ct.error(json.error || '修复失败');
        }
    });
}
</script>
<?php $this->display('footer', 'system');