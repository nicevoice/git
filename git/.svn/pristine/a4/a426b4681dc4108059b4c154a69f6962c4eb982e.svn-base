<?php $this->display('header', 'system');?>

<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/tablesorter/style.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/pagination/style.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/contextMenu/style.css"/>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.tablesorter.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.contextMenu.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.pagination.js"></script>
<div class="bk_10"></div>
<div class="table_head">
	<div class="f_l">
	<input type="button" name="add" id="add" value="添加组" class="button_style_4 f_l" onclick="group.add();return false;"/>
	</div>
</div>
<div class="bk_8"></div>
<table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list mar_l_8">
	<thead>
		<tr>
			<th width="80" class="bdr_3"><div>用户组</div></th>
			<th width="82">用户数</th>
			<th>备注</th>
			<th width="82">自定义</th>
			<th width="80">管理操作</th>
		</tr>
	</thead>
	<tbody id="list_body"></tbody>
</table>
<div class="table_foot"></div>
<!--右键菜单-->
<ul id="rightMenu_0" class="contextMenu">
	<li class="edit"><a href="#group.edit">编辑</a></li>
	<li class="delete"><a href="#group.del">删除</a></li>
</ul>
<ul id="rightMenu_1" class="contextMenu">
	<li class="edit"><a href="#group.edit">编辑</a></li>
</ul>
<script type="text/javascript">
var manage_operation = '<img src="images/edit.gif" alt="编辑" width="16" height="16" class="manage" onclick="group.edit({groupid})"/>&nbsp;<img src="images/delete.gif" alt="删除" width="16" height="16" class="manage del" onclick="group.del(\'{groupid}\');"/>';
var row_template  ='<tr id="row_{groupid}" class="is_{issystem}" right_menu_id="rightMenu_{issystem}">';
	row_template +='	<td class="t_c"><a href="javascript:group.open({groupid});">{name}</a></td>';
	row_template +='	<td class="t_r">{persons}</td>';
	row_template +='	<td >{remarks}</td>';
	row_template +='	<td class="t_c">{system}</td>';
	row_template +='	<td class="t_c" id="manage_{groupid}" name="manage" value="manage">'+manage_operation+'</td>';
	row_template +='</tr>';
var tableApp= new ct.table('#item_list', {
	rowIdPrefix : 'row_',
	rowCallback: 'init_row_event',
	jsonLoaded : 'json_loaded',
	dblclickHandler : 'group.edit',
	template : row_template,
	baseUrl  : '?app=<?=$app?>&controller=<?=$controller?>&action=page'
});

function init_row_event(id, tr) {
	$("tr.is_1").find('.del').remove();
}

$(function(){
	$.validate.setConfigs({
		xmlPath:'/apps/member/validators/'
	});
	tableApp.load();
});
var baseUrl = '?app=<?=$app?>&controller=<?=$controller?>';
var id2del;
var group = {
	add : function() {
		ct.form('添加用户组',baseUrl+'&action=add', 360, 200, group.addSubmit, function(){return true});
	},
	addSubmit :function(json) {
		if(json.state) {
			tableApp.addRow(json.data,true);
		}
		return true;
	},
	edit : function(groupid) {
		ct.form('修改用户组',baseUrl+'&action=edit&groupid='+groupid, 360, 200,
			function(json) {
				tableApp.updateRow(groupid,json.data);
				ct.ok('修改成功');
				return true;
			},function(){return true});
	},
	del : function(groupid) {
		id2del = groupid;
		var persons = $("#row_"+groupid+" > td:eq(1)").html();
		var msg = ''
		if(persons>0) {
			msg = '该用户组下有用户！删除需要先将用户转移到其它用户组！是否将用户转至其他用户组？';
			ct.confirm(msg,'group_change');
		} else {
			group_del(groupid);
		}
	},
	open: function (groupid)
	{
		ct.assoc.open('?app=member&controller=index&action=index&groupid='+groupid, 'newtab');
	}
}
function group_del(groupid) {
	var msg = '确定删除该用户组吗？';
	ct.confirm(msg,function(){
		$.getJSON(baseUrl+'&action=delete',{id:groupid},function(json){
			json.state
			 ? (ct.warn('删除完毕'), tableApp.deleteRow(groupid))
			 : ct.warn(json.error);
		});
	}).dialog('option','width',360);
}
function group_change() {
	ct.form('修改用户组',baseUrl+'&action=changegroup&groupid='+id2del, 360, 160,
			function(json) {
				if(json.state) {
					$("#row_"+id2del+" > td:eq(1)").html('0');
					group_del(id2del);
					return true;
				}
			},function(){return true});
}
</script>
<?php $this->display('footer', 'system');?>