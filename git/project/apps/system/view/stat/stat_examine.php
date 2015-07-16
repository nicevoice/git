<?php $this->display('header', 'system');?>
<script src="<?=IMG_URL?>js/lib/cmstop.list.js" type="text/javascript"></script>
<link href="<?=IMG_URL?>js/lib/list/style.css" rel="stylesheet" type="text/css" />

<script src="<?=IMG_URL?>js/lib/cmstop.tree.js" type="text/javascript"></script>
<link href="<?=IMG_URL?>js/lib/tree/style.css" rel="stylesheet" type="text/css"/>
<!--tablesorter-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/tablesorter/style.css" />
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.tablesorter.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>
<!--pagination-->
<link rel="stylesheet" type="text/css" href="<?=IMG_URL?>js/lib/pagination/style.css" />
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.pagination.js"></script>
<style type="text/css">
.bg_3{
 width:154px;
}
</style>
<div class="bk_8"></div>


<div id="tree_in" style="background:none">
	  <div class="w_160 box_6 f_l" style="position:relative;width:14%;height:740px;background: #F9FCFD;border-right:1px solid #94C5E5;border-top:1px solid #94C5E5">
		<h3><span class="dis_b b">部门列表</span></h3>
		<div id="department" style="position:absolute;z-index:3;">
		
		</div>
	  </div>
  <div class="f_l" style="width:85%">
  <div class="tag_1" style="background:none">

<table  border="0" width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td width="430">
	<form name="condition" id="condition">
	<table width="100%" border="0" cellpadding="0" cellspacing="0" >
  <tr>
    <td width="40">
    <select id="year" name="year" style="width:50px;z-index:99;" marginTop="1" marginLeft="0">
    <?php 
    $default_year = $month == 1 ? $year-1 : $year;
    $default_month = $month == 1 ? 12: $month;
    for($y = $year; $y > $year-10; $y--):?>
  		  <option value="<?=$y?>"<?php if($y == $default_year):?> selected="selected"<?php endif;?>><?=$y?></option>
  	<?php endfor;?>
	  </select></td>
    <td width="20" align="center">年</td>
    <td width="40"><select id="month" name="month" style="width:20px;z-index:99;" marginTop="1" marginLeft="0">
    <?php for ($m = 1; $m <= 12; $m++): ?>
  		  <option value="<?=sprintf("%02s", $m)?>"<?php if($m == $default_month):?> selected="selected"<?php endif;?>><?=sprintf("%02s", $m)?></option>
  	<?php endfor;?>
	  </select></td>
    <td width="20" align="center">月</td>
    <td width="72"><select id="model" name="modelid" style="width:80px;" marginTop="1" marginLeft="0">
    <option value="" selected="selected">所有模型</option>
    <?php foreach ($models as $m): ?>
  		  <option value="<?=$m['modelid']?>"><?=$m['name']?></option>
  	<?php endforeach;?>
	  </select></td>
    <td width="10">&nbsp;</td>
    <td width="72"><input style="display:none" id="category" width="100" class="selectree" name="catid" url="?app=system&controller=category&action=cate&catid=%s" paramVal="catid" paramTxt="name" multiple="multiple"/></td>
    <td width="10">&nbsp;</td>
    <td width="240"><input type="button" class="button_style_2" value="查询" onclick="stat();"/>&nbsp;<input type="button" class="button_style_4" value="导出EXECL" onclick="exportdata(true);"/></td>
    <td>&nbsp;</td>
  </tr>
</table>
	</form>
</td>
    <td align="right">
	<table border="0" cellpadding="0" cellspacing="0" >
    <tr>
    <td>统计时间：<span id="last_stat_time"></span></td>
    <td width="10">&nbsp;</td>
    <td><a href="javascript:;" onclick="restat()">重新统计</a></td>
  </tr>
</table>
</td>
  </tr>
</table>
</div>
  <table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list mar_l_8">
	<thead><tr>
		<th width="30" class="t_l bdr_3"><input type="checkbox" id="check_all" /></th>
		<th width="60">编辑</th>
		<th class="sorter"><div>发稿量</div></th>
		<th class="sorter"><div>访问量</div></th>
		<th class="sorter"><div>评论量</div></th>
		<th class="sorter"><div>评分</div></th>
		<th width="10%">操作</th>
	</tr></thead>
	<tbody></tbody>
	<tfoot>
		<tr>
			<td></td>
			<td>合计</td>
			<td class="t_c posts"></td>
			<td class="t_c pv"></td>
			<td class="t_c comments"></td>
			<td class="t_c score"></td>
			<td></td>
		</tr>
	</tfoot>
</table>
  <div class="table_foot">
	<p class="f_l">
		<input type="button" name="button" id="button" onclick="exportdata(false);" value="导出EXECL" class="button_style_1"/>
	</p>
</div>
  </div>
  <div class="clear"></div>
</div>
<script type="text/javascript">
$('#department').tree({
	url:"?app=system&controller=stat_examine&action=depart_tree&departmentid=%s",
	paramId : 'departmentid',
	paramHaschild:"hasChildren",
	renderTxt:function(div, id, item){
		return $('<span>'+item.name+'</span>');
	},
	active : function(div, id, item){
		var d = $('#condition').find('input[name="departmentid"]:hidden');
		if(!d[0])  $('#condition').append('<input type="hidden" name="departmentid" value="'+id+'"/>')
		else d.val(id);
		tableApp.load($('#condition'));
	}
});

$('#year,#month,#model').selectlist({alt:'所有模型'});
$("#category").selectree({alt:'所有栏目'});

var row_template = '<tr id="row_{userid}">';
row_template +='	<td><input type="checkbox" class="checkbox_style" name="chk_row" id="chk_row_{id}" value="{userid}" /></td>';
row_template +='	<td class="editor"><a href="javascript:;" onclick="ct.assoc.open(\'?app=member&controller=index&action=profile&userid={userid}\',\'newtab\')" >{name}</td>';
row_template +='	<td class="t_c posts">{posts}</td>';
row_template +='    <td class="t_c pv">{pv}</td>';
row_template +='    <td class="t_c comments">{comments}</td>';
row_template +='    <td class="t_c score">{score}</td>';
row_template +='	<td class="t_c" ><a href="?app=system&controller=administrator&action=stat&userid={userid}">查看</a> &nbsp;<a href="javascript:;" onclick="exportdata(false,{userid})">导出</a></td>';
row_template +='</tr>';

var stat = {
	'posts'		: 0,
	'pv'		: 0,
	'comments'	: 0,
	'score'		: 0
};
var tableApp = new ct.table('#item_list', {
	rowIdPrefix : 'row_',
	pageField : 'page',
	rowCallback     : 'init_row_event',
	jsonLoaded : function(json){
		$('#last_stat_time').html(json.last_stat_time);
		$.each(json.data, function(i,item) {
			stat.posts		+= parseInt(item.posts) || 0;
			stat.pv			+= parseInt(item.pv) || 0;
			stat.comments	+= parseInt(item.comments) || 0;
			stat.score		+= parseInt(item.score) || 0;
		});
		$('#item_list').find('td.posts').html(stat.posts);
		$('#item_list').find('td.pv').html(stat.pv);
		$('#item_list').find('td.comments').html(stat.comments);
		$('#item_list').find('td.score').html(stat.score);
	},
	template : row_template,
	baseUrl  : '?app=<?=$app?>&controller=<?=$controller?>&action=stat'
});

function init_row_event(id, tr)
{
	if(tr[0].id=='row_0')
	{
		tr.find('td:last,td:first').html('');
	}
}

tableApp.load($('#condition'));
function restat(){
	var h = $('#condition').find('input[name="restat"]:hidden');
	!h[0] && $('#condition').append('<input type="hidden" name="restat" value="1"/>');
	tableApp.load($('#condition'));
}
function stat(){
	$('#condition').find('input[name="restat"]').remove();
	tableApp.load($('#condition'));
}
function exportdata(ignore,userid)
{
	var url = '?app=system&controller=stat_examine&action=export&'+$('#condition').serialize();
	if(!ignore){
		if(!userid){
			userid = tableApp.checkedIds();
			if(userid.length == 0){
				ct.msg('请选择要操作的记录！');
				return false;
			}
		}
		url += '&userid='+userid;
	}
	window.open(url);
	return false;
}

</script>
<?php $this->display('footer', 'system');?>