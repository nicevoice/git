<?php $this->display('header', 'system');?>
<div class="bk_8"></div>
<form id="article_setting" method="POST" action="?app=article&controller=setting&action=index">
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
	<caption>文章设置</caption>
	<tr>
		<th width="120">图片水印：</th>
		<td><input type="radio" name="setting[watermark]" value="1" class="radio" <?php if ($setting['watermark'] == 1) echo 'checked';?>/> 启用 &nbsp; <input type="radio" name="setting[watermark]" value="0" class="radio" <?php if ($setting['watermark'] == 0) echo 'checked';?>/> 禁用</td>
	</tr>
	<tr>
		<th width="120">缩略图：</th>
		<td>宽 <input type="text" name="setting[thumb_width]" value="<?=$setting['thumb_width']?>" size="4"/>px ~ 高 <input type="text" name="setting[thumb_height]" value="<?=$setting['thumb_height']?>" size="4"/>px
		</td>
	</tr>
	<tr>
		<th width="120">权重默认值：</th>
		<td><input type="text" name="setting[weight]" value="<?=$setting['weight']?>" size="3"/>
		</td>
	</tr>
	<tr>
		<th width="120">来源默认值：</th>
		<td><input type="text" name="setting[source]" value="<?=$setting['source']?>" size="20"/>
		</td>
	</tr>
	<tr>
	    <th>&nbsp;</th>
	    <td  class="t_c"><input type="submit" class="button_style_2" value="保存" /></td>
	</tr>
</table>
</form>
<script type="text/javascript">
$(function(){
	$('#article_setting').ajaxForm(function (response) {
		ct.tips('保存成功');
	});
	$('.tips').attrTips('tips', 'tips_green', 200, 'top');
});
</script>
<?php $this->display('footer', 'system');?>