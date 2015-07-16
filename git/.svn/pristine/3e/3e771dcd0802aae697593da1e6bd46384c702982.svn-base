<?php $this->display('header');?>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>
<script type="text/javascript" src="apps/field/js/field.js"></script>
<div class="bk_8"></div>
<div class="table_head">
	<input type="button" id="add" value="新增方案" class="button_style_4 f_l"/>
</div>
<div class="bk_8"></div>
<table width="99%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list" style="margin-left:6px;">
  <thead>
    <tr>
      <th width="25" class="t_l bdr_3"><input type="checkbox" id="check_all" /></th>
      <th width="3%">ID</th>
      <th width="20%">方案名称</th>
      <th>描述</th>
      <th width="20%">管理操作</th>
    </tr>
  </thead>
  <tbody id="list_body">
  </tbody>
</table>
<div class="clear"></div>
<div class="table_foot" style="width:98%">
<div id="pagination" class="pagination f_r"></div>
<div class="f_r">
	 共有<span id="pagetotal">0</span>条记录&nbsp;&nbsp;&nbsp;
</div>
<div class="f_l">
	<p class="f_l">
		<input type="button" onclick="field.del()" value="删 除" class="button_style_1"/>
	</p>
</div>
</div>
<!--右键菜单-->
<ul id="right_menu" class="contextMenu">
   <li class="edit"><a href="#field.add">编辑</a></li>
   <li class="del"><a href="#field.del">删除</a></li>
</ul>

<script type="text/javascript">
var app = '<?=$app?>';
var controller = '<?=$controller?>';
var row_template = '\
<tr id="tr_{pid}">\
	<td><input type="checkbox" class="checkbox_style" name="chk_row" id="chk_row_{pid}" value="{pid}" /></td>\
	<td class="t_l"><a href="javascript:;" class="edit"> {pid}</a> </td>\
	<td class="t_l"><a href="javascript:;" class="edit"> {name}</a> </td>\
	<td class="t_l"><a href="javascript:;" class="edit"> {description}</a> </td>\
    <td class="t_c">\
    	<img title="设计字段" src="images/setting.gif" alt="设计字段" class="hand set" />\
    	<img title="编辑字段" src="images/edit.gif" alt="编辑字段" class="hand edit" />\
    	<img title="删除" src="images/delete.gif" alt="删除" class="hand delete" />\
    </td>\
</tr>';

var tableApp = new ct.table('#item_list', {
    rowIdPrefix : 'tr_',
    rightMenuId : 'right_menu',
    pageVar : 'page',
	pageSize : 15,
    dblclickHandler : 'field.add',
    rowCallback     : 'init_event',
    template : row_template,
	jsonLoaded : 'json_loaded',
    baseUrl  : '?app=<?=$app?>&controller=<?=$controller?>&action=page'
});

$(function (){
	tableApp.load();
	$('#add').click(field.add);
});

// 显示总条数
function json_loaded(json) {
	$('#pagetotal').html(json.total);
}

//init function
function init_event(id, tr) {
	tr.find('a.edit, img.edit').click(function (){field.add(id);});
	tr.find('img.delete').click(function (){field.del(id);});
	tr.find('img.set').click(function (){
			ct.assoc.open('?app=field&amp;controller=project&amp;action=design&amp;pid='+id,'newtab');
		}
	);
}

</script>
<?php $this->display('footer','system');?>