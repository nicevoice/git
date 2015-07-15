<?php $this->display('header', 'system');?>
<!--tablesorter-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/tablesorter/style.css" />
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>
<!--contextmenu-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/contextMenu/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.contextMenu.js"></script>

<script type="text/javascript" src="apps/system/js/model.js"></script>

<div class="bk_8"></div>
<table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list" style="margin-left:6px;">
  <thead>
    <tr>
      <th width="60" class="bdr_3">ID</th>
      <th width="10%">模型名称</th>
      <th width="10%">英文名</th>
      <th>描述</th>
      <th width="10%">内容数</th>
      <th width="10%">状态</th>
      <th width="10%">管理操作</th>
    </tr>
  </thead>
  <tbody id="list_body">
  </tbody>
</table>
<!--右键菜单-->
<ul id="right_menu" class="contextMenu">
   <li class="new"><a href="#model.add">添加</a></li>
   <li class="edit"><a href="#model.edit">编辑</a></li>
   <li class="delete"><a href="#model.del">删除</a></li>
</ul>
<script type="text/javascript">
$.validate.setConfigs({
    xmlPath:'<?=ADMIN_URL?>apps/<?=$app?>/validators/model/'
});
var row_template = '<tr id="row_{modelid}">\
	                 	<td class="t_c">{modelid}</td>\
	                	<td class="t_l">{name}</td>\
	                	<td class="t_l">{alias}</td>\
	                	<td class="t_l">{description}</td>\
	                	<td class="t_l">{posts}</td>\
	                	<td class="t_l">{state}</td>\
	                	<td class="t_c"><img src="images/edit.gif" alt="编辑" width="16" height="16" class="hand edit"/></td>\
	                </tr>';

var tableApp = new ct.table('#item_list', {
    rowIdPrefix : 'row_',
    rightMenuId : 'right_menu',
    pageField : 'page',
    pageSize : 100,
    dblclickHandler : 'model.edit',
    rowCallback     : 'init_row_event',
    template : row_template,
    baseUrl  : '?app=<?=$app?>&controller=<?=$controller?>&action=page'
});

function init_row_event(id, tr)
{
	tr.find('img.edit').click(function(){
		model.edit(id);
	});
	tr.find('img.delete').click(function(){
		model.del(id);
	});    
}

tableApp.load();
</script>
<?php $this->display('footer', 'system');