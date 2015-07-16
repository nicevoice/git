<?php $this->display('header', 'system');?>
<style type="text/css">
.selectDiv {float: left;margin: 1px 8px 0 0;}
</style>
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
<!--list-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/list/style.css"/>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.list.js"></script>
<!-- 时间选择器 -->
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.datepicker.js"></script>
<link href="<?=IMG_URL?>js/lib/datepicker/style.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="apps/system/js/cron.js"></script>
<div class="bk_8"></div>
<div class="table_head">
  <div class="f_r" style="padding-top: 3px;">当前时间：<span id="serverTime"> </span></div>

  <div class="selectDiv">
  <select name="disabled" id="disabled" style="width:50px;">
      <option value="-1" selected>状态</option>
      <option value="0">启用</option>
      <option value="1">禁用</option>
  </select>
  </div>

  <input type="button" id="add" value="添加" class="button_style_2 f_l""/>
  <input type="button" id="log" value="日志" tips="任务运行日志" class="button_style_2 f_l"/>
  <input type="button" id="interval" tips="服务器上每一分钟检测一次可运行任务，点击此按钮可立即检测" value="立即检测" class="button_style_4 f_l"/>
</div>
<div class="bk_8"></div>
<table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list" style="margin-left:6px;">
  <thead>
    <tr>
		<th width="30" class="t_l bdr_3">
			<input type="checkbox" id="check_all" class="checkbox_style"/>
		</th>
		<th>名称</th>
		<th width="90" class="header ajaxer">
			<em name="pop_layer_id"></em><div name="mode">任务模式</div>
		</th>
		<th width="90" class="header ajaxer">
			<em name="pop_layer_id"></em><div name="type">任务类型</div>
		</th>
		<th width="130" class="header ajaxer">
			<em name="pop_layer_id"></em><div name="lastrun">上次运行时间</div>
		</th>
		<th width="130" class="header ajaxer tipClass" tips="根据定时检测时间长短会有少许误差">
			<em name="pop_layer_id"></em><div name="nextrun">下次运行时间</div>
		</th>
		<th width="60" class="header ajaxer tipClass" tips="失效表示过期或者<br/>一年内没有机会运行">
			<em name="pop_layer_id"></em><div name="disabled">状态</div>
		</th>
		<th width="120">管理操作</th>
    </tr>
  </thead>
  <tbody id="list_body">
  </tbody>
</table>
<!--右键菜单-->
<ul id="right_menu" class="contextMenu">
   <li class="edit"><a href="#cron.save">编辑</a></li>
   <li class="log"><a href="#cron.log">日志</a></li>
   <li class="copy"><a href="#cron.copy">复制</a></li>
   <li class="run"><a href="#cron.run">运行</a></li>
   <li class="del"><a href="#cron.del">删除</a></li>
</ul>
<div class="table_foot">
	<div id="pagination" class="pagination f_r"></div>
	<div class="f_r"> 共<span id="pagetotal">{total}</span>条&nbsp;每页
	<input type="text" value="<?=$size?>" name="pagesize" size="2" id="pagesize"/> 条&nbsp;&nbsp;</div>
	<p class="f_l">
		<input type="button" onclick="cron.del()" value="删除" class="button_style_1"/>
	</p>
</div>
<script type="text/javascript">
var row_template = '<tr id="row_{cronid}" status="{disabled}">\
					<td><input type="checkbox" class="checkbox_style" id="chk_row_{cronid}" value="{cronid}" /></td>\
                	<td class="t_l tipClass" tips="{tips}"><a class="edit" href="javascript:;">{name}</a></td>\
                	<td class="t_l">{modeStr}</td>\
                	<td class="t_c">{type}</td>\
                	<td class="t_c">&nbsp;{lastrun}</td>\
                	<td class="t_c">&nbsp;{nextrun}</td>\
                	<td class="t_c"><img src="images/execute.gif" title="运行中，点击可禁用" width="16" height="16" class="hand change"/></td>\
                	<td class="t_c">\
                	<img src="images/edit.gif" title="编辑" width="16" height="16" class="hand edit"/>\
                	<img src="images/dialog.gif" title="查看运行日志" width="16" height="16" class="hand log"/>\
                	<img src="images/page_white_copy.png" title="复制为新" width="16" height="16" class="hand copy"/>\
                	<img src="images/refresh.gif" title="立即执行" width="16" height="16" class="hand run"/>\
                	<img title="删除本行" src="images/delete.gif" alt="删除" width="16" height="16" class="hand delete" />\
                	</td>\
                </tr>';

var tableApp = new ct.table('#item_list', {
    rowIdPrefix : 'row_',
    rightMenuId : 'right_menu',
    pageField : 'page',
    pageSize : <?=$size?>,
    dblclickHandler : 'cron.save',
    rowCallback     : 'init_row_event',
    template : row_template,
    baseUrl  : '?app=<?=$app?>&controller=<?=$controller?>&action=page'
});

$(function (){
	$('#log').click(cron.log);
	$('#add').click(cron.save);
	$('#interval').click(cron.interval);
	$('#pagesize').change(function(){
		tableApp.setPageSize(this.value);
		ct.setCookie('cronPageSize', this.value, {expires:30});
		tableApp.load();
	});
    $('#disabled').selectlist().bind('changed',function(e, t){
        tableApp.load('disabled=' + t.checked[0]);
    });
	//显示服务器时间
	var date = new Date(<?=TIME?> * 1000 + 500);
	var tim = setInterval(function (){
		date.setTime(date.getTime() + 1000);
		$('#serverTime').text(date.toLocaleString());
	},1000);
	$('div.table_head input[tips], th.tipClass').attrTips('tips');
})
function init_row_event(id, tr)
{
	tr.find('img.edit,a.edit').click(function(){
		cron.save(id);
	});
	tr.find('img.delete').click(function(){
		cron.del(id);
	});
	tr.find('img.log').click(function(){
		cron.log(id);
	});
	tr.find('img.run').click(function(){
		cron.run(id);
	});
	tr.find('img.copy').click(function(){
		cron.copy(id);
	});
	tr.find('td.tipClass').attrTips('tips');

    if (tr.attr('status') == '失效') {
        tr.find('img.change').attr({
            'src': 'images/stoped.gif',
            'title': '已禁用，点击可启用'
        })
    }
    tr.find('img.change').click(function(){
        cron.change(id, tr.attr('status') == '正常');
    });
}
function json_loaded(json) {
	$('#pagetotal').html(json.total);
}
tableApp.load();
</script>
<style>
#cmstopAttrHiddenDivtips_orange label{
	float: left;
	width: 60px;
}
</style>
<?php $this->display('footer', 'system');