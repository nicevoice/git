<?php $this->display('header', 'system');?>
<!--tablesorter-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/tablesorter/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.tablesorter.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>
<!--contextmenu-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/contextMenu/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.contextMenu.js"></script>

<script type="text/javascript" src="apps/system/js/psn.js"></script>
<div class="bk_8"></div>
<div class="table_head">
  <input type="button" name="add" id="add" value="添加" class="button_style_2 f_l" onclick="psn.add();"/>
</div>
<div class="bk_8"></div>
<table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list" style="margin-left:6px;">
  <thead>
    <tr>
      <th width="60" class="bdr_3">ID</th>
      <th>名称</th>
      <th width="25%">发布路径</th>
      <th width="25%">发布网址</th>
      <th width="10%">管理操作</th>
    </tr>
  </thead>
  <tbody id="list_body">
  </tbody>
</table>
<!--右键菜单-->
<ul id="right_menu" class="contextMenu">
   <li class="new"><a href="#psn.add">添加</a></li>
   <li class="edit"><a href="#psn.edit">编辑</a></li>
   <li class="delete"><a href="#psn.del">删除</a></li>
</ul>
<script type="text/javascript">
$.validate.setConfigs({
	xmlPath:'apps/system/validators/psn/'
});

var row_template = '<tr id="row_{psnid}">\
	                 	<td class="t_c">{psnid}</td>\
	                	<td class="t_l">{name}</td>\
	                	<td class="t_l">{path}</td>\
	                	<td class="t_l">{url}</td>\
	                	<td class="t_c"><img src="images/edit.gif" alt="编辑" width="16" height="16" class="hand edit"/> &nbsp;<img src="images/delete.gif" alt="删除" width="16" height="16" class="hand delete" /></td>\
	                </tr>';

var tableApp = new ct.table('#item_list', {
    rowIdPrefix : 'row_',
    rightMenuId : 'right_menu',
    pageField : 'page',
    pageSize : 15,
    dblclickHandler : 'psn.edit',
    rowCallback     : 'init_row_event',
    template : row_template,
    baseUrl  : '?app=<?=$app?>&controller=<?=$controller?>&action=page'
});

function init_row_event(id, tr)
{
	tr.find('img.edit').click(function(){
		psn.edit(id);
	});
	tr.find('img.delete').click(function(){
		psn.del(id);
	});    
}

tableApp.load();
</script>
<?php $this->display('footer', 'system');