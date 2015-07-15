<?php $this->display('header');?>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>
<script type="text/javascript" src="apps/magazine/js/page.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.blockUI.js"></script>

<link href="<?=IMG_URL?>js/lib/tree/style.css" rel="stylesheet" type="text/css" />
<script src="<?=IMG_URL?>js/lib/cmstop.tree.js" type="text/javascript"></script>


<div class="bk_8"></div>
<div class="table_head">
	<input type="button" id="add" value="新建" class="button_style_2 f_l"/>
	<!--<input type="button" id="edit" value="编辑模式" class="button_style_2 f_l"/>
	<input type="button" id="save" value="全部保存" class="button_style_2 f_l" style="display:none;"/>-->
	<input type="button" id="access" value="前台" class="button_style_2 f_l"/>
	<input type="button" id="publish" value="发布" class="button_style_2 f_l"/>
</div>
<div class="bk_8"></div>
<table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list" style="margin-left:6px;">
  <thead>
    <tr>
      <th width="30" class="t_l bdr_3"><input type="checkbox" id="check_all" /></th>
      <th width="80" class="ajaxer headerSortDown"><em name="pop_layer_id"></em><div name="p.pageno">栏目号</div></th>
      <th class="ajaxer"><em name="pop_layer_id"></em><div name="name">栏目名</div></th>
      <th width="100" class="ajaxer"><em name="pop_layer_id"></em><div name="count">文章数</div></th>
      <th width="100" class="ajaxer"><em name="pop_layer_id"></em><div name="editor">主编</div></th>
      <th width="100" class="ajaxer"><em name="pop_layer_id"></em><div name="arteditor">美编</div></th>
      <th width="120">管理</th>
    </tr>
  </thead>
  <tbody id="list_body">
  </tbody>
</table>
<div class="table_foot">
	<p class="f_l">
		<input type="button" onclick="page.del()" value="删 除" class="button_style_1"/>
	</p>
</div>
<!--右键菜单-->
<ul id="right_menu" class="contextMenu">
   <li class="edit"><a href="#page.edit">编辑</a></li>
   <li class="manage"><a href="#page.manage">管理</a></li>
   <li><a href="#page.relate">关联文章</a></li>
   <li class="del"><a href="#page.del">删除</a></li>
</ul>

<script type="text/javascript">
var app = '<?=$app?>';
var controller = '<?=$controller?>';
var eid = '<?=$_GET['id']?>';
var row_template = '\
<tr id="tr_{pid}">\
	<td><input type="checkbox" class="checkbox_style" name="chk_row" id="chk_row_{pid}" value="{pid}" /></td>\
    <td class="t_c"><a href="javascript:;"> {pageno}</a> </td>\
    <td name="name" class="t_c"><a href="javascript:;" class="manage"> {name}</a> </td>\
    <td class="t_c count"> <span>{count}<span> </td>\
	<td name="editor"  class="t_c" size="20">{editor}</td>\
    <td name="arteditor"  class="t_c" size="30">{arteditor}</td>\
	 <td class="t_c">\
    	<img src="images/edit.gif" title="编辑" class="hand edit"/>\
    	<img src="images/contribute.gif" title="栏目管理" class="hand manage"/>\
    	<img src="images/add.png" title="关联文章" class="hand relate"/>\
    	<img title="删除" src="images/delete.gif" alt="删除" class="hand delete" />\
    </td>\
</tr>';

var tableApp = new ct.table('#item_list', {
    rowIdPrefix : 'tr_',
    rightMenuId : 'right_menu',
    pageField : 'page',
    pageSize : 999,
    dblclickHandler : 'page.edit',
    rowCallback     : 'init_event',
    template : row_template,
    baseUrl  : '?app=<?=$app?>&controller=<?=$controller?>&action=page&eid='+eid
});

$(function (){
	tableApp.load();
	$('#add').click(page.add);
	$('#edit').click(page.editAll);
	$('#save').click(page.saveAll);
	$('#access').click(function (){
		var url = '<?=$SETTING['www_root']?>/<?=$m['alias']?>/<?=$e['year']?>/<?=$e['eid']?>/';
		window.open(url);
	});
	$('#publish').click(function (){
		page.publish(eid, true);
	});
	
	$('#edit').click(function (){
		$(this).hide();
		$('#save').show();
	});
	$('#save').click(function (){
		$(this).hide();
		$('#edit').show();
	});
	ct.nav([
		{text: '扩展'},
		{text: '杂志', url: '?app=magazine&controller=magazine&action=index'},
		{text: '<?=$m['name']?>', url: '?app=magazine&controller=edition&action=index&id=<?=$m['mid']?>'},
		{text: '第<?=$e['total_number']?>期'}
	]);
});
//init function
function init_event(id, tr)
{
	tr.find('img.edit').click(function (){
		page.edit(id, tr);
	});
	tr.find('a.manage, img.manage').click(function (){
		page.manage(id);
	});
	tr.find('img.relate').click(function (){
		page.relate(id, tr);
	});
	tr.find('img.delete').click(function (){
		page.del(id);
	});
	tr.find('img.publish').click(function (){
		page.publish(id, true);
	});
	tr.find('img.view').click(function (){
		page.access(id, tr);
	});
}
function json_loaded(json) {
	$('#pagetotal').html(json.total);
}
</script>

<?php $this->display('footer','system');?>