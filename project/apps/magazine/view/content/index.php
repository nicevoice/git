<?php $this->display('header');?>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>
<script type="text/javascript" src="apps/magazine/js/content.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.blockUI.js"></script>

<link href="<?=IMG_URL?>js/lib/tree/style.css" rel="stylesheet" type="text/css" />
<script src="<?=IMG_URL?>js/lib/cmstop.tree.js" type="text/javascript"></script>

<div class="bk_8"></div>
<div class="table_head">
	<input type="button" id="add" value="关联文章" class="button_style_4 f_l"/>
	<input type="button" id="publish" value="发 布" class="button_style_2 f_l"/>
	<input type="button" id="access" value="前 台" class="button_style_2 f_l"/>
</div>
<div class="bk_8"></div>
<table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list" style="margin-left:6px;">
  <thead>
    <tr>
      <th width="30" class="t_l bdr_3"><input type="checkbox" id="check_all" /></th>
      <th width="80" class="ajaxer headerSortDown"><em name="pop_layer_id"></em><div name="sort">顺序</div></th>
      <th width="100" class="ajaxer"><em name="pop_layer_id"></em><div name="name">所属频道</div></th>
      <th>标题</th>
      <th width="80" class="ajaxer"><em name="pop_layer_id"></em><div name="pv">浏览量</div></th>
      <th width="80">管理</th>
    </tr>
  </thead>
  <tbody id="list_body">
  </tbody>
</table>
<div class="table_foot">
	<p class="f_l">
		<input type="button" onclick="content.del()" value="删除关联" class="button_style_1"/>
	</p>
</div>
<!--右键菜单-->
<ul id="right_menu" class="contextMenu">
   <li class="del"><a href="#content.del">删除关联</a></li>
</ul>

<script type="text/javascript">
var app = '<?=$app?>';
var controller = '<?=$controller?>';
var pid = '<?=$_GET['pid']?>';
var row_template = '\
<tr id="tr_{mapid}">\
	<td><input type="checkbox" class="checkbox_style" name="chk_row" id="chk_row_{mapid}" value="{mapid}" /></td>\
    <td class="t_c">{sort} </td>\
    <td name="name" class="t_c"> <a target="_BLANK" href="{caturl}">{name}</a> </td>\
    <td name="name" class="t_l"> <a target="_BLANK" href="{url}">{title}</a> </td>\
    <td class="t_c">{pv} </td>\
	 <td class="t_c">\
    	<img title="删除关联" src="images/delete.gif" alt="删除关联" class="hand delete" />\
    </td>\
</tr>';

var tableApp = new ct.table('#item_list', {
    rowIdPrefix : 'tr_',
    rightMenuId : 'right_menu',
    pageField : 'page',
    pageSize : 999,
    dblclickHandler : 'content.edit',
    rowCallback     : 'init_event',
    template : row_template,
    baseUrl  : '?app=<?=$app?>&controller=<?=$controller?>&action=page&pid='+pid
});

$(function (){
	tableApp.load();
	$('#add').click(content.relate);
	$('#access').click(function (){
		var url = '<?=$SETTING['www_url']?>/<?=$m['alias']?>/<?=$e['year']?>/<?=$e['eid']?>/';
		window.open(url);
	});
	$('#publish').click(function (){
		content.publish(<?=$e['eid']?>, true);
	});
	ct.nav([
		{text: '扩展'},
		{text: '杂志', url: '?app=magazine&controller=magazine&action=index'},
		{text: '<?=$m['name']?>', url: '?app=magazine&controller=edition&action=index&id=<?=$m['mid']?>'},
		{text: '第<?=$e['total_number']?>期', url: '?app=magazine&controller=page&action=index&id=<?=$e['eid']?>'},
		{text: '<?=$p['name']?>版'}
	]);
});
//init function
function init_event(id, tr)
{
	tr.find('img.edit').click(function (){
		content.edit(id, tr);
	});
	tr.find('a.manage, img.manage').click(function (){
		content.manage(id);
	});
	tr.find('img.delete').click(function (){
		content.del(id);
	});
	tr.find('img.publish').click(function (){
		content.publish(id, true);
	});
	tr.find('img.view').click(function (){
		content.access(id, tr);
	});
}
function json_loaded(json) {
	$('#pagetotal').html(json.total);
}
</script>

<?php $this->display('footer','system');?>