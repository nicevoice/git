<?php $this->display('header', 'system');?>
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

<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/dropdown/style.css"/>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.dropdown.js"></script>

<script type="text/javascript" src="apps/system/js/content_log.js"></script>
<div class="bk_8"></div>
<div class="tag_1">
	<div class="search_icon search f_r">
	  <form method="GET" name="search_f" id="search_f" action="" onsubmit="tableApp.load($('#search_f'));return false;">
	    <input type="text" name="title" id="title" value="<?=$title?>" size="30" title="请输入标题关键词"/>
	    <a href="javascript:;" onclick="tableApp.load($('#search_f'));return false;" title="搜索">搜索</a><a href="javascript:;" class="more_search" onclick="content_log.search(<?=intval($catid)?>)" title="高级搜索">高级搜索</a>
	  </form>
	</div>
	<div style="padding-left:120px;">
	  <div id="add_data">
	  <select id="changeaction" style="width:100px" marginTop="1" marginLeft="0">
	      <?php foreach (config('content_action') as $act=>$name) {?>
		  <option value="<?=$act?>" <?=$opaction == $act ? 'selected' : ''?>><?=$name?></option>
		  <?php } ?>
	  </select>
	  </div>
	  <ul class="tag_list">
	    <li><a href="javascript: tableApp.load('1=1');" class="s_3">全部</a></li>
	    <li><a href="javascript: tableApp.load('created_min=<?=date('Y-m-d', TIME)?>');">今日</a></li>
	    <li><a href="javascript: tableApp.load('created_min=<?=date('Y-m-d', strtotime('yesterday'))?>&created_max=<?=date('Y-m-d', strtotime('yesterday'))?>');");">昨日</a></li>
	    <li><a href="javascript: tableApp.load('created_min=<?=date('Y-m-d', strtotime('last monday'))?>');">本周</a></li>
	    <li><a href="javascript: tableApp.load('created_min=<?=date('Y-m-01', strtotime('this month'))?>');">本月</a></li>
	  </ul>
	</div>
</div>

<table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list mar_l_8 mar_t_8">
<thead>
  <tr>
    <th class="bdr_3">标题</th>
    <th width="120">操作</th>
    <th width="100">操作人</th>
    <th width="120">操作时间</th>
    <th width="200">IP</th>
  </tr>
</thead>
<tbody id="list_body">
</tbody>
</table>

<div class="table_foot">
  <div id="pagination" class="pagination f_r"></div>
  <p class="f_l">
  <input type="button" value="删除一周前日志" class="button_style_1" style="width:120px" onclick="content_log.del(7)">
  <input type="button" value="删除一月前日志" class="button_style_1" style="width:120px" onclick="content_log.del(30)">
  <input type="button" value="删除所有日志" class="button_style_1" style="width:100px" onclick="content_log.del(0)">
  </p>
</div>

<script type="text/javascript">
var row_template = '<tr id="row_{logid}">';
row_template +='	<td><a href="javascript: ct.assoc.open(\'?app={model}&controller={model}&action=view&contentid={contentid}\', \'newtab\');">{title}</a></td>';
row_template +='	<td>{action_name}({action})</td>';
row_template +='	<td class="t_c"><a href="javascript: url.member({createdby});">{createdbyname}</a></td>';
row_template +='	<td class="t_c">{created}</td>';
row_template +='	<td class="t_c">{ip}({iparea})</td>';
row_template +='</tr>';

var tableApp = new ct.table('#item_list', {
    rowIdPrefix : 'row_',
    rightMenuId : null,
    pageField : 'page',
    pageSize : 15,
    dblclickHandler : null,
    rowCallback     : 'init_row_event',
    template : row_template,
    baseUrl  : '?app=<?=$app?>&controller=<?=$controller?>&action=page&catid=<?=$catid?>&createdby=<?=$createdby?>'
});

function init_row_event(id, tr)
{
    tr.find('a.title_list').attrTips('tips');
}

tableApp.load();

var interval = setInterval(function(){tableApp.load();}, 180000);
window.onunload = function ()
{
	clearInterval(interval);
}

$('#changeaction').dropdown({
	changeaction:{
		onchange:function(action, name){
			tableApp.load('opaction='+action);
		}
	}
});

$('ul.tag_list a').click(function (){
	$('ul.tag_list a').attr('class', '');
	$(this).attr('class', 's_3');
});
</script>
<?php $this->display('footer', 'system');
