<?php $this->display('header', 'system');?>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/tablesorter/style.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/pagination/style.css"/>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.tablesorter.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.pagination.js"></script>
<!-- 时间选择器 -->
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.datepicker.js"></script>
<link href="<?=IMG_URL?>js/lib/datepicker/style.css" rel="stylesheet" type="text/css" />

<div class="bk_10"></div>
<div class="table_head">
	<div class="tag_1">
		<ul class="tag_list">
			<li><a href="javascript:void(0);" onclick="tableApp.load('range=0');tab('#tab_4');return false;" class="s_3" id="tab_4">全部</a></li>
			<li><a href="javascript:void(0);" onclick="tableApp.load('range=1');tab('#tab_1');return false;" id="tab_1">今日</a></li>
			<li><a href="javascript:void(0);" onclick="tableApp.load('range=7');tab('#tab_2');return false;" id="tab_2">本周</a></li>
			<li><a href="javascript:void(0);" onclick="tableApp.load('range=30');tab('#tab_3');return false;" id="tab_3">本月</a></li>
		</ul>
		<div class="f_r">
		<form method="GET" action="javascript:;" name="search_f" id="search_f">
			<table cellpadding="0" cellspacing="0">
				<tr>
					<td><input type="text" name="publish_d" id="publish_d" class="input_calendar" value="<?=$publish_d?>"  size="20"/>
					&nbsp;至&nbsp;
					<input type="text" name="unpublish_d" id="unpublish_d" class="input_calendar" value="<?=$unpublish_d?>" size="20"/> </td>
					<td>&nbsp; <select name="log_type" id="type">
						<option value=1>动作url</option>
						<option value=2>IP</option>
						<option value=3>用户名</option>
						<option value=4>用户ID</option>
						</select> &nbsp;
					</td>  
					<td><input type="text" name="keywords" id="keywords" size="20" value=""> </td>
					<td><input type="button" id="submit" class="button_style_1" value="搜索"/></td>
				</tr>
			</table>
		</form>
		</div>
	</div>
</div>
<!--头部结束-->
<table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list mar_l_8">
	<thead><tr>      
		<th width="10%" class="bdr_3">操作人</th>
		<th width="55%">链接地址</th>
		<th width="20%" class="ajaxer"><div name="time">操作时间</div></th>
		<th width="20%" class="ajaxer"><div name="ip">操作IP</div></th>
	</tr></thead>
	<tbody id="list_body"></tbody>
</table>
<!--删除-->
<div class="table_foot">
	<div id="pagination" class="pagination f_r"></div>
	<p class="f_l">
		<input type="button" id="button" onclick="del_log(); return false;" value="清除" class="button_style_1" />
		<input type="text" onkeyup="value=this.value.replace(/\D+/g,'')" id="del_all" value="30" size="2" >天之前的记录
	</p>
</div>
<script type="text/javascript">
var row_template = '<tr id="row_{logid}">';
	row_template +='	<td name="name" size="20" class="t_c"><a href="javascript: url.member({userid});">{username}</a></td>';
	row_template +='	<td name="url" value="{url}" class="title_list" tips="ID：{logid}<br />操作人：{userid}<br />操作时间：{time}<br />操作IP地址：{ip}({iplocation})"  size="45">{url}</td>';
	row_template +='	<td class="t_c">{time}</td>';
	row_template +='	<td class="t_c">{ip}<span class="c_gray">({iplocation})<span></td>';
	row_template +='</tr>';
var tableApp = new ct.table('#item_list', {
		rowIdPrefix : 'row_',
		pageSize : 15,
		rowCallback: 'init_row_event',
		dblclickHandler : 'member.edit',
		jsonLoaded : 'json_loaded',
		template : row_template,
		baseUrl  : '?app=<?=$app?>&controller=<?=$controller?>&action=page'
});
function init_row_event(id, tr) {
	
}

function json_loaded(json) {
	$('#pagetotal').html(json.total);
}

$(function() {
	tableApp.load();
	$('#submit').click(function (){
		var get = $('#search_f').serialize();
		tableApp.load(get);
	});
	$('input.input_calendar').DatePicker({'format':'yyyy-MM-dd HH:mm:ss'});
});
function tab(id) {
	$('.s_3').removeClass('s_3');
	$(id).addClass('s_3');
}
function del_log() {
	var del_all =$('#del_all').val();
	if(del_all == '') {
		ct.warn('请输入天数');
		$('#del_all').focus();
		return false;
	}
	ct.confirm(
		'此操作不可恢复，确认删除'+del_all+'天的之前的记录？',
		function() {
			$.getJSON("?app=system&controller=adminlog&action=del_all",{range:del_all}, function(json){
				if(json.state) {
					ct.ok(json.message);
					tableApp.load();
				} else {
					ct.error(json.error);
				}
			});
		}
	);
}

</script>
<?php $this->display('footer', 'system');