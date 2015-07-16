<div class="pop_box_area" style="height:465px; overflow:hidden;">
  <div class="operation_area layout">
    <div class="search_icon" style="width: 280px;">
      <form method="POST" id="system_related_search">
        <input type="text" name="keywords" id="keywords" size="10" title="请输入关键词" value="<?=$keywords?>">
        <?=element::category('catid', 'catid', $catid)?>
        <input type="button" id="search" value="搜索" class="button_style_1" style="*margin-top: -7px;width:60px"/>
      </form>
    </div>
  </div>
  <div class="attachment_lib">
    <div class="box_10 f_r" style="width:360px;">
      <h3>已选(<span id="checked_count"><?=count($contents)?></span>)</h3>
      <div class="h_350">
        <ul id="list" class="txt_list">
        	<?foreach ($contents as $r): ?>
        	<li page="1">
        	<input type="checkbox" value="<?=$r['contentid']?>" checked class="checkbox_style">国内
        	<a href="<?$r['url']?>" target="_blank"><?=str_cut($r['title'], 33)?></a>
        	<span class="date">2010-01-29</span>
        	</li>
        	<?endforeach;?>
        </ul>
      </div>
    </div>
    <div class="box_10 f_l" style="width:360px;">
      <h3 class="layout">
      <span class="f_l">待选(<span id="count">0</span>/<span id="total">0</span>)</span>
      </h3>
      <div id="scroll_div" class="h_350">
        <ul class="txt_list">
        </ul>
      </div>
    </div>
    <div class="f_l" style="padding:200px 0 0 0;"><img src="images/move_left.gif" alt="" title="" height="16" width="24"/></div>
    <div class="clear"></div>
  </div>
  <div class="btn_area t_c">
    <input id="enter" type="button" value="确定" class="button_style_1"/>
    <input id="cancel" type="button" value="取消" class="button_style_1"/>
  </div>
</div>
<script type="text/javascript">
var total = 0;
var count = 0;
var post = {page: 1};
var loaded = 0;
$(function(){
	$('#scroll_div').scroll(function(){
		var o = $('#scroll_div');
		if (o.scrollTop()+o.height() > o.get(0).scrollHeight - 90)
		{
			if (window.loading ) return;
			if (window.show_more_lock) return;
			if (count >= total) return;
	
			window.laoding = true;
			window.show_more_lock = true;
			post.page++;
			if(loaded) load();
			
		}
	});
	$('#search').click(function (){
		post.keywords = $('#keywords').val();
		post.catid = $('#catid').val();
		post.page = 1;
		$('#scroll_div>ul').empty();
		load();
	});
	$('#enter').click(save);
	$('#cancel').click(cancel);
	load();
});
function load()
{
	$.post('?app=magazine&controller=content&action=getArticle', post, function(response) {
		if(response.state)
		{
			loaded = 1;
			total = response.total;
			count += 15;
			var ul = $('#scroll_div>ul');
			for(k in response.data) {
				var r = response.data[k];
				if(!r.title) continue;
				var li = '\
				<li page="'+post.page+'">\
					<input type="checkbox" class="checkbox_style" value="'+r.contentid+'">'+r.catname+'\
					<a target="_blank" href="'+r.url+'">'+ r.title.substr(0, 17) +'</a>\
					<span class="date">'+r.time+'</span>\
				</li>';
				ul.append(li);
			}
			$('#scroll_div>ul>li[page='+post.page+']>input:checkbox').click(addLi);
			if(post.page == 1) cancelLi();
		}
		$('#total').text(total);
		$('#count').text(ul.find('li').length);
		setTimeout(function(){window.show_more_lock = false;},10);
		window.loading = false;
	}, 'json');
	loaded = 0;
}
function addLi()
{
	if(this.checked) {
		var li = $(this).parent().clone();
		$('#list').append(li);
		li.find('input:checkbox').attr('checked', 1);
		cancelLi(li);
	}else{
		$('#list>li>input[value='+this.value+']').parent().remove();
	}
	liCount();
}
function cancelLi(li)
{
	if(!li) {
		var li = $('#list>li');
		li.each(function (i, e){
			var v = $(e).find('input').val();
			$('#scroll_div>ul>li>input[value='+v+']').attr('checked', 1);
		});
	}
	li.find('input').click(function (){
		if(!this.checked) {
			$('#scroll_div>ul>li>input[value='+this.value+']').attr('checked', 0);
			$(this).parent().remove();
			liCount();
		}
	});
}
function save()
{
	var ids = '';
	$('#list>li>input').each(function (i, e){
		ids += e.value+',';
	});
	if(ids) {
		ids = ids.substr(0, ids.length - 1);
	}
	$.post('?app=magazine&controller=content&action=saveRelate', {ids: ids, pid: '<?=$_GET['pid']?>'}, function (data){
		if(data == 1) {
			$.growlUI('保存成功');
			$('span.close').click();
			tableApp.load();
		}else{
			ct.error('保存失败');
		}
	});
}
function cancel()
{
	$('span.close').click();
}
function liCount()
{
	$('#checked_count').text($('#list>li').length);
}
</script>
</body></html>