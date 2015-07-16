<?php $this->display('header', 'system');?>

<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/tablesorter/style.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/pagination/style.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/contextMenu/style.css"/>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.tablesorter.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.contextMenu.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.pagination.js"></script>
<script type="text/javascript" src="apps/space/js/space.js"></script>

<link rel="stylesheet" type="text/css" href="<?=IMG_URL?>js/lib/autocomplete/style.css" />
<script src="<?=IMG_URL?>js/lib/cmstop.autocomplete.js" type="text/javascript"></script>
<script type="text/javascript" src="uploader/cmstop.uploader.js"></script>
<script type="text/javascript" src="imageEditor/cmstop.imageEditor.js"></script>
<script type="text/javascript" src="js/cmstop.filemanager.js"></script>

<div class="bk_10"></div>
<div class="tag_1">
	<div class="search search_icon" style="float:right;">
	<form method="GET" name="search_f" id="search_f" action="" onsubmit="tableApp.load($('#search_f'));return false;">
		<input type="text" size="30" id="keywords" name="keywords" value="" /><a href="javascript:void(0);" onclick="tableApp.load($('#search_f'));return false;"  title="搜索">搜索</a><a href="javascript:void(0);" class="more_search"  onclick="space.search(); return false;" title="高级搜索">高级搜索</a>
	</form>
	</div>
	<div class="f_l">
		<button type="button" class="button_style_4 f_l" onclick="space.add()">添加专栏</button>
	</div>
	<ul class="tag_list">
		<li><a value="3" href="javascript:void(0);" class="s_3">已开通</a></li>
		<li><a value="4" href="javascript:void(0);">已推荐</a></li>
		<li><a value="1" href="javascript:void(0);">审核中</a></li>
		<li><a value="2" href="javascript:void(0);">未批准</a></li>
		<li><a value="0" href="javascript:void(0);">已禁用</a></li>
	</ul>
</div>
<table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list mar_l_8">
	<thead>
		<tr>
			<th width="30" class="t_l bdr_3"><input type="checkbox" id="check_all" class="checkbox_style"/></th>
			<th>专栏名</th>
			<th width="85">作者名</th>
			<th width="85">用户名</th>
			<th width="100">管理操作</th>
			<th width="75" class="ajaxer"><div name="posts">文章数</div></th>
			<th width="75" class="t_c ajaxer"><div name="pv">浏览量</div></th>
			<th width="75" class="t_c ajaxer" ><div name="comments">评论</div></th>
			<th width="120" class="t_c ajaxer"><div name="created">创建时间</div></th>
		</tr>
	</thead>
	<tbody id="list_body"></tbody>
</table>
<div class="table_foot">
	<div id="pagination" class="pagination f_r"></div>
	<div class="f_r"> 共有<span id="pagetotal">0</span>条记录&nbsp;&nbsp;&nbsp;每页
	<input type="text" name="pagesize" size=3 id="pagesize" value=""/> 条&nbsp;&nbsp;</div>
	<p class="f_l">
		<input type="button" onclick="space.mulDel(); return false;" value="删除" class="button_style_1" id="mul_del"/>
		<input type="button" onclick="space.mulOpen(); return false;" value="开通" class="button_style_1 dis_n mulCss" id="mul_open"/>
		<input type="button" onclick="space.mulBan(); return false;" value="禁用" class="button_style_1 mulCss" id="mul_ban"/>
		<input type="button" onclick="space.mulRecommend(); return false;" value="推荐" class="button_style_1 mulCss" id="mul_recommend"/>
		<input type="button" onclick="space.mulCancel(); return false;" value="取消推荐" class="button_style_1 dis_n mulCss" id="mul_cancel"/>
	</p>
</div>
<!--右键菜单-->
<ul id="right_menu" class="contextMenu">
	<li class="edit"><a href="#space.edit">编辑</a></li>
	<li class="delete"><a href="#space.del">删除</a></li>
</ul>
<ul id="right_menu_0" class="contextMenu">
	<li class="edit"><a href="#space.edit">编辑</a></li>
	<li class="delete"><a href="#space.del">删除</a></li>
	<li><a href="#space.open">开通</a></li>
</ul>
<ul id="right_menu_1" class="contextMenu">
	<li class="edit"><a href="#space.edit">编辑</a></li>
	<li class="delete"><a href="#space.del">删除</a></li>
	<li><a href="#space.open">开通</a></li>
</ul>
<ul id="right_menu_2" class="contextMenu">
	<li class="edit"><a href="#space.edit">编辑</a></li>
	<li class="delete"><a href="#space.del">删除</a></li>
	<li><a href="#space.open">开通</a></li>
</ul>
<ul id="right_menu_3" class="contextMenu">
	<li class="edit"><a href="#space.edit">编辑</a></li>
	<li class="delete"><a href="#space.del">删除</a></li>
	<li><a href="#space.recommend">推荐</a></li>
	<li><a href="#space.ban">禁用</a></li>
</ul>
<ul id="right_menu_4" class="contextMenu">
	<li class="edit"><a href="#space.edit">编辑</a></li>
	<li class="delete"><a href="#space.del">删除</a></li>
	<li><a href="#space.ban">禁用</a></li>
	<li><a href="#space.cancel">取消推荐</a></li>
</ul>
<script type="text/javascript">
$.validate.setConfigs({
	xmlPath:'/apps/space/validators/'
});
var manage_td     ='<img width="16" height="16" alt="添加文章" title="添加文章" src="images/add_1.gif" onclick="space.published(\'{author}\')" class="hand add_{status}"> &nbsp;<a target="_blank" href="<?=SPACE_URL?>{alias}"><img width="16" height="16" alt="访问" title="访问" src="images/view.gif"></a> &nbsp;<img src="images/edit.gif" alt="编辑"  title="编辑" width="16" height="16" class="manage" onclick="space.edit({spaceid})"/> &nbsp;<img src="images/delete.gif" alt="删除" title="删除" width="16" height="16" class="manage" onclick="space.del(\'{spaceid}\');"/>';
var row_template  ='<tr id="row_{spaceid}" right_menu_id="right_menu_{status}">';
	row_template +='	<td><input type="checkbox" class="checkbox_style" id="chk_row_{spaceid}" value="{spaceid}" /></td>';
	row_template +='	<td ><a href="javascript:space.panel({spaceid})" tips="作者名：{author}<br/>用户：{username}<br/>状态：{status_name}<br/>文章数：{posts}<br/>评论数：{comments}<br/>PV：{pv}<br/>创建时间：{created}<br/>修改时间：{modified}" class="title_list">{name}</a>{icon}</td>';
	row_template +='	<td class="t_c"><a target="_blank" href="<?=SPACE_URL?>{alias}">{author}</a></td>';
	row_template +='	<td class="t_c"><a href="javascript:url.member({userid});">{username}</a></td>';
	row_template +='	<td class="t_c" id="manage_{spaceid}" name="manage" value="manage">'+manage_td+'</td>';
	row_template +='	<td class="t_c">{posts}</td>';
	row_template +='	<td class="t_c">{pv}</td>';
	row_template +='	<td class="t_c">{comments}</td>';
	row_template +='	<td class="t_c">{created}</td>';
	row_template +='</tr>';

var tableApp = new ct.table('#item_list', {
		rowIdPrefix : 'row_',
		pageSize : 15,
		rowCallback: 'init_row_event',
		dblclickHandler : 'space.edit',
		jsonLoaded : 'json_loaded',
		template : row_template,
		baseUrl  : '?app=<?=$app?>&controller=<?=$controller?>&action=page&status=3'
});
function init_row_event(id, tr) {
	tr.find('a.title_list').attrTips('tips');
	tr.find('.add_0,.add_1,.add_2').remove();
}

function json_loaded(json) {
	$('#pagetotal').html(json.total);
}

$(function() {
	tableApp.load();
	$('#pagesize').val(tableApp.getPageSize());
	$('#pagesize').blur(function(){
		var p = $(this).val();
		tableApp.setPageSize(p);
		tableApp.load();
	});
	$('img.tips').attrTips('tips', 'tips_green', 200, 'top');
	$('.tag_list a').click(function(){
		$('.tag_list a.s_3').removeClass('s_3');
		$(this).addClass('s_3');
		var status = $(this).attr('value');
		tableApp.load('status='+status);
		$('.mulCss').hide();
		//mul_open mul_recommend mul_ban mul_cancel
		if(status ==0) {
			$('#mul_open,#mul_del').show();
		} else if(status == 1) {
			$('#mul_open,#mul_del').show();
		} else if(status == 2) {
			$('#mul_open,#mul_del').show();
		} else if(status == 3) {
			$('#mul_ban,#mul_recommend').show();
		} else if(status == 4) {
			$('#mul_ban,#mul_cancel').show();
		}
	}).focus(function(){
		this.blur();
	});
});
</script>
<?php $this->display('footer', 'system');?>