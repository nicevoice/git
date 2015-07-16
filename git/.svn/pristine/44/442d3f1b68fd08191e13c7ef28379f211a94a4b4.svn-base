<table width="98%" cellpadding="0" cellspacing="0" id="item_log" class="tablesorter table_list mar_t_8" style="margin-left:6px;">
  <thead>
    <tr>
    	<th width="20" class="t_l bdr_3">
			<input type="checkbox" id="check_all1" class="checkbox_style"/>
		</th>
		<th>名称</th>
		<th width="100">运行时间</th>
		<th width="60">状态</th>
		<th tips="return格式如array('state'=>true,'info'=>'更新126条记录');" width="140">信息</th>
    </tr>
  </thead>
  <tbody id="log_body">
  </tbody>
</table>
<div class="table_foot">
	<div id="paginaion1" class="pagination f_r"><?=$link?></div>
	<p style="padding:0;" class="f_l">
	<?if ($cronid): ?>
		<input type="button" onclick="cron.delLog(<?=$cronid?>)" value="清空" class="button_style_1"/>
	<? else: ?>
		<input type="button" onclick="cron.delLog('all')" value="清空" class="button_style_1"/>
	<? endif; ?>
		<input type="button" onclick="cron.delLog('select')" value="删除" class="button_style_1"/>
	</p>
</div>
<script>
var tpl = '<tr id="row1_{logid}">\
					<td><input type="checkbox" class="checkbox_style" id="chk_row1_{logid}" value="{logid}" /></td>\
                	<td class="t_l">{name}</td>\
                	<td class="t_c">{runtime}</td>\
                	<td class="t_c">{runSuccess}</td>\
                	<td class="t_c tipClass" tips="{info}">{infoCut}</td>\
                </tr>';
var logTable = new ct.table('#item_log', {
    rowIdPrefix : 'row1_',
    rightMenuId : 'none',
    pagerId: 'paginaion1',
    jsonLoaded: function (json){
    	$('span#pagetotal1').text(json.total);
    },
    pageField : 'page1',
    pageSize : 10,
    //dblclickHandler : 'cron.save',
    rowCallback     : 'init_event',
    template : tpl,
    baseUrl  : '?app=<?=$app?>&controller=<?=$controller?>&action=logList&cronid=<?=$cronid?>'
});
$(function (){
	$('th[tips]').attrTips('tips');
})
function init_event(id, tr)
{
	tr.find('td.tipClass').attrTips('tips');
}
logTable.load();
</script>