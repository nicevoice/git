<div class="pop_box_area" style="height:auto; overflow:hidden;">
  <div class="operation_area layout">
    <div class="f_r">
      <input type="button" value="手工添加" class="button_style_1" onclick="related_add()"/>
    </div>
    <div class="search_icon">
      <form method="GET" id="system_related_search" action="?app=system&controller=related&action=related">
        <input type="text" name="keywords" id="keywords" size="10" title="请输入关键词" value="<?=$keywords?>" class="w_160">&nbsp;
        <label><input type="checkbox" name="thumb" value="1" />有缩略图</label>
        <?=element::model('modelid', 'modelid', $model)?>&nbsp;
        <?=element::category('catid_related', 'catid', $catid, 1, null, '请选择', true, true)?>
        <input type="submit" value="搜索" class="button_style_1"/>
      </form>
    </div>
  </div>
    <div class="bdr_r f_r" style="width:390px; height:425px;">
      <h3>已选(<span id="checked_count">0</span>)</h3>
      <div class="h_350" style="height:463px;padding:0 10px;">
        <ul id="list" class="txt_list" >
        </ul>
      </div>
    </div>
    <div class="bdr_r f_l" style="width:408px; height:425px;">
      <h3 class="layout"><span class="f_r"><img src="images/dx.gif" title="按时间 新→旧 排序" height="20" width="16" onclick="related_sort('desc')" class="hand"/>　<img src="images/zx.gif" title="按时间 旧→新 排序" height="20" width="16" onclick="related_sort('asc')" class="hand" /></span><span class="f_l">待选(<span id="count">0</span>/<span id="total">0</span>)</span></h3>
      <div id="scroll_div" class="h_350" style="height:403px;margin-left:10px;">
        <ul id="data" class="txt_list">
        </ul>
      </div>
    </div>
    <div class="clear"></div>
</div>
<script type="text/javascript">
$.validate.setConfigs({
	xmlPath:'<?=ADMIN_URL?>apps/<?=$app?>/validators/related/'
});
var related = '';
var page = 1;
var pagesize = 20;
var count = 0;
var total = 0;
var rid = 0;
var checked_count = 0;
var loading = false;
var show_more_lock = false;
var contentid = '<?=$contentid?>';
$('#scroll_div').scroll(function(){
	var o = $('#scroll_div');
	if (o.scrollTop()+o.height() > o.get(0).scrollHeight - 90)
	{
		if (window.loading ) return;
		if (window.show_more_lock) return;
		if (count >= total) return;

		window.loading = true;
		window.show_more_lock = true;
		page++;
		$.getJSON('?app=system&controller=related&action=related', 'catid='+$('#catid_related').val()+'&modelid='+$('#system_related_search').find('#modelid').val()+'&keywords='+encodeURI($('#keywords').val())+'&page='+page+'&pagesize='+pagesize, function(response) {
			if(response.state)
			{
				related_append(response.data);
				if (sort_mode != '') related_sort(sort_mode);
				count += response.data.length;
				$('#count').html(count);
			}
			setTimeout(function(){ window.show_more_lock = false;},10);
			window.loading = false;
		});
	}
});
async_form('system_related_search', 'related_search_ok');
$('#system_related_search').submit();
init_list();
</script>
