<?php $this->display('header', 'system');?>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/tablesorter/style.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/pagination/style.css"/>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.tablesorter.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.pagination.js"></script>
<!-- 时间选择器 -->
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.datepicker.js"></script>
<link href="<?=IMG_URL?>js/lib/datepicker/style.css" rel="stylesheet" type="text/css" />

<script>
var SUCCEED = 4;
</script>
<div class="bk_10"></div>
<div class="tag_1">
	<ul class="tag_list">
		<li><a href="javascript:void(0);" onclick="SUCCEED = 4; log.search();" class="s_3" >全部</a></li>
		<li><a href="javascript:void(0);" onclick="SUCCEED = 1; log.search();">成功</a></li>
		<li><a href="javascript:void(0);" onclick="SUCCEED = 2; log.search();">失败</a></li>
	</ul>
</div>
<form id="search_f" name="search_f"  onsubmit="log.search($('#search_f').serialize());return false;" action="?app=member&controller=log&action=search" method="GET" style="left:10px">
<table class="table_form mar_5 mar_l_8" cellpadding="0" cellspacing="0" width="780px">
	<tr>
		<th>用户名：</th>
		<td>
			<input name="username" type="text" size="10" value=""/>　
		</td>
		<th>IP：</th>
		<td>
			<input name="ip" type="text" size="10" value="" />　
		</td>
		<th>日期：</th>
		<td>
			<input type="text" name="publish_d" id="publish_d" class="input_calendar" value="" size="20"/>
				 至 
			 <input type="text" name="unpublish_d" id="unpublish_d" class="input_calendar" value="" size="20"/>
			 <input type="submit" value="搜索" class="button_style_1"/>
		</td>
	</tr>
</table>
</form>
<div class="bk_10"></div>
<div class="tag_list_1 pad_8 layout mar_l_8" id="bytime_list">
	<a href="javascript:log.search();" id="all" class="s_5">全部</a>
	<a href="javascript:log.search('publish_d=<?=date('Y-m-d', TIME)?>');">今日</a>
	<a href="javascript:log.search('publish_d=<?=date('Y-m-d', strtotime('yesterday'))?>&unpublish_d=<?=date('Y-m-d', strtotime('yesterday'))?>');">昨日</a>
	<a href="javascript:log.search('publish_d=<?=date('Y-m-d', strtotime('last monday'))?>');">本周</a>
	<a href="javascript:log.search('publish_d=<?=date('Y-m-d', strtotime('last month'))?>');">本月</a>
	<div class="clear"></div>
</div>
<table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list mar_l_8 mar_t_5">
	<thead>
		<tr>
			<th width="80" class="bdr_3 ajaxer"><div name="logid">ID</div></th>
			<th width="200">用户名</th>
			<th>IP</th>
			<th width="150" class="ajaxer"><div name="time">登录时间</div></th>
			<th width="80" class="t_c">结果</th>
		</tr>
	</thead>
	<tbody id="list_body"></tbody>
</table>
<div class="table_foot">
	<div id="pagination" class="pagination f_r"></div>
	<div class="f_r"> 共有<span id="pagetotal">0</span>条记录&nbsp;&nbsp;&nbsp;每页
	<input type="text" size="3" id="pagesize" value=""/> 条&nbsp;&nbsp;</div>
	<p class="f_l">
		<input type="button" name="button" id="mul_del" onclick="log.delete_dialog(); return false;" value="删除记录" class="button_style_1"/>
	</p>
</div>

<script type="text/javascript">
	var row_template  ='<tr id="row_{logid}">';
		row_template +='	<td class="t_r">{logid}</td>';
		row_template +='	<td class="t_c">{username}</td>';
		row_template +='	<td class="t_r">{ip} {location}</td>';
		row_template +='	<td class="t_c">{time}</td>';
		row_template +='	<td  class="t_c">{succeed}</td>';
		row_template +='</tr>';
	var tableApp = new ct.table('#item_list', {
		rowIdPrefix : 'row_',
		pageSize : 15,
		rowCallback: 'init_row_event',
		jsonLoaded : 'json_loaded',
		template : row_template,
		baseUrl  : '?app=<?=$app?>&controller=<?=$controller?>&action=page'
	});

	function init_row_event(id, tr) {
		tr.unbind("click");
	}
	function json_loaded(json) {
		$('#pagetotal').html(json.total);
	}
	$(function(){
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
	});
	
	$('input.input_calendar').DatePicker({'format':'yyyy-MM-dd HH:mm:ss'});
	
	$('input.input_calendar').DatePicker({'format':'yyyy-MM-dd HH:mm:ss'});
	var log = {
		delete_dialog: function() {
			ct.form('删除登录记录','?app=<?=$app?>&controller=<?=$controller?>&action=delete', 360, 200,function(json){
				ct.tips('操作成功');
				tableApp.load();
				return true;
			},function(){return true});
		},
		search:function(params) {
            if (SUCCEED) {
                if (params) {
                    params += '&';
                } else {
                    params = '';
                }
                params += 'succeed=' + SUCCEED;
            }
			tableApp.load(params);
		}
	};
	$('.tag_list a').click(function(){
		$('.tag_list .s_3').removeClass('s_3');
		$(this).addClass('s_3');
	}).focus(function(){
		this.blur();
	});
	$('#bytime_list > a').click(function(){
		$('#bytime_list > a.s_5').removeClass('s_5');
		$(this).addClass('s_5');
	}).focus(function(){
		this.blur();
	});
</script>

<?php $this->display('footer', 'system');?>