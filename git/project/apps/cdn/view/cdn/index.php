<?php $this->display('header');?>
<!--tablesorter-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/tablesorter/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.tablesorter.js"></script>
<!--tabnav-->
<script type="text/javascript" src="/js/cmstop.tabnav.js"></script>

<!--contextmenu-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/contextMenu/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.contextMenu.js"></script>

<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>
<script type="text/javascript" src="apps/cdn/js/cdn.js"></script>
<div class="bk_8"></div>
<div class="table_head">
  <input type="button" name="add" id="add" value="添加" class="button_style_2 f_l" onclick="cdn.add();"/>
</div>
<!--右键菜单-->
<ul id="right_menu" class="contextMenu">
   <li class="edit"><a href="#cdn.edit">编辑</a></li>
   <li class="delete"><a href="#cdn.del">删除</a></li>
</ul>
<div class="bk_8"></div>
<table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list" style="margin-left:6px;">
  <thead>
    <tr>
      <th width="80" class="bdr_3">ID</th>
      <th width="500">名称</th>
      <th width="150">管理操作</th>
    </tr>
  </thead>
  <tbody id="list_body">
  </tbody>
</table>

<script type="text/javascript">
var row_template = '<tr id="row_{cdnid}">\
	                 	<td class="t_c">{cdnid}</td>\
	                	<td class="t_l">{name}</td>\
	                	<td class="t_c"><img src="images/edit.gif" alt="编辑" width="16" height="16" class="hand edit"/> &nbsp;<img src="images/delete.gif" alt="删除" width="16" height="16" class="hand delete" /></td>\
	                </tr>';

var tableApp = new ct.table('#item_list', {
    rowIdPrefix : 'row_',
    pageField : 'page',
    pageSize : 15,
    template : row_template,
	dblclickHandler : cdn.edit,
    baseUrl  : '?app=<?=$app?>&controller=<?=$controller?>&action=page'
});

function init_row_event(id, tr)
{
	tr.find('img.edit').click(function(){
		cdn.edit(id);
	});
	tr.find('img.delete').click(function(){
		cdn.del(id);
	});    
}

$(document).ready(function() {
	tableApp.load();
});
</script>
<?php $this->display('footer');