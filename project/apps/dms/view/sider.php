<div class="dms_nav_bg"></div>
<div class="dms_search_bg"></div>
<div class="dms_nav">
	<span class="add_button" id="dms_add_button"></span>
	<div class="add_type" id="dms_add_type" style="display:none">
		<span class="add_article" onclick="ct.assoc.open('?app=dms&controller=article&action=add', 'newtab');"></span>
		<span class="add_picture" onclick="ct.assoc.open('?app=dms&controller=picture&action=add', 'newtab');"></span>
		<span class="add_picture_group" onclick="ct.assoc.open('?app=dms&controller=picture_group&action=add', 'newtab');"></span>
		<!--<span class="add_video"></span>-->
		<span class="add_attachment" onclick="ct.assoc.open('?app=dms&controller=attachment&action=add', 'newtab');"></span>
		<!--<span class="add_other"></span>-->
	</div>
	<a class="<?=$controller=='article'?'cur':''?> article" href="?app=dms&controller=article&action=index">文章</a>
	<a class="<?=$controller=='picture'?'cur':''?> picture" href="?app=dms&controller=picture&action=index">图片</a>
	<a class="<?=$controller=='picture_group'?'cur':''?> picture_group" href="?app=dms&controller=picture_group&action=index">组图</a>
	<!--<a class="<?=$controller=='video'?'cur':''?> video">视频</a>-->
	<a class="<?=$controller=='attachment'?'cur':''?> attachment" href="?app=dms&controller=attachment&action=index">附件</a>
	<!--<a class="<?=$controller=='other'?'cur':''?> other">其他</a>-->
	<a class="<?=$controller=='setting'?'cur':''?> setting" href="?app=dms&controller=setting&action=index">设置</a>
	<a class="<?=$controller=='log'?'cur':''?> log" href="?app=dms&controller=log&action=index">日志</a>
	<a class="<?=$controller=='model'?'cur':''?> model" href="?app=dms&controller=model&action=index">模型</a>
	<a class="<?=$controller=='app'?'cur':''?> app" href="?app=dms&controller=app&action=index">应用</a>
	<a class="<?=$controller=='priv'?'cur':''?> priv" href="?app=dms&controller=priv&action=index">授权</a>
	<a class="<?=$controller=='tag'?'cur':''?> tag" href="?app=dms&controller=tag&action=index">标签</a>
	<!--<a class="<?=$controller=='server'?'cur':''?> server" href="?app=dms&controller=server&action=index">存储</a>-->
	<a class="<?=$controller=='quote'?'cur':''?> quote" href="?app=dms&controller=quote&action=index">引用</a>
</div>
<script type="text/javascript">
$(document).ready(function() {
	var dms_add_type = $('#dms_add_type');
	$('#dms_add_button').bind('mouseover', function() {
		if (dms_add_type.css('display') == 'none') {
			dms_add_type.show();
			$('body').bind('mouseover', function(obj) {
				if (($(obj.target).parents('.add_type').length == 0 && !$(obj.target).hasClass('add_type')) && ($(obj.target).parents('.add_button').length == 0 && !$(obj.target).hasClass('add_button'))) {
					dms_add_type.hide();
					$('body').unbind('mouseover');
				}
			});
		}
	})
	$('#dms_framework').height();
});
</script>