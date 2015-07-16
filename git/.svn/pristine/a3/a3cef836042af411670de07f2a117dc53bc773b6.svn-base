<?php $this->display('header', 'system');?>

<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/tablesorter/style.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/pagination/style.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/contextMenu/style.css"/>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.contextMenu.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.tablesorter.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.pagination.js"></script>
<div class="bk_10"></div>
<div class="tag_1">
	<div class="search search_icon" style="float:right;">
	<form method="GET" name="search_f" id="search_f" action="" onsubmit="tableApp.load($('#search_f'));return false;">
		<input type="text" size="30" id="keywords" name="keywords" value="" /><a href="javascript:void(0);" onclick="tableApp.load($('#search_f'));return false;" title="搜索">搜索</a><a href="javascript:void(0);" class="more_search"  onclick="member.search(); return false;" title="高级搜索">高级搜索</a>
	</form>
	</div>
	<div class="f_l">
		<ul class="tag_list">
			<li><a value="3" href="javascript:tableApp.load('date=');" class="s_3">全部</a></li>
			<li><a value="4" href="javascript:tableApp.load('date=today');">今日</a></li>
			<li><a value="1" href="javascript:tableApp.load('date=yesterday');">昨日</a></li>
			<li><a value="2" href="javascript:tableApp.load('date=week');">本周</a></li>
			<li><a value="0" href="javascript:tableApp.load('date=month');">本月</a></li>
		</ul>
	</div>
</div>
<table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list mar_l_8">
	<thead><tr>
		<th width="30" class="t_l bdr_3"><input type="checkbox" id="check_all" class="checkbox_style"/></th>
		<th width="65" class="ajaxer"><div name="userid">ID</div></th>
		<th>用户名</th>
		<th>E-mail</th>
		<th width="120" class="t_c ajaxer"><div name="regtime">注册时间</div></th>
		<th width="80">管理操作</th>
	</tr></thead>
	<tbody id="list_body"></tbody>
</table>
<div class="table_foot">
	<div id="pagination" class="pagination f_r"></div>
	<div class="f_r"> 共有<span id="pagetotal">0</span>条记录&nbsp;&nbsp;&nbsp;每页
		<input type="text" name="pagesize" size="3" id="pagesize" value="" /> 条&nbsp;&nbsp;</div>
	<p class="f_l">
		<input type="button" onclick="member.mulDel(); return false;" value="删除" class="button_style_1"/>
		<input type="button" onclick="member.audit(); return false;" value="批量审核" class="button_style_1"/>
	</p>
</div>
<!--右键菜单-->
<ul id="right_menu" class="contextMenu">
	<li class="edit"><a href="#member.edit">编辑</a></li>
	<li class="delete"><a href="#member.del">删除</a></li>
	<li class="sendmail separator"><a href="#member.sendmail">发送邮件</a></li>
	<li class="profile"><a href="#member.view">查看资料</a></li>
</ul>
<script type="text/javascript">
var manage_td = '<img src="images/sh.gif" alt="通过" width="16" height="16" class="manage" onclick="member.audit(\'{userid}\');"/>&nbsp;<img src="images/edit.gif" alt="编辑" width="16" height="16" class="manage" onclick="member.edit({userid})"/>&nbsp;<img src="images/delete.gif" alt="删除" width="16" height="16" class="manage" onclick="member.del(\'{userid}\');"/>';

var row_template = '<tr id="row_{userid}">';
	row_template +='	<td><input type="checkbox" class="checkbox_style" name="chk_row" id="chk_row_{userid}" value="{userid}" /></td>';
	row_template +='	<td  class="t_r">{userid}</td>';
	row_template +='	<td ><a href="javascript:url.member({userid});">{username}</a></td>';
	row_template +='	<td ><a href="javascript:void(0);" onclick="member.sendmail({userid});" >{email}</a></td>';
	row_template +='	<td class="t_c">{regtime}</td>';
	row_template +='	<td class="t_c" id="manage_{userid}" name="manage" value="manage">'+manage_td+'</td>';
	row_template +='</tr>';
function init_row_event(id, tr) {
	
}

function json_loaded(json) {
	$('#pagetotal').html(json.total);
}
var tableApp = new ct.table('#item_list', {
		rowIdPrefix : 'row_',
		pageSize : 15,
		rowCallback: 'init_row_event',
		dblclickHandler : 'member.edit',
		jsonLoaded : 'json_loaded',
		template : row_template,
		baseUrl  : '?app=<?=$app?>&controller=<?=$controller?>&action=page'
});
$(function() {
	$.validate.setConfigs({
		xmlPath:'/apps/member/validators/'
	});
	tableApp.load();
	$('#pagesize').val(tableApp.getPageSize());
	$('#pagesize').blur(function(){
		var p = $(this).val();
		tableApp.setPageSize(p);
		tableApp.load();
	});
	$('.tag_list a').click(function(){
		$('.tag_list .s_3').removeClass('s_3');
		$(this).addClass('s_3');
	}).focus(function(){
		this.blur();
	});
});
</script>
<script type="text/javascript" src="apps/member/js/member.js"></script>
<?php $this->display('footer', 'system');?>