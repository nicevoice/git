<?php $this->display('header');?>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>
<script type="text/javascript" src="apps/magazine/js/edition.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.blockUI.js"></script>
<script type="text/javascript" src="uploader/cmstop.uploader.js"></script>
<script type="text/javascript" src="imageEditor/cmstop.imageEditor.js"></script>
<script type="text/javascript" src="js/cmstop.filemanager.js"></script>
<div class="bk_8"></div>
<div class="table_head">
	<div id="disabled_x" class="th_pop" style="display:none;">
		<a rel="-1" href="javascript:;">全部</a><br/>
		<?foreach ($disabledMap AS $k => $d): ?>
		<a rel="<?=$k?>" href="javascript:;"><?=$d?></a><br/>
		<?endforeach; ?>
	</div>
	<?
	$array = array('publish', 'created', 'year');
	foreach ($array as $item):
	?>
	<div id="<?=$item?>" class="th_pop" style="left: 0; display:none;">
		<div>
			<? if($item != 'year'): ?>
			<a min="<?=date('Y-m-01', strtotime('this month'))?>" href="javascript:;">本月</a> |
			<? endif;?>
			<a min="<?=date('Y-01-01', strtotime('this year'))?>" href="javascript:;">本年</a>
		</div>
		<ul>
			<?php 
			for ($i=1; $i<=7; $i++) { 
			$y = date('Y', strtotime("-$i year"));
			?>
			<li><a min="<?=$y?>-01-01" max="<?=$y?>-12-31" href="javascript:;"><?=$y?>年</a></li>
			<?php } ?>
		</ul>
	</div>
	<? endforeach; ?>
	<input type="button" id="add" value="新建期号" class="button_style_4 f_l"/>
</div>
<div class="bk_8"></div>
<table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list" style="margin-left:6px;">
  <thead>
    <tr>
      <th width="25" class="t_l bdr_3"><input type="checkbox" id="check_all" /></th>
      <th class="ajaxer headerSortUp"><em name="pop_layer_id"></em><div name="total_number">总期号</div></th>
      <th class="ajaxer">
	      <em class="more_pop" name="year"></em><div name="year">年度期号</div>
      </th>
      <th width="15%" class="ajaxer">
		<em class="more_pop" name="publish"></em><div name="publish">出版日期</div>
      </th>
      <th width="10%" class="ajaxer">
      	<em class="more_pop" name="disabled_x"></em><div name="e.disabled">状态</div>
      </th>
      <th width="15%" class="ajaxer">
      	<em class="more_pop" name="created"></em><div name="created">创建日期</div>
      </th>
      <th width="7%" class="ajaxer">
      	<em name="pop_layer_id"></em><div name="count">文章数</div>
      </th>
      <th width="100" class="ajaxer">
      	<em name="pop_layer_id"></em><div name="createdby">最后编辑</div>
      </th>
      <th width="120">管理操作</th>
    </tr>
  </thead>
  <tbody id="list_body">
  </tbody>
</table>
<div class="table_foot">
	<div id="pagination" class="pagination f_r"></div>
	<div class="f_r"> 共<span id="pagetotal">{total}</span>期&nbsp;每页
	<input type="text" name="pagesize" value="<?=$size?>" size="2" id="pagesize"/> 期&nbsp;&nbsp;</div>
	<p class="f_l">
		<input type="button" onclick="edition.del()" value="删 除" class="button_style_1"/>
		<input type="button" onclick="edition.disabled(2)" value="休 眠" class="button_style_1"/>
		<input type="button" onclick="edition.publish()" value="发布" class="button_style_1"/>
	</p>
</div>
<!--右键菜单-->
<ul id="right_menu" class="contextMenu">
   <li class="edit"><a href="#edition.save">编辑</a></li>
   <li class="view"><a href="#edition.access">访问</a></li>
   <li class="publish"><a href="#edition.publish">发布</a></li>
   <li class="manage"><a href="#edition.manage">管理</a></li>
   <li class="del"><a href="#edition.del">删除</a></li>
</ul>

<script type="text/javascript">
var app = '<?=$app?>';
var controller = '<?=$controller?>';
var mid = '<?=$_GET['id']?>';
var row_template = '\
<tr id="tr_{eid}">\
	<td><input type="checkbox" class="checkbox_style" name="chk_row" id="chk_row_{eid}" value="{eid}" /></td>\
	<td class="t_c"><a href="javascript:;" class="manage"> 第{total_number}期</a> </td>\
    <td class="t_c"><a href="javascript:;" class="manage"> {year}年-{number}期</a> </td>\
    <td name="publish"  class="t_c" size="20">{publish}</td>\
    <td class="t_c">{disabled_words}</td>\
    <td name="created"  class="t_c" size="30">{created}</td>\
    <td class="t_c count"> <span>{count}</span> </td>\
    <td class="t_c"> <a href="javascript: url.member({createdby});">{username}</a> </td>\
    <td class="t_c">\
    	<img src="images/edit.gif" title="编辑" class="hand edit"/>\
    	<img src="images/view.gif" href="{url}" title="访问前台"/ class="hand access">\
    	<img src="images/refresh.gif" title="发布本期" class="hand publish"/>\
    	<img src="images/contribute.gif" title="栏目管理" class="hand manage"/>\
    	<img title="删除" src="images/delete.gif" alt="删除" class="hand delete" />\
    </td>\
</tr>';

var tableApp = new ct.table('#item_list', {
    rowIdPrefix : 'tr_',
    rightMenuId : 'right_menu',
    pageField : 'page',
    pageSize : <?=$size?>,
    dblclickHandler : 'edition.save',
    rowCallback     : 'init_event',
    template : row_template,
    baseUrl  : '?app=<?=$app?>&controller=<?=$controller?>&action=page&mid='+mid
});

$(function (){
	tableApp.load();
	edition.pagesize();
	$('#add').click(edition.save);
	edition.search();
	ct.nav([
		{text: '扩展'},
		{text: '杂志', url: '?app=<?=$app?>&controller=<?=$app?>&action=index'},
		{text: '<?=$head['title']?>'}
	]);
});
//init function
function init_event(id, tr)
{
	tr.find('img.edit').click(function (){
		edition.save(id);
	});
	tr.find('a.manage, img.manage').click(function (){
		edition.manage(id);
	});
	tr.find('img.delete').click(function (){
		edition.del(id);
	});
	tr.find('img.publish').click(function (){
		edition.publish(id, true);
	});
	tr.find('img.access').click(edition.access);
}
function json_loaded(json) {
	$('#pagetotal').html(json.total);
}
</script>
<?php $this->display('footer','system');?>