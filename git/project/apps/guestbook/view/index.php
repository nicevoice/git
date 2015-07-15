<?php $this->display('header', 'system');?>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/tablesorter/style.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/pagination/style.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/contextMenu/style.css"/>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.tablesorter.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.contextMenu.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.pagination.js"></script>
<script type="text/javascript" src="apps/guestbook/js/guestbook.js"></script>
<!-- 时间选择器 -->
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.datepicker.js"></script>
<link href="<?=IMG_URL?>js/lib/datepicker/style.css" rel="stylesheet" type="text/css" />

<div class="bk_10"></div>
<div class="tag_1 mar_l_8">
	<ul class="tag_list" id="bystate_list">
		<li><a href="javascript:tableApp.load('status=0');" class="s_3" >全部</a></li>
		<li><a href="javascript:tableApp.load('status=1');" >未查看</a></li>
		<li><a href="javascript:tableApp.load('status=2');" >已回复</a></li>
	</ul>
</div>
<table class="table_form mar_5 mar_l_8" cellpadding="0" cellspacing="0" width="98%">
	<tr>     
		<th>检索</th><td>
		<form name="search_f" id="search_f" action="?app=digg&controller=digg&action=search" method="GET" onsubmit="tableApp.load($('#search_f'));return false;">
			<input type="text" name="published" id="published" class="input_calendar" value="" size="20"/>
			至
			<input type="text" name="unpublished" id="unpublished" class="input_calendar" value="" size="20"/>
			 <?=element::guestbook_type('typeid', 'typeid', $typeid)?> 
			<input name="keywords" id="keywords" type="text" size="20" value=""/> 
			<input type="submit" id="g_search" value="搜索" class="button_style_1"/>
		</form>
	</td>
	</tr>
</table>  
<div class="bk_8"></div>
<div class="tag_list_1 pad_8 layout mar_l_8" id="bytime_list">
	<a href="javascript: tableApp.load('status=0');" class="s_5">全部</a>
	<a href="javascript: tableApp.load('published=<?=date('Y-m-d', TIME)?>');">今天</a>
	<a href="javascript: tableApp.load('published=<?=date('Y-m-d', strtotime('yesterday'))?>&unpublished=<?=date('Y-m-d', strtotime('yesterday'))?>');">昨天</a>
	<a href="javascript: tableApp.load('published=<?=date('Y-m-d', strtotime('last monday'))?>');">本周</a>
	<div class="clear"></div>
</div>
<div class="bk_8"></div>
<table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list mar_l_8">
	<thead><tr>
		<th width="35" class="t_l bdr_3"><input type="checkbox" id="check_all" /></th>
		<th width="10%" class="ajaxer"><div name="typeid">类型</div></th>
		<th >留言标题</th>
		<th width="12%">留言人</th>
		<th width="15%" class="ajaxer"><div name="addtime">发布时间</div></th>
		<th width="9%">状态</th>
		<th width="80">操作管理</th>
	</tr></thead>
	<tbody id="list_body"></tbody>
</table>
<div class="clear"></div>
<div class="table_foot" style="width:98%">
<div id="pagination" class="pagination f_r"></div>
<div class="f_r">
	 共有<span id="pagetotal">0</span>条记录&nbsp;&nbsp;&nbsp;每页
	<input  value="" id="pagesize" size="1" />条&nbsp;&nbsp;  
</div>
<div class="f_l">
	<input type="button" name="button" id="button" onclick="guestbook.del(); return false;" value="删 除" class="button_style_1"/>
</div>
</div>
<ul id="right_menu" class="contextMenu">
	<li class="edit"><a href="#guestbook.view">编辑</a></li>
	<li class="delete"><a href="#guestbook.del">删除</a></li>
</ul>
<script type="text/javascript">
var manage_td = '<img src="images/edit.gif" title="修改" alt="修改" width="16" height="16" class="manage" onclick="guestbook.view(\'{gid}\');"/> &nbsp;<img src="images/delete.gif" title="删除" alt="删除" width="16" height="16" class="manage" onclick="guestbook.del(\'{gid}\');"/>';
var row_template = '<tr id="row_{gid}">';
row_template +='	<td><input type="checkbox" class="checkbox_style" name="chk_row" id="chk_row_{gid}" value="{gid}" /></td>';
row_template +='	<td class="t_c">{typename}</td>';
row_template +='	<td><a href="javascript:void(0);" class="title_list" tips="ID：{gid}<br />留言人：{username}<br />操作时间：{addtime}<br />操作IP地址：{ip}" class="view_\'{isview}\'" onclick="guestbook.view(\'{gid}\')">{title}</a></td>';
row_template +='	<td><a href="javascript: url.member({userid});">{username}</a></td>';
row_template +='	<td class="t_c">{addtime}</td>';
row_template += '    <td class="t_c">{state}</td>';
row_template +='	<td class="t_c" id="manage_{gid}" name="manage" value="manage">'+manage_td+'</td>';
row_template +='</tr>';

var tableApp = new ct.table('#item_list', {
	rowIdPrefix : 'row_',
	pageSize : 15,
	rowCallback: 'init_row_event',
	dblclickHandler : 'guestbook.view',
	jsonLoaded : 'json_loaded',
	template : row_template,
	baseUrl  : '?app=<?=$app?>&controller=<?=$controller?>&action=page'
});
function init_row_event(id, tr) { }

function json_loaded(json) {
	$('#pagetotal').html(json.total);
}

$(function() {
	$.validate.setConfigs({
		xmlPath:'/apps/guestbook/validators/'
	});
	tableApp.load();
	$('#pagesize').val(tableApp.getPageSize());
	$('#pagesize').blur(function(){
		var p = $(this).val();
		tableApp.setPageSize(p);
		tableApp.load();
	});
	$('input.input_calendar').DatePicker({'format':'yyyy-MM-dd HH:mm:ss'});

	$('#bytime_list > a').click(function(){
		$('#bytime_list > a.s_5').removeClass('s_5');
		$(this).addClass('s_5');
	});
	$('#bystate_list a').click(function(){
		$('#bystate_list a.s_3').removeClass('s_3');
		$(this).addClass('s_3');
	});
});
</script>
<?php $this->display('footer', 'system');?>