<?php $this->display('header', 'system');?>
<!--tablesorter-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/tablesorter/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.tablesorter.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>
<!--pagination-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/pagination/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.pagination.js"></script>
<script type="text/javascript" src="apps/digg/js/digg.js"></script>
<!-- 时间选择器 -->
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.datepicker.js"></script>
<link href="<?=IMG_URL?>js/lib/datepicker/style.css" rel="stylesheet" type="text/css" />

<div class="bk_8"></div>
<div class="table_head">
	<div class="tag_1">
		<div class="f_r">
			<form name="search_f" id="search_f" action="?app=digg&controller=digg&action=search" method="GET" onsubmit="tableApp.load($('#search_f'));return false;">
				<table cellpadding="0" cellspacing="0"><tr>
					<td><input type="text" name="published" id="published" class="input_calendar" value="" size="20"/>
					&nbsp;至&nbsp;
					<input type="text" name="unpublishd" id="unpublishd" class="input_calendar" value="" size="20"/></td>
					<td class="lh_24">&nbsp;模型 <?=element::model('modelid', 'modelid', $modelid)?></td>
					<td class="lh_24">&nbsp;栏目 <?=element::category('catid', 'catid', $catid)?></td>
					<td>&nbsp; <input type="submit" name="button3" id="rway" value="列出" class="button_style_1"/></td>
				</tr></table>
			</form>  
		</div>
		<ul class="tag_list">
			<li><a href="javascript:void(0);" onclick="tableApp.load('orderby=supports%7Cdesc');digg.tab('supports');return false;this.blur();" class="s_3" id="supports">顶最多</a></li>
			<li><a href="javascript:void(0);" onclick="tableApp.load('orderby=againsts%7Cdesc');digg.tab('againsts');return false;this.blur();" id="againsts">踩最多</a></li>
		</ul>
	</div>
</div>
<table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list mar_l_8">
	<thead><tr>
		<th class="bdr_3">标题</th>
		<th width="80" class="ajaxer"><div name="supports">顶</div></th>
		<th width="80" class="ajaxer"><div name="againsts">踩</div></th>
		<th width="60">栏目</th>
		<th width="60">类型</th>
		<th width="80">发布人</th>
		<th width="120" class="ajaxer"><div name="published">发布时间</div></th>
	</tr></thead>
	<tbody id="list_body"></tbody>
</table>
<div class="clear"></div>
<div class="table_foot">
	<div id="pagination" class="pagination f_r"></div>
	<div class="f_r"> 共有<span id="pagetotal">0</span>条记录&nbsp;&nbsp;&nbsp;每页
	<input type="text" name="pagesize" size=3 id="pagesize" value=""/> 条&nbsp;&nbsp;</div>
</div>
<script type="text/javascript">
var row_template = '<tr id="row_{contentid}">';
row_template +='	<td><a href="{url}" target="_blank"><img src="images/link.gif" height="16" width="16"/></a>'
+'&nbsp;&nbsp;&nbsp;<a href="javascript: digg.view({modelid},{contentid})" class="title_list" tips="标题：{title}<br />顶：{againsts}<br />踩：{supports}<br />">{title}</a></td>';
row_template +='	<td class="t_s t_c" id="{contentid}">{supports}</td>';
row_template +='	<td class="t_a t_c" id="{contentid}">{againsts}</td>';
row_template +='	<td class="t_c"><a href="javascript:void(0);" onclick="tableApp.load(\'catid={catid}\');">{catname}</a></td>';
row_template +='	<td class="t_c">{modelname}</td>';
row_template +='	<td class="t_c"><a href="javascript:void(0);" onclick="tableApp.load(\'createdby={createdby}\');">{createdbyname}</a></td>';
row_template +='	<td class="t_c">{published}</td>';
row_template +='</tr>';
//url.member({createdby})
var tableApp = new ct.table('#item_list', {
	rowIdPrefix : 'row_',
	rightMenuId : 'right_menu',
	pageField : 'page',
	pageSize : 15,
	jsonLoaded : 'json_loaded',
	rowCallback     : 'init_row_event',
	template : row_template,
	baseUrl  : '?app=<?=$app?>&controller=<?=$controller?>&action=page'
});

function init_row_event(id, tr) { }
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
	$('input.input_calendar').DatePicker({'format':'yyyy-MM-dd HH:mm:ss'});
});

</script>
<?php $this->display('footer', 'system');?>