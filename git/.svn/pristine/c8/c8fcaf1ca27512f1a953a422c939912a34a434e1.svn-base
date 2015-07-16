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
<div class="bk_8"></div>
<table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list mar_l_8">
	<thead><tr>
		<th width="30" class="t_l bdr_3">&nbsp;</th>
		<th class="sorter">模板</th>
		<th width="120">创建时间</th>
		<th width="10%">管理操作</th>
	</tr></thead>
	<tbody></tbody>
</table>
<div class="table_foot">
	<p class="f_l">
		<input type="button" name="button" id="button" onclick="set_tpl(); return false;" value="设为默认模板" class="button_style_1"/>
	</p>
</div>

<!--右键菜单-->


<script type="text/javascript">
$.validate.setConfigs({
	xmlPath:'apps/system/validators/keylink/'
});
var row_template = '<tr id="row_{dir}">';
row_template +='	<td><input type="radio" class="checkbox_style" name="chk_row" id="chk_row_{dir}" value="{dir}"{checked}/></td>';
row_template +='	<td id="name"><a href="javascript:;">{title}</a>&nbsp;{default}</td>';
row_template +='    <td id="created" class="t_c" >{created}</td>';
row_template +='	<td class="t_c" ><img src="images/edit.gif" alt="编辑" width="16" height="16" class="manage common_edit"/></td>';
row_template +='</tr>';

var tableApp = new ct.table('#item_list', {
	rowIdPrefix : 'row_',
	rightMenuId : 'right_menu',
	pageField : 'page',
	pageSize : 15,
	dblclickHandler : 'common_edit',
	rowCallback     : 'init_row_event',
	template : row_template,
	baseUrl  : '?app=<?=$app?>&controller=<?=$controller?>&action=loadTpl'
});

function init_row_event(id, tr) {
	tr.find('img.common_edit').click(function(){
		common_edit(id,tr);
	});
	tr.click(function(){
		$(this).find('input:radio').attr('checked','checked')
	});
}
tableApp.load();


function common_edit(id,tr) {
	var url = '?app=<?=$app?>&controller=<?=$controller?>&action=editTpl&tpl='+id;
	ct.form('编辑模板',url,300,50,function(json){
		if(json.state)
		{
			ct.ok('修改成功！');
			tableApp.load();
		}
		return true;
	});
}

function set_tpl()
{
	$.post('?app=<?=$app?>&controller=<?=$controller?>&action=showTpl', {'tpl' :$('input:radio:checked').val()}, function(json){
		if(json.state){
			ct.ok('设置成功！');
			tableApp.load();
		}	
	}, 'json');
}
</script>
<?php $this->display('footer', 'system');