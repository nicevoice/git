<style>
.tw_box {line-height:20px; height:auto; padding:4px; margin:6px;list-style:none;}
.tw_box .head {height:20px; padding-left:6px; width:470px;}
.tw_box .head li {float:left;}
.tw_box .head li.icon {background:url(<?=IMG_URL?>js/lib/dropdown/bg.gif) no-repeat scroll 0 -50px transparent; margin:3px 2px 0 0; width:16px; height:16px;}
.tw_box .head .action {float:right; margin-right:4px; cursor:pointer; background:url(<?php echo IMG_URL?>js/lib/dropdown/bg.gif) no-repeat scroll 0 -25px transparent; margin:3px 2px 0 0; width:16px; height:16px;}
.tw_box .boxlist {height:auto; line-height:20px; padding:4px; margin:6px;}
.tw_box .text {width:100%; padding:3px; margin:-4px -2px -7px -4px; line-height:20px; overflow: auto; height: 72px; width:470px; font-size:14px;}
.tw_box .count {height:20px; padding-left:6px; width:470px;}
.tw_box .count li {color:#077AC7; float:right;}
.tw_box .count li.pic {float:left; border:none; cursor:pointer;}
.picList {border:1px solid #077AC7; width:452px; padding:4px; margin-left:15px;list-style:none; background:#E6F8F9; padding-left:12px; overflow:hidden; display:none;}
.picList .imgList {height:90px; width:10000px;}
.imgList li {margin:2px 6px 0 0;width:82px;}
.imgList li a {border:1px solid #CAE2F6; background:white; height:82px; line-height:80px; width:82px; text-align:center; vertical-align: middle; display: table-cell; outline: none;}
.imgList li a.on {border:1px solid green;}
.imgList a img {max-height: 82px; max-width: 80px; cursor:pointer;}
</style>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/jquery.jcarousellite.js"></script>
<form name="forward" id="forward" method="POST" action="?app=system&controller=tweets&action=forward" class="validator">
<div>
	<?php foreach ($list as $k => $item) : ?>
	<div class="tw_box">
		<ul class="head">
			<li class="icon"></li>
			<li><a href="<?=$item['url']?>" target="_blank"><?=$item['title']?></a></li>
		</ul>
		<ul class="boxlist">
			<textarea class="text" name="text[]"><?=$item['text']?></textarea>
			<input type="hidden" name="pic[]" id="pic_<?=$item['contentid']?>" value="" />
			<input type="hidden" name="url[]" id="url_<?=$item['contentid']?>" value="<?=$item['url']?>" />
		</ul>
		<ul class="count" style="clear:both;">
			<li class="pic" rel="<?=$item['contentid']?>"><img src="images/ico_pic.gif" align="absmiddle"/> <span>转发图片</span> </li>
			<li>您还可以输入 <span class="num">0</span> 字</li>
		</ul>
	</div>
	<div class="picList">
		<div class="imgList"></div>
		<div class="imgFoot">
			<a href="javascript:void(0);" class="cancel">取消转发图片</a>
			<span style="float:right;margin-right:6px;">
				<a href="javascript:void(0);" class="prev"> 上一张 </a>&nbsp;&nbsp;
				<a href="javascript:void(0);" class="next"> 下一张 </a>
			</span>		
		</div>
	</div>
	<?php endforeach; ?>
<div>
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
  <?php foreach ($userlist as $key => $user): ?>
  <tr>
    <th width="80"> <?=$key?> ：</th>
    <td>
    <?php foreach ($user as $k => $u): ?>
    <label><input type="checkbox" name="users[]" value="<?=$u['tweetid']?>" <?php if (in_array($u['tweetid'], $check)) :?>checked<?php endif;?>/> <?=$u['name']?></label>
    <?php endforeach; ?>
    </td>
  </tr>
  <?php endforeach; ?>
</table>
</form>
<script>
$.validate.setConfigs({
    xmlPath:'<?=ADMIN_URL?>apps/<?=$app?>/validators/tweets/'
});
(function($) {
	$('#forward').submit(function(){
		var cnt = 0;
		$(":checkbox[name^='user']").each(function(i,n){
			if ($(this).attr('checked')) {
				cnt++;
			}
		})
		if (!cnt) {
			ct.error('请至少选择一个转发账户');
			return false;
		}
	});
	$.each($('.text'), function(i,n){count($(this))})
	$('.text').keyup(function(){
		count($(this));
	});
	function count(obj, flag)
	{
		var num = obj.parent().next().find('.num');
		var max = 130;
		var l = max - obj.val().length
		if (flag == undefined) {
			num.html(l <= 0 ? 0 : l);
			l <= 0 && num.addClass('c_red');
		}
		return l;
	}
	$('#forward').submit(function(){
		$.each($('.text'), function(){
			if (count($(this), true) < 0)
				return false;
		})
		return false;
	});
	
	$('.pic').click(function(){
		var id = $(this).attr('rel');
		var that = $(this);
		var picList = that.parent().parent().next();
		
		$('.cancel').click(function(){
			$('#pic_'+id).val('');
			picList.find('.imgList li a').removeClass("on");
			$(this).parent().parent().hide();
			that.children('span').html('转发图片');
		});
		
		if (picList.find('.imgList').html() == '') {
			$.get('?app=system&controller=tweets&action=forward_pic', {id:id}, function(response){
				var str = '<ul>';
				
				if (response.state) {
					for (var i = 0; i < response.data.length; i++) {
						str += '<li><a><img src="'+response.data[i]+'" /></a></li>';
					}
					str += '</ul>';
					picList.find('.imgList').empty().append(str);
					picList.show();
					that.children('span').html('请选择一张图片转发');
					picList.find('.imgList').jCarouselLite({
					    btnNext: picList.find(".imgFoot .next"),
					    btnPrev: picList.find(".imgFoot .prev"),
					    circular: false,
					    visible: 5,
					    scroll: 1,
					});
					var img = picList.find('.imgList li a');
					var start = picList.find('.imgList li a:eq(0)');
					start.addClass("on");
					$('#pic_'+id).val(start.attr("src"));
					img.click(function(){
						img.removeClass("on");
						$(this).addClass("on");
						$('#pic_'+id).val($(this).children().attr("src"));
					})
				} else {
					ct.alert(response.msg);
					return false;
				}
			}, 'json');
		} else {
			that.children('span').html('请选择一张图片转发');
			picList.show();
		}
		
	});
})(jQuery);
</script>