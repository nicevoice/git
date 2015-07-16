<?php $this->display('header', 'system');?>

<div class="pop_box_area" style="height:465px; overflow:hidden;">
  <div class="operation_area layout">
    <div class="search_icon">
      <form method="GET" id="system_related_picture" action="<?=$url?>">
        <input type="text" name="keywords" id="keywords" size="10" title="请输入关键词" value="<?=$keywords?>">
        <?=element::category('catid', 'catid', $catid)?>
        <input type="submit" value="搜索" class="button_style_1" style="width:60px"/>
      </form>
    </div>
  </div>
 
  <div class="attachment_lib">
   
    <div class="box_10 f_l" style="width:420px;">
      <h3 class="layout"><span class="f_r"><img src="images/dx.gif" title="按时间 新→旧 排序" height="20" width="16" onclick="related_sort('desc')" class="hand"/>　<img src="images/zx.gif" title="按时间 旧→新 排序" height="20" width="16" onclick="related_sort('asc')" class="hand" /></span><span class="f_l">待选(<span id="count">0</span>/<span id="total">0</span>)</span></h3>
      <div id="scroll_div" class="h_350">
        <ul id="data" class="txt_list">
        </ul>
      </div>
    </div>
    <div class="clear"></div>
  </div>
  <div class="btn_area t_c">
    <input type="button" value="确定" class="button_style_1" onclick="picture_ok()"/>
    <input type="button" value="取消" class="button_style_1" onclick="related_cancel()"/>
  </div>
</div>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.cookie.js"></script>
<script type="text/javascript" src="js/related.js"></script>
<script type="text/javascript">
var related = '';
var page = 1;
var pagesize = 20;
var count = 0;
var total = 0;
var rid = 0;
var checked_count = 0;
var loading = false;
var show_more_lock = false;

$('#scroll_div').scroll(function(){
	var o = $('#scroll_div');
	if (o.scrollTop()+o.height() > o.get(0).scrollHeight - 90)
	{
		if (window.loading ) return;
		if (window.show_more_lock) return;
		if (count >= total) return;

		window.laoding = true;
		window.show_more_lock = true;
		page++;
		$.getJSON('<?=$url?>', 'catid='+$('#catid').val()+'&modelid='+$('#modelid').val()+'&keywords='+$('#keywords').val()+'&page='+page+'&pagesize='+pagesize, function(response) {
			if(response.state)
			{
				related_picture(response.data);
				if (sort_mode != '') related_sort(sort_mode);
				count += response.data.length;
				$('#count').html(count);
			}
			setTimeout(function(){ window.show_more_lock = false;},10);
			window.loading = false;
		});
	}
});

async_form('system_related_picture', 'related_picture_ok');
$('#system_related_picture').submit();
</script>
</body></html>