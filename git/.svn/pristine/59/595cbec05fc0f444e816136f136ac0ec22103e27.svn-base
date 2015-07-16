<?$this->display('header'); ?>
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/dropdown/style.css"/>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.dropdown.js"></script>
<script type="text/javascript" src="apps/paper/js/paper.js"></script>
<div class="suggest mar_t_10 mar_l_8" style="width: 98%;">
  <h2>友情提示</h2>
  <p>
  	要在前台查看报纸，必须至少发布一期，而要发布一期，必须创建版面且每个版面都要关联好文章。
  </p>
</div>
<div class="mar_l_8">
	<div class="mar_t_10">
		<input type="button" id="add" value="创建报纸" class="button_style_4"/>
		<input type="button" id="access" value="查看前台" class="button_style_4"/>
	</div>
	<div id="paperDiv" class="clearfix"><!-- paper容器 -->
	
	</div>	
</div>
<?php $this->display('footer','system');?>
<script type="text/javascript">
var tpl = '\
		<div rel="{paperid}" class="paperItem">\
			<ul class="paper">\
				<li class="paper_logo">\
					<a href="{url}" target="_BLANK"><img alt="{name}" src="{logo}"/></a>\
				</li>\
				<li><span class="b">报纸名：</span>{name}</li>\
				<li><span class="b">版面数：</span>{pages}版</li>\
				<li><span class="b">总期数：</span>{total_number}期</li>\
				<li>\
					<input type="button" class="manage button_style_1" value="管理"/>\
					<input value="新建期号" type="button" class="newEdition button_style_1" />\
				</li>\
				<li>\
					<input type="button" class="edit button_style_1" value="属性"/>\
					<input type="button" class="delete button_style_1" value="删除"/>\
				</li>\
			</ul>\
		</div>';
$(function (){
	$('#add').click(paper.save);
	$('#access').click(function (){
		window.open('<?=$SETTING['www_root']?>');
	});
	paper.load();
	ct.nav([{text: '扩展'},{text: '报纸'}]);
});
</script>