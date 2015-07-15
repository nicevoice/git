<?php $this->display('header');?>
<link href="apps/dms/css/style.css" rel="stylesheet" type="text/css" />

<!--tablesorter-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/tablesorter/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.tablesorter.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>
<!--pagination-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/pagination/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.pagination.js"></script>

<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/list/style.css"/>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.list.js"></script>

<style type="text/css">
.ct_selector {
    background-color: #FBFBFB;
    border:1px solid #ddd;
    padding:3px;
    border-color:#888 #ddd #ddd #888;
    background-color:#fbfbfb;
    font-size:12px;
    font-family:Tahoma, Verdana,"宋体";
}
.ct_selector li {
    line-height: 24px;
    margin-bottom: 0;
}
</style>

<?php $this->display('sider');?>
<div class="dms_search" id="dms_search">
	<form id="search-form" action="#" method="get">
		<ul>
			<li>
				<h2>搜索：</h2>
				<div>
					<input type="text" style="width: 171px;" name="title" size="30" id="title" placeholder="标题" />
				</div>
			</li>
			<li>
				<h2>选择应用</h2>
				<div>
					<select name="appid" id="appid">
						<option value="">所有应用</option>
						<?php foreach (table('dms_app') as $dms_app): ?>
						<option value="<?=$dms_app['appid']?>"><?=$dms_app['name']?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</li>
			<li>
				<input type="text" style="width: 171px;" role="datepicker" onclick="DatePicker(this,{'format':'yyyy-MM-dd'});" name="starttime" size="30" title="开始时间" placeholder="开始时间" />
				~
				<input type="text" style="width: 171px;" role="datepicker" onclick="DatePicker(this,{'format':'yyyy-MM-dd'});" name="endtime" size="30" title="结束时间" placeholder="结束时间" />
			</li>
			<li>
				<input class="button_style_4 f_l" type="submit" value="确定" />
			</li>
		</ul>
	</form>
</div>
<div class="dms_content">
	<div class="dms_inner">
		<div class="bk_8"></div>
		<table width="98%" cellpadding="0" cellspacing="0" id="quote_list" class="tablesorter table_list" style="margin-left:6px;">
			<thead>
				<tr>
					<th class="t_c bdr_3" width="">标题</th>
					<th width="150">APP</th>
					<th width="100">时间</th>
					<th width="100">操作人</th>
					<th width="80">状态</th>
				</tr>
			</thead>
			<tbody id="list_body">
			</tbody>
		</table>
		<div class="statusbar">
			<input type="button" class="button_style_1" name="refresh" value="刷新" onclick="tableApp.reload();" />
			<div id="pagination" class="pagination"></div>
		</div>
	</div>
</div>
<script type="text/javascript">
var templateRow	= new Array('<tr id="row_{quoteid}">',
		'<td><a onclick="ct.assoc.open(\'?app=dms&controller=article&action=edit&id={target}\', \'target\'); return false;" href="#" title="{title}">{shorttitle}</a></td>',
		'<td class="quoteapp t_c">{app}</td>',
		'<td class="quotetime t_c" tip="{long_time}">{time}</td>',
		'<td class="quoteoperator t_c">{operator}</td>',
		'<td class="quotestatus t_c">{status}</td>',
	'</tr>').join('\r\n');
var init_row_event = function(id, tr) {
	tr.find('.quotetime').attrTips('tip');
};
var tableApp = new ct.table('#quote_list', {
    rowIdPrefix : 'row_',
    pageField : 'page',
    pageSize : 15,
    template : templateRow,
	rowCallback : init_row_event,
    baseUrl  : '?app=dms&controller=quote&action=page'
});
$(document).ready(function() {
	tableApp.load();
	$('#search-form')[0].reset();
	$('#search-form').submit(function() {
		tableApp.load($('#search-form').serialize());
		return false;
	});
	$('#appid').selectlist();
});
</script>