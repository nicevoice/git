<?php $this->display('header','system');?>

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

<script type="text/javascript" src="apps/system/js/workflow.js"></script>

<div class="bk_10"></div>
<div class="table_head"> 
  <input type="button" name="add" id="add" value="添加" class="button_style_2 f_l" onclick="workflow.add();  return false;"/>
</div>
<div class="bk_8"></div>
<table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list" style="margin-left:6px;">
  <thead>
    <tr>
      <th width="30" class="t_l bdr_3"><input type="checkbox" id="check_all" /></th>
      <th width="50" class="sorter"><div>ID</div></th>
      <th width="30%" class="sorter"><div>工作流名称</div></th>
      <th>描述</th>
      <th width="50">步骤</th>
      <th width="10%">管理操作</th>
    </tr>
  </thead>
  <tbody>
  </tbody>
</table>
<div class="table_foot">
  <div id="pagination" class="pagination f_r"></div>
  <p class="f_l">
    <input type="button" name="button" id="button" onclick="workflow.del(); return false;" value="删 除" class="button_style_1"/>
  </p>
</div>
<!--右键菜单-->
<ul id="right_menu" class="contextMenu">
   <li class="new"><a href="#workflow.add">添加</a></li>
   <li class="edit"><a href="#workflow.edit">编辑</a></li>
   <li class="delete"><a href="#workflow.del">删除</a></li>
</ul>

<script type="text/javascript">
var row_template = '<tr id="row_{workflowid}">';
row_template +='	<td><input type="checkbox" class="checkbox_style" name="chk_row" id="chk_row_{workflowid}" value="{workflowid}" /></td>';
row_template +='	<td>{workflowid}</td>';
row_template +='	<td name="name" value="{name}" size="20">{name}</td>';
row_template +='	<td name="description" value="{description}" size="50">{description}</td>';
row_template +='	<td class="t_r">{steps}</td>';
row_template +='	<td class="t_c"><img src="images/edit.gif" alt="编辑" width="16" height="16" class="edit hand" /> &nbsp;<img src="images/delete.gif" alt="删除" width="16" height="16" class="delete hand"/></td>';
row_template +='</tr>';

var tableApp = new ct.table('#item_list', {
	rowIdPrefix : 'row_',
	rightMenuId : 'right_menu',
	pageField : 'page',
	pageSize : 15,
	dblclickHandler : 'workflow.edit',
	rowCallback     : 'init_row_event',
	template : row_template,
	baseUrl  : '?app=<?=$app?>&controller=<?=$controller?>&action=page'
});

function init_row_event(id, tr)
{
	tr.find('img.delete').click(function(){
		workflow.del(id);
	});
	tr.find('img.edit').click(function(){
		workflow.edit(id);
	});
}
tableApp.load();
</script>
<?php $this->display('footer', 'system');