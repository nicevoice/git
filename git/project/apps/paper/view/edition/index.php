<?php $this->display('header');?>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>
<script type="text/javascript" src="apps/paper/js/edition.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.blockUI.js"></script>
<div class="bk_8"></div>
<div class="table_head">
	<div id="disabled_x" class="th_pop" style="display:none;">
		<a rel="-1" href="javascript:;">全部</a><br/>
		<?foreach ($disabledMap AS $k => $d): ?>
		<a rel="<?=$k?>" href="javascript:;"><?=$d?></a><br/>
		<?endforeach; ?>
	</div>
	<?
	$array = array('date', 'created');
	foreach ($array as $item):
	?>
	<div id="<?=$item?>" class="th_pop" style="display:none;">
		<div>
			<a min="<?=date('Y-m-d', TIME)?>" href="javascript:;">今日</a> |
			<a min="<?=date('Y-m-d', strtotime('yesterday'))?>" max="<?=date('Y-m-d', strtotime('yesterday'))?>" href="javascript:;">昨日</a> | 
			<a min="<?=date('Y-m-d', strtotime('last monday'))?>" href="javascript:;">本周</a> | 
			<a min="<?=date('Y-m-01', strtotime('this month'))?>" href="javascript:;">本月</a><br/>
			<a min="all" href="javascript:;">全部</a>
		</div>
		<ul>
			<?php 
			for ($i=2; $i<=7; $i++) { 
			$createdate = date('Y-m-d', strtotime("-$i day"));
			?>
			<li><a min="<?=$createdate?>" max="<?=$createdate?>" href="javascript:;"><?=$createdate?></a></li>
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
      <th width="2%" class="t_l bdr_3"><input type="checkbox" id="check_all" /></th>
      <th class="ajaxer"><em name="pop_layer_id"></em><div name="total_number">总期号</div></th>
      <th class="ajaxer"><em name="pop_layer_id"></em><div name="number">期号</div></th>
       <th width="12%" class="ajaxer">
      	<em class="more_pop" name="created"></em><div name="created">创建日期</div>
      </th>
      <th width="100" class="ajaxer">
      	<em class="more_pop" name="disabled_x"></em><div name="e.disabled">状态</div>
      </th>
      <th width="15%" class="ajaxer">
		<em class="more_pop" name="date"></em><div name="date">出版日期</div>
      </th>
      <th width="80" class="ajaxer"><em name="pop_layer_id"></em><div name="count">文章数</div></th>
      <th width="100" class="ajaxer"><em name="pop_layer_id"></em><div name="createdby">最后编辑</div></th>
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
		<input type="button" onclick="edition.publish()" value="发 布" class="button_style_1"/>
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
var paperid = '<?=$_GET['id']?>';
var row_template = '\
<tr id="tr_{editionid}">\
	<td><input type="checkbox" class="checkbox_style" name="chk_row" id="chk_row_{editionid}" value="{editionid}" /></td>\
    <td class="t_c"><a href="javascript:;" class="manage"> 第{total_number}期</a> </td>\
    <td class="t_c"><a href="javascript:;" class="manage"> 第{number}期</a> </td>\
    <td name="date"  class="t_c" size="30">{created}</td>\
    <td class="t_c">{disabled_words}</td>\
    <td name="date"  class="t_c" size="30">{date}</td>\
	<td class="t_c count" size="20"> <span>{count}</span></td>\
	<td name="createdby"  class="t_c"> <a href="javascript: url.member({createdby});">{username}</a> </td>\
	<td class="t_c">\
    	<img src="images/edit.gif" title="编辑" class="hand edit"/>\
    	<img src="images/view.gif" href="{url}" title="访问前台"/ class="hand view">\
    	<img src="images/refresh.gif" title="发布本期" class="hand publish"/>\
    	<img src="images/contribute.gif" title="版面管理" class="hand manage"/>\
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
    baseUrl  : '?app=<?=$app?>&controller=<?=$controller?>&action=page&paperid='+paperid
});

$(function (){
	tableApp.load();
	edition.pagesize();
	$('#add').click(edition.save);
	edition.search();
	ct.nav([
		{text: '扩展'},
		{text: '报纸', url: '?app=paper&controller=paper&action=index'},
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
	tr.find('img.view').click(function (){
		edition.access(id, tr);
	});
}
function json_loaded(json) {
	$('#pagetotal').html(json.total);
}
</script>

<?php $this->display('footer','system');?>