<?php $this->display('header', 'system');?>

<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/tablesorter/style.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/pagination/style.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/contextMenu/style.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/dropdown/style.css"/>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.dropdown.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.tablesorter.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.contextMenu.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.pagination.js"></script>

<div class="bk_10"></div>
<div class="tag_1">
	<div class="search search_icon" style="float:left;">
	<form method="GET" name="search_f" id="search_f" action="" onsubmit="tableApp.load($('#search_f'));return false;">
		<input type="text" size="30" id="keywords" name="keywords" value="" /><a href="javascript:void(0);" onclick="tableApp.load($('#search_f'));return false;"  title="搜索">搜索</a><a href="javascript:void(0);" class="more_search"  onclick="member.search(); return false;" title="高级搜索">高级搜索</a>
	</form>
	</div>
	<!--<div class="f_l">
		<input type="button" name="add" id="add" value="添加用户" class="button_style_4 f_l" onclick="member.add();return false;"/>
		<div style="left:100px;position:absolute;">
			<select id="changeaction" style="width:100px;left:300px;" marginTop="1" marginLeft="0">
				<?php foreach($tabs as $key => $value){ if($key!=2) {?>
					<option value="<?=$key?>" <?=$groupid == $key ? 'selected' : ''?>><?=$value?></option>
				<?php }}?>
			</select>
		</div>
	</div>
	<ul class="tag_list" style="left:220px;position:absolute;">
		<li><a value="3" href="javascript:tableApp.load('date=');" class="s_3">全部</a></li>
		<li><a value="4" href="javascript:tableApp.load('date=today');">今日</a></li>
		<li><a value="1" href="javascript:tableApp.load('date=yesterday');">昨日</a></li>
		<li><a value="2" href="javascript:tableApp.load('date=week');">本周</a></li>
		<li><a value="0" href="javascript:tableApp.load('date=month');">本月</a></li>
	</ul>-->
</div>
<table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list mar_l_8">
	<thead>
		<tr>
			<th width="30" class="t_l bdr_3"><input type="checkbox" id="check_all" class="checkbox_style"/></th>
			<th width="60" class="ajaxer"><div name="userid">ID</div></th>
			<th width="100" >用户名</th>
			<!--<th width="200">E-mail</th>-->
			<th width="85" class="ajaxer"><div name="groupid">用户组</div></th>
			<!--<th width="110" class="t_c ajaxer"><div name="regtime">注册时间</div></th>-->
			<th class="t_c ajaxer" ><div name="lastlogintime">最后登录</div></th>
			<th width="60">解锁操作</th>
		</tr>
	</thead>
	<tbody id="list_body"></tbody>
</table>
<div class="table_foot">
	<div id="pagination" class="pagination f_r"></div>
	<div class="f_r"> 共有<span id="pagetotal">0</span>条记录&nbsp;&nbsp;&nbsp;每页
	<input type="text" name="pagesize" size=3 id="pagesize" value=""/> 条&nbsp;&nbsp;</div>
	<p class="f_l">
		<input type="button" onclick="member.mulDel(); return false;" value="删除" class="button_style_1"/>
		<input type="button" onclick="member.remarks(); return false;" value="备注" class="button_style_1"/>
		<input type="button" onclick="member.changeGroup(); return false;" value="移动" class="button_style_1"/>
	</p>
</div>
<!--右键菜单-->
<ul id="right_menu" class="contextMenu">
	<li class="delete"><a href="#member.unlock">解锁</a></li>
</ul>
<script type="text/javascript" src="apps/member/js/member.js"></script>
<script type="text/javascript">
var manage_td     ='<img src="images/delete.gif" alt="解锁" width="16" height="16" class="manage" onclick="member.unlock(\'{userid}\');"/>';
var row_template  ='<tr id="row_{userid}">';
	row_template +='	<td><input type="checkbox" class="checkbox_style" id="chk_row_{userid}" value="{userid}" /></td>';
	row_template +='	<td class="t_r">{userid}</td>';
	row_template +='	<td ><a href="javascript:url.member({userid});">{username}</a></td>';
	row_template +='	<td class="t_c"><a href="javascript:void(0);" onclick="member.showGroup({groupid});return false;" >{group}</a></td>';
	row_template +='	<td class="t_c">{lastlogintime} {location}</td>';
	row_template +='	<td class="t_c" id="manage_{userid}" name="manage" value="manage">'+manage_td+'</td>';
	row_template +='</tr>';

var tableApp = new ct.table('#item_list', {
		rowIdPrefix : 'row_',
		pageSize : 15,
		rowCallback: 'init_row_event',
		jsonLoaded : 'json_loaded',
		template : row_template,
		baseUrl  : '?app=<?=$app?>&controller=<?=$controller?>&action=locked_page'
});
function init_row_event(id, tr) {
}

function json_loaded(json) {
	$('#pagetotal').html(json.total);
}

$(function() {
	$.validate.setConfigs({
		xmlPath:'apps/member/validators/'
	});
	tableApp.load();
	$('#pagesize').val(tableApp.getPageSize());
	$('#pagesize').blur(function(){
		var p = $(this).val();
		tableApp.setPageSize(p);
		tableApp.load();
	});
	$('#changeaction').dropdown({
		changeaction:{
			onchange:function(id, name){
				tableApp.load('groupid='+id);
			}
		}
	});
	$('.tag_list a').click(function(){
		$('.tag_list .s_3').removeClass('s_3');
		$(this).addClass('s_3');
	}).focus(function(){
		this.blur();
	});
});
$("#usergroup").change(function(){
	tableApp.load('groupid='+this.value);
});
</script>
<?php $this->display('footer', 'system');?>