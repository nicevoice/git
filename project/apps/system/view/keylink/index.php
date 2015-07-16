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
		<a href="javascript:;" onfocus="this.blur()" onclick="tableApp.load($('#search_f'));parentid = '';return false;" title="搜索">搜索</a>
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
		<th class="sorter"><div>关键词</div></th>
		<th width="50%">链接</th>
		<th width="120" class="ajaxer"><div name="created">创建时间</div></th>
		<th width="10%">创建人</th>
		<th width="10%">管理操作</th>
	</tr></thead>
	<tbody></tbody>
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
	xmlPath:'apps/system/validators/keylink/'
});
var row_template = '<tr id="row_{id}">';
row_template +='	<td><input type="checkbox" class="checkbox_style" name="chk_row" id="chk_row_{id}" value="{id}" /></td>';
row_template +='	<td id="name"><a href="{url}" target="_blank">{name}</a></td>';
row_template +='	<td>{url}</td>';
row_template +='    <td id="created" class="t_c" >{created}</td>';
row_template +='    <td id="createdby" class="t_c"><a href="javascript: url.member({createdby});">{createdbyname}</a></td>';
row_template +='	<td class="t_c"><img src="images/edit.gif" alt="编辑" width="16" height="16" class="manage common_edit"/> &nbsp;<img src="images/delete.gif" alt="删除" width="16" height="16" class="manage del_row");"/></td>';
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
			? (ct.ok('删除完毕'), tableApp.deleteRow(id))
			: ct.error(json.error);
		});
	});
}

function common_edit(id,tr) {
	var url = '?app=<?=$app?>&controller=<?=$controller?>&action=edit&id='+id;
	tr.trigger('check');
	ct.form('编辑菜单',url,400,180,function(json){
		if(json.state){
			tableApp.updateRow(id, json.data);
			return true;
		}
		else{
			return false;
		}
	}).dialog('option','modal',true);
}

function add_dialog() {
	ct.form('添加','?app=<?=$app?>&controller=<?=$controller?>&action=add',400,180,function(json){
		if(json.state){
			tableApp.load();
			ct.tips('关键词添加成功！', 'ok');
			return true;
		}
		else{
			return false;
		}
	});
}
</script>
<?php $this->display('footer', 'system');