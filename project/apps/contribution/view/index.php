<?php $this->display('header','system');?>
<!--treeview-->
<link href="<?=IMG_URL?>js/lib/treeview/treeview.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.treeview.js"></script>

<!--tablesorter-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/tablesorter/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.tablesorter.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>
<!--contextmenu-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/contextMenu/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.contextMenu.js"></script>
<!--pagination-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/pagination/style.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.pagination.js"></script>

<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/list/style.css"/>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.list.js"></script>

<script src="<?=IMG_URL?>js/lib/cmstop.tree.js" type="text/javascript"></script>
<link href="<?=IMG_URL?>js/lib/tree/style.css" rel="stylesheet" type="text/css"/>
<style>
.selectDiv {float: left;margin: 1px 8px 0 0;}
#info {	padding: 6px 12px;	text-align: center;	color: #666;width: 360px;margin: 6px auto 3px auto;}
#info span {color: #d00;}
td div.icon {background: url(<?php echo IMG_URL?>js/lib/dropdown/bg.gif) no-repeat scroll 0 -50px transparent;	margin-right: 3px;	width: 16px;height: 20px;float: left;}
</style>
<div class="bk_10"></div>
<div class="table_head">
	<form method="GET" name="search_f" id="search_f" action="" onsubmit="tableApp.load($('#search_f'));return false;">
		<div class="selectDiv">
			<?=element::category('catid', 'catid', $catid)?>
		</div>
		<div class="selectDiv">
		<select id="changestatus" name="status" style="width:60px">
			<option value="6" <?php if($_GET['status'] == 6) echo 'selected'?>>已发</option>
			<option value="3" <?php if(!isset($_GET['status']) || $_GET['status'] == 3) echo 'selected'?>>待审</option>
			<option value="2" <?php if($_GET['status'] == 2) echo 'selected'?>>退稿</option>
			<option value="1" <?php if($_GET['status'] == 1) echo 'selected'?>>草稿</option>
			<option value="0" <?php if(isset($_GET['status']) && $_GET['status'] == 0) echo 'selected'?>>回收站</option>
		</select>
		</div>
		<div class="selectDiv">
		<?php $date_str[$date] = 'selected';?>
		<select name="date" id="changedate" style="width:50px;">
			<option value="none" <?php echo $date_str['none'];?>>日期</option>
			<option value="today" <?php echo $date_str['today'];?>>今日</option>
			<option value="yesterday" <?php echo $date_str['yesterday'];?>>昨日</option>
			<option value="week" <?php echo $date_str['week'];?>>本周</option>
			<option value="month" <?php echo $date_str['month'];?>>本月</option>
		</select>
		</div>
		<div class="search_icon search">
			<input type="text" name="keywords" id="keywords" value="<?=$keywords?>" size="15"/>
			<a id="submit" href="javascript:;">搜索</a>
		</div>
	</form>
</div>
<div class="bk_8"></div>
<?php $this->display("status/$status");?>
<div id="info">
	全部(<span id="totalNum">0</span>) &nbsp;&nbsp;
	待审(<span id="waitNum">0</span>) &nbsp;&nbsp;
	已发(<span id="publishNum">0</span>) &nbsp;&nbsp;
	退稿(<span id="rejectNum">0</span>) &nbsp;&nbsp;
	草稿(<span id="draftNum">0</span>) &nbsp;&nbsp;
	回收站(<span id="removeNum">0</span>)
</div>
<!--右键菜单-->
<ul id="right_menu_0" class="contextMenu">
	<li class="view"><a href="#contribution.view">查看</a></li>
	<li class="remove"><a href="#contribution.del">彻底删除</a></li>
</ul>
<ul id="right_menu_1" class="contextMenu">
	<li class="view"><a href="#contribution.view">查看</a></li>
	<li class="remove"><a href="#contribution.remove">删除</a></li>
</ul>
<ul id="right_menu_2" class="contextMenu">
	<li class="publish"><a href="#contribution.publish">发稿</a></li>
	<li class="remove"><a href="#contribution.remove">删除</a></li>
</ul>
<ul id="right_menu_3" class="contextMenu">
	<li class="view"><a href="#contribution.view">查看</a></li>
	<li class="publish"><a href="#contribution.publish">发稿</a></li>
	<li class="reject"><a href="#contribution.reject">退稿</a></li>
</ul>
<ul id="right_menu_6" class="contextMenu">
	<li class="view"><a href="#contribution.view">查看</a></li>
</ul>
<script type="text/javascript" src="apps/contribution/js/contribution.js"></script>
<script type="text/javascript">
var param_catid = '<?php echo intval($catid);?>';
var param_status = '<?php echo intval($status);?>';
var param_date = '<?php echo $date;?>';
var param_keywords = encodeURIComponent('<?php echo $keywords;?>');

var tableApp = new ct.table('#item_list', {
	rowIdPrefix : 'row_',
	rightMenuId : 'right_menu',
	pageField : 'page',
	pageSize : 15,
	dblclickHandler : 'contribution.view',
	jsonLoaded : 'json_loaded',
	rowCallback : 'init_row_event',
	template : row_template,
	baseUrl  : '?app=<?=$app?>&controller=<?=$controller?>&action=page&status=' + param_status
});
$("#catid").bind('changed',function(){
	param_catid = this.value;
	tableApp.load('status='+param_status+'&catid='+param_catid+'&date='+param_date+'&keywords='+param_keywords);
});

function init_row_event(id, tr) {
	tr.find('a.title_list').attrTips('tips');
}

function json_loaded(json) {
	$('#pagetotal').html(json.total);
	$('#totalNum').html(json.count.total);
	$('#waitNum').html(json.count.wait);
	$('#publishNum').html(json.count.publish);
	$('#rejectNum').html(json.count.reject);
	$('#draftNum').html(json.count.draft);
	$('#removeNum').html(json.count.remove);
}
tableApp.load('catid='+param_catid+'&date='+param_date+'&keywords='+param_keywords);
$(function(){
	$('#pagesize').val(tableApp.getPageSize());
	$("#keywords").change(function(){
		param_keywords = encodeURIComponent($(this).val());
	});
	$('#changestatus').selectlist().bind('changed',function(e, t){
		param_status = t.checked[0];
		window.location.href = '?app=contribution&controller=index&action=index&status='+param_status+'&catid='+param_catid+'&date='+param_date+'&keywords='+param_keywords;
	});
	$('#submit').click(function(){
		tableApp.load('status='+param_status+'&catid='+$('#catid').val()+'&date='+param_date+'&keywords='+param_keywords);
	});
	$('#changedate').selectlist().bind('changed',function(e, t){
		param_date = t.checked[0];
		tableApp.load('status='+param_status+'&catid='+param_catid+'&date='+param_date+'&keywords='+param_keywords);
	});
});
</script>
<?php $this->display('footer','system'); ?>