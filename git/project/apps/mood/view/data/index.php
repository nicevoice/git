<?php $this->display('header', 'system');?>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/tablesorter/style.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/pagination/style.css"/>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.tablesorter.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.pagination.js"></script>
<script type="text/javascript" src="apps/mood/js/mood.js"></script>
<!-- 时间选择器 -->
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.datepicker.js"></script>
<link href="<?=IMG_URL?>js/lib/datepicker/style.css" rel="stylesheet" type="text/css" />

<div class="bk_8"></div>
<div class="table_head">
	<div class="tag_1">
	<div class="f_r">
		<form method="GET" name="search_f" id="search_f" action="" onsubmit="tableApp.load($('#search_f'));return false;" >
		<table cellpadding="0" cellspacing="0">
			<tr>
				<td><input type="text" name="published" id="published" class="input_calendar" value=""  size="20"/>
				&nbsp;至&nbsp;
				<input type="text" name="unpublishd" id="unpublishd" class="input_calendar" value="" size="20"/></td>
				<td class="lh_24">&nbsp;模型 <?=element::model('modelid', 'modelid', $modelid)?></td>
				<td class="lh_24">&nbsp;栏目 <?=element::category('catid', 'catid', $catid)?></td>
				<td>&nbsp; <input type="submit" name="button3"   value="列出" class="button_style_1"/></td>
			</tr>
		</table>
		</form>
	</div>
	<ul class="tag_list" id="range_tabs">
		<li><a href="javascript:void(0);" range="0" >全部</a></li>
		<li><a href="javascript:void(0);" range="1">今日</a></li>
		<li><a href="javascript:void(0);" range="7" class="s_3">本周</a></li>
		<li><a href="javascript:void(0);" range="30">本月</a></li>
	</ul>
	</div>
</div>
<table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list" style="margin-left:6px;">
	<thead><tr>
		<th width="25%" class="t_c bdr_3">标题</th>
		<th width="5%" class="header ajaxer"  id="total"><div name="total">合计</div></th>
		<?php if (is_array($rank)) foreach ($rank as $key=>$value) {
			echo "<th class=\"header ajaxer\" id=\"m{$key}\"><div name=\"m{$key}\">{$value['name']}</th></div>";
		}?>
	</tr></thead>
	<tbody id="list_body"></tbody>
</table>
<div class="table_foot">
	<div id="pagination" class="pagination f_r"></div>
	<div class="f_r"> 共有<span id="pagetotal">0</span>条记录&nbsp;&nbsp;&nbsp;每页
	<input type="text" name="pagesize" size="3" id="pagesize"  value=""/>条&nbsp;&nbsp;</div>
</div>
<script type="text/javascript">
var row_template = '<tr id="tr_{contentid}">';
	row_template +='<td name="name" value="{title}">&nbsp;<a href="{url}" target="_blank"><img src="images/link.gif" height="16" width="16"/></a>&nbsp;'+
	'<a title="{fulltitle}" class="atitle" href="javascript:mood.view({modelid},{contentid});" tips="标题：{fulltitle}">{title}</a></td><td name="total" class="t_r" size="20">{total}</td>'+
	<?php foreach($rank as $key=>$value){printf("'<td class=\"t_r\">{m%s}</td>'+",$key);}?>'';
 	row_template +='</tr>';
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

function json_loaded(json) {
	$('#pagetotal').html(json.total);
}
function init_row_event(id, tr) {
	tr.find('a.atitle').attrTips('tips');
}
$(function() {
	tableApp.load('range=7');
	$('#pagesize').val(tableApp.getPageSize());
	$('#pagesize').blur(function(){
		var p = $(this).val();
		tableApp.setPageSize(p);
		tableApp.load();
	});
	$('input.input_calendar').DatePicker({'format':'yyyy-MM-dd HH:mm:ss'});
	$('#range_tabs a').click(function(){
		var d = $(this);
		if(d.hasClass('s_3')) {
			return;
		}
		$('#range_tabs a.s_3').removeClass('s_3');
		d.addClass('s_3');
		tableApp.load('range='+d.attr('range'));
	}).focus(function(){
		this.blur();
	});
});

</script>
<?php $this->display('footer', 'system');?>
