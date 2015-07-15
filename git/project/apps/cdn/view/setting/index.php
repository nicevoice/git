<?php $this->display('header');?>
<!--tablesorter-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/tablesorter/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.tablesorter.js"></script>

<!--contextmenu-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/contextMenu/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.contextMenu.js"></script>

<!--tabnav-->
<script type="text/javascript" src="/js/cmstop.tabnav.js"></script>

<!--tab-->
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>
<script type="text/javascript" src="apps/cdn/js/cdn.js"></script>
<div class="bk_8"></div>
<div class="table_head">
  <input type="button" name="add" id="add" value="添加" class="button_style_2 f_l" onclick="cdn.add_type();"/>
</div>
<div class="bk_8"></div>
<table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list" style="margin-left:6px;">
  <thead>
    <tr>
      <th width="60" class="bdr_3">ID</th>
      <th width="30%">名称</th>
      <th width="40%">参数</th>
	  <th width="20%">文件</th>
	  <th width="180">管理</th>
    </tr>
  </thead>
  <tbody id="list_body">
  </tbody>
</table>
<script type="text/javascript">
function init_row_event(id, tr)
{
	tr.find('img.edit').click(function(){
		cdn.edit_type(id);
	});
	tr.find('img.delete').click(function(){
		cdn.delete_type(id);
	});    
}

var row_template = '<tr id="row_{tid}">\
	                 	<td class="t_c">{tid}</td>\
	                	<td class="t_l">{name}</td>\
						<td class="t_l parameter">{parameter}</td>\
						<td class="t_l">{type}</td>\
	                	<td class="t_c"><img src="images/edit.gif" alt="编辑" width="16" height="16" class="hand edit"/> &nbsp;<img src="images/delete.gif" alt="删除" width="16" height="16" class="hand delete" /></td>\
	                </tr>';

var tableApp = new ct.table('#item_list', {
    rowIdPrefix : 'row_',
    pageField : 'page',
    pageSize : 15,
    template : row_template,
	dblclickHandler : cdn.edit_type,
    baseUrl  : '?app=<?=$app?>&controller=<?=$controller?>&action=page'
});

$(document).ready(function() {
	tableApp.load();
});
</script>
<?php $this->display('footer');