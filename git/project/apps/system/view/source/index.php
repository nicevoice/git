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
		<form method="GET" name="search_f" id="search_f" action="" onsubmit="tableApp.load($('#search_f'));return false;">
			<input type="text" name="keywords" id="keywords" value="<?=$keywords?>" size="30"/>
			<a href="javascript:;" onclick="tableApp.load($('#search_f'));return false;" title="搜索">搜索</a><a href="javascript:;" class="more_search" onclick="search_dialog();return false;" title="高级搜索">高级搜索</a>
		</form>
	</div>
	<div class="f_l">
		<input type="button" name="add" id="add" value="添加" class="button_style_2 f_l" onclick="common_add();  return false;"/>
	</div>
</div>
<div class="bk_8"></div>
<table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list mar_l_8">
	<thead><tr>
		<th width="30" class="t_l bdr_3"><input type="checkbox"/></th>
		<th width="50" class="ajaxer"><div name="sourceid">ID</div></th>
		<th class="ajaxer"><div name="name">来源名称</div></th>
		<th>链接地址</th>
		<th width="250" class="t_c">Logo</th>
		<th width="80" class="ajaxer"><div name="count">转载量</div></th>
		<th width="80">管理操作</th>
	</tr></thead>
	<tbody id="list_body"></tbody>
</table>
<div class="table_foot">
	<div id="pagination" class="pagination f_r"></div>
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
var row_template = 
'<tr id="row_{sourceid}">\
	<td>\
	   <input type="checkbox" class="checkbox_style" name="chk_row" value="{sourceid}" />\
	</td>\
	<td class="t_r">{sourceid}</td>\
	<td>{name}</td>\
	<td>{url}</td>\
	<td class="t_c"><a href="{url}" target="_blank">{logo}</a></td>\
	<td class="t_r">{count}</td>\
	<td class="t_c">\
	   <img class="manage common_edit"height="16" width="16" alt="编辑" src="images/edit.gif"/>\
       <img class="manage del_row" height="16" width="16"  alt="删除" src="images/delete.gif"/>\
	</td>\
</tr>';
</script>
<script type="text/javascript">
$.validate.setConfigs({
	xmlPath:'apps/system/validators/source/'
});
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
	var url = '?app=<?=$app?>&controller=<?=$controller?>&action=edit&sourceid='+id;
	tr.trigger('check');
	ct.form('编辑来源',url,400,200,function(json){
		if(json.state)
		{
			tableApp.updateRow(id, json.data);
			return true;
		}
		else
		{
			return false;
		}
	}).dialog('option','modal',true);
}
function common_add() {
	ct.form('添加来源','?app=<?=$app?>&controller=<?=$controller?>&action=add',400,200,function(json){
		if(json.state)
		{
			tableApp.load();
			return true;
		}
		else
		{
			return false;
		}
	});
}
function search_dialog() {
	ct.ajax('高级搜索', '?app=<?=$app?>&controller=<?=$controller?>&action=search', 400, 200, null, function(dialog){
		tableApp.load(dialog.find('form'));
		return true;
	});
}
</script>
<?php $this->display('footer', 'system');