<?php $this->display('header','system');?>
<link href="<?=IMG_URL?>js/lib/tree/style.css" rel="stylesheet" type="text/css"/>
<script src="<?=IMG_URL?>js/lib/cmstop.tree.js" type="text/javascript"></script>
<!--tablesorter-->
<link rel="stylesheet" type="text/css" href="<?=IMG_URL?>js/lib/tablesorter/style.css" />
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.tablesorter.js"></script>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.table.js"></script>
<!--pagination-->
<link rel="stylesheet" type="text/css" href="<?=IMG_URL?>js/lib/pagination/style.css" />
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.pagination.js"></script>
<script src="<?=IMG_URL?>js/lib/cmstop.list.js" type="text/javascript"></script>
<link href="<?=IMG_URL?>js/lib/list/style.css" rel="stylesheet" type="text/css" />
<style type="text/css">
/* 文章标题前的图片 */
td div.icon {background: url(<?php echo IMG_URL?>js/lib/dropdown/bg.gif) no-repeat scroll 0 -50px transparent;	margin-right: 3px;	width: 16px;height: 20px;float: left;}
td div.picture {background-position: 0 -336px;}
td div.link {background-position: 0 -100px;}
td div.video {background-position: 0 -125px;}
td div.activity {background-position: 0 -252px;}
td div.vote {background-position: 0 -171px;}
td div.interview {background-position: 0 -147px;}
td div.survey {background-position: 0 -309px;}
td div.special {background-position: 0 -280px;}
</style>
<div class="bk_10"></div>
<div class="tag_1" style="margin-bottom:0px">
	<ul class="tag_list" id="datatype" style="margin-left:145px">
		<li><a href="?app=system&controller=rank&action=index">综合排行</a></li>
	    <li><a href="?app=system&controller=rank&action=rank_pv">点击排行</a></li>
	    <li><a href="?app=system&controller=rank&action=rank_comments">评论排行</a></li>
	    <li><a href="?app=system&controller=rank&action=rank_digg">Digg排行</a></li>
	    <li><a href="?app=system&controller=rank&action=rank_mood" class="s_3">心情排行</a></li>
	</ul>
</div>

<div class="bg_1" id="tree_in">
  <div class="w_160 box_6 mar_r_8 f_l" style="position:relative;width:155px;background: #F9FCFD;">
    <h3><span class="dis_b b" onclick="">栏目列表</span></h3>
    <div id="category" style="position:absolute;z-index:3;"></div>
  </div>
  <div class="f_l" style="padding-left:10px;width:78%;margin-top:5px">
<form id="search">
<table>
<tr><td width="75">
<input type="hidden" name="catid" value="<?=$catid?>">
<select id="model" name="modelid" style="width:80px;" marginTop="1" marginLeft="0">
    <option value="" selected="selected">所有模型</option>
    <?php foreach ($models as $m): ?>
  		  <option value="<?=$m['modelid']?>"><?=$m['name']?></option>
  	<?php endforeach;?>
</select>
</td><td width="75">
<select id="editor" name="editor" style="width:80px;" marginTop="1" marginLeft="0">
    <option value="" selected="selected">所有编辑</option>
    <?php foreach ($editor as $e): ?>
  		  <option value="<?=$e['userid']?>"><?php echo $e['name']?$e['name']:table('member',$e['userid'],'username')?></option>
  	<?php endforeach;?>
</select>
</td><td>
从
<input type="text" name="created_min" id="created_min" class="input_calendar" value="<?=$created_min?>" size="20"/> 到 
<input type="text" name="created_max" id="created_max" class="input_calendar" value="<?=$created_max?>" size="20"/>
</td>
<td>&nbsp;&nbsp;<input type="button" value="查询" class="button_style_2" onclick="load();"/></td>
</tr>
</table>
</form>
<div class="bk_8"></div>
<div id="proids">
    <dl class="proids" id="mood"><dt>心情：</dt><dd>
    <a href="0" class="checked">全部</a>
    <?php $mood = table('mood');
    	foreach ($mood as $m):?>
    		<a href="<?=$m[moodid]?>"><?=$m[name]?></a>
    <?php endforeach;?>
 	</dd></dl>
</div>
<div class="tag_list_1 pad_8 layout mar_l_8" id="bytime_list">
	<a href="all" id="all" class="s_5">全部</a>
	<a href="today">今天</a>
	<a href="yesterday">昨天</a>
	<a href="last week">本周</a>
	<a href="last month">本月</a>
	<div class="clear"></div>
</div>
<table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list mar_t_8">
<thead>
  <tr>
    <th class="bdr_3">标题</th>
    <th width="60">总计</th>
    <th width="100">录入</th>
    <th width="120">录入时间</th>
  </tr>
</thead>
<tbody id="list_body">
</tbody>
</table>
<div class="table_foot" style="width:98%">
  <div id="pagination" class="pagination f_r"></div>
</div>
  </div>
  <div class="clear"></div>
</div>
<script type="text/javascript">
var row_template = '<tr id="row_{contentid}">';
row_template +='	<td><a title="查看内容" href="javascript: ct.assoc.open(\'?app={modelalias}&controller={modelalias}&action=view&contentid={contentid}\', \'newtab\');"><div class="icon {modelalias}"></div></a><a href="{url}" target="_blank">{title}</a></td>';
row_template +='	<td class="t_r">{mood}</td>';
row_template +='	<td class="t_c"><a href="javascript: url.member({createdby});">{createdbyname}</a></td>';
row_template +='	<td class="t_c">{created}</td>';
row_template +='</tr>';

var tableApp = new ct.table('#item_list', {
    rowIdPrefix : 'row_',
    rightMenuId : null,
    pageField : 'page',
    pageSize : 15,
    dblclickHandler : null,
    rowCallback     : 'init_row_event',
    template : row_template,
    baseUrl  : '?app=system&controller=rank&action=page'
});

function init_row_event(id, tr)
{
    tr.find('a.title_list').attrTips('tips');
}

var catid = 0,modelid = 0,userid = 0;
$('#category').tree({
	url:"?app=system&controller=rank&action=cate&catid=%s",
	paramId : 'catid',
	paramHaschild:"hasChildren",
	renderTxt:function(div, id, item){
		return $('<span>'+item.name+'</span>');
	},
	active : function(div, id, item){
		catid = id;
		load();
	},
	prepared:function(){
		var t = this;
		$.getJSON('?app=system&controller=rank&action=category_path&catid='+<?=$catid?>, function(path){
			t.open(path);
		});
	}
});

$(function(){
	$('#model').selectlist({alt:'所有模型'});
	$('#editor').selectlist({alt:'所有编辑'});
	$('input.input_calendar').DatePicker({'format':'yyyy-MM-dd HH:mm:ss'});
	$('#bytime_list a').click(function(){
		$('#bytime_list a').removeClass('s_5');
		$(this).addClass('s_5');
		load($(this).attr('href'));
		return false;
	});
	$('#mood a').click(function(){
		$('#mood a').removeClass('checked');
		$(this).addClass('checked');
		$('#item_list thead th:eq(1)').html(this.innerHTML);
		load();
		return false;
	});
})

function load()
{
	var modelid = $('[name="modelid"]').val();
	var userid = $('[name="editor"]').val();
	var created_min = $('#created_min').val();
	var created_max = $('#created_max').val();
	var mood = $('#mood a.checked').attr('href');
	var where = [];
	where.push('datatype=mood');
	where.push('mood='+mood);
	if(catid) where.push('catid='+catid);
	if(modelid) where.push('modelid='+modelid);
	if(userid) where.push('userid='+userid);
	if(arguments[0])
	{
		where.push('date='+arguments[0]);
	}
	else
	{
		if(created_min) where.push('created_min='+created_min);
		if(created_max) where.push('created_max='+created_max);
	}
	tableApp.load(where.join('&'));
	return false;
}
</script>
<?php $this->display('footer');?>