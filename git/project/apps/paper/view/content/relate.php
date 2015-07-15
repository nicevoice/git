<div class="pop_box_area" style="height:465px; overflow:hidden;">
  <div class="operation_area layout">
    <div class="search_icon" style="width: 300px;">
      <form method="POST" id="system_related_search">
        <input type="text" name="keywords" id="keywords" size="10" title="请输入关键词" value="<?=$keywords?>">
        <?=element::category('catid', 'catid', $catid)?>
        <input type="button" id="search" value="搜索" class="button_style_1" style="width:60px"/>
      </form>
    </div>
  </div>
  <div class="attachment_lib">
    <div class="box_10" style="width:500px;">
      <h3 class="layout">
      <span class="f_l">待选(<span id="count">0</span>/<span id="total">0</span>)</span>
      </h3>
      <div id="scroll_div" class="h_350">
        <ul class="txt_list">
        </ul>
      </div>
    </div>
  </div>
  <div class="btn_area t_c">
    <input id="cancel" type="button" value="取消" class="button_style_1"/>
  </div>
</div>
<script type="text/javascript">
var total = 0;
var count = 0;
var post = {page: 1};
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
			load();
		}
	});
	$('#search').click(function (){
		post.keywords = $('#keywords').val();
		post.catid = $('#catid').val();
		post.page = 1;
		 $('#scroll_div>ul').empty();
		load();
	});
	$('#cancel').click(cancel);
	load();
});
function load()
{
	$.post('?app=paper&controller=content&action=getArticle', post, function(response) {
		if(response.state)
		{
			total = response.total;
			count += 20;
			var ul = $('#scroll_div>ul');
			for(k in response.data) {
				var r = response.data[k];
				if(!r.title) continue;
				
				var li = '\
				<li page="'+post.page+'">\
					<a target="_blank" href="'+r.url+'"><img src="images/view.gif"/></a>\
					<a onclick="select(this, '+r.contentid+')" href="javascript:;">' + r.title +'</a>\
					<span class="date">'+r.published+'</span>\
				</li>';
				ul.append(li);
				$('#total').text(total);
				$('#count').text(ul.find('li').length);
			}
		}
		setTimeout(function(){window.show_more_lock = false;},10);
		window.loading = false;
	}, 'json');
}
//标注
function select(a, contentid)
{
	var title = $(a).text();
	var li = $('#store li.selected');
	var k = $('#store li').index(li) + 1;
	if(!title) title = '';
	var cutTitle = title.substr(0, 14);
	cutTitle = k + ': 	' + cutTitle;
	title = k + ': 	' + title;
	li.find('input[name=contentid[]]').val(contentid).end().find('span').html(cutTitle).attr('title', title);
	$('div.mark[rel='+(k-1)+']').html(title);
	area.saveMap(li);
	cancel();
}
function cancel()
{
	$('span.close').click();
	$('#store li').removeClass('selected');
	area.emptyArea();
}
</script>
</body></html>
<style>
#scroll_div li {
	height: 24px;
	line-height: 24px;
}
</style>