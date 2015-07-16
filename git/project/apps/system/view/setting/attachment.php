<?php $this->display('header', 'system');?>
<div class="bk_10"></div>
<form id="setting_edit_attachment" action="?app=system&controller=setting&action=edit" method="POST">
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
	<caption>基本设置</caption>
	<tr>
		<th width="150">允许上传的附件类型：</th>
		<td><input type="text" name="setting[attachexts]" value="<?=$attachexts?>" size="80"/></td>
	</tr>
</table>
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
	<caption>缩略图</caption>
	<tr>
		<th width="150">缩略图：</th>
		<td>
		<input type="radio" name="setting[thumb_enabled]" value="1" class="radio" <?php if ($thumb_enabled == 1) echo 'checked';?>/> 启用 &nbsp; <input type="radio" name="setting[thumb_enabled]" value="0" class="radio" <?php if ($thumb_enabled == 0) echo 'checked';?>/> 禁用
		</td>
	</tr>
	<tr>
		<th>缩略图大小：</th>
		<td><input type="text" name="setting[thumb_width]" value="<?=$thumb_width?>" size="5"/> X <input type="text" name="setting[thumb_height]" value="<?=$thumb_height?>" size="5"/></td>
	</tr>
	<tr>
		<th>缩略图质量：</th>
		<td><input type="text" name="setting[thumb_quality]" value="<?=$thumb_quality?>" size="5"/></td>
	</tr>
</table>
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
	<caption>图片水印</caption>
	<tr>
		<th width="150">图片水印：</th>
		<td>
			<input class="radio" type="radio" <?php if($watermark_enabled==1):?>checked="checked" <?php endif;?>value="1" name="setting[watermark_enabled]">
			启用 &nbsp;
			<input class="radio" type="radio" <?php if($watermark_enabled==0):?>checked="checked" <?php endif;?>value="0" name="setting[watermark_enabled]">
			禁用
		</td>
	</tr>
	<tr>
		<th width="150">默认水印方案：</th>
		<td>
			<select name="setting[default_watermark]">
				<option value="">无</option>
				<?php foreach($watermark as $item):?>
				<option value="<?=$item['watermarkid']?>"<?php if($item['watermarkid']==$default_watermark):?> selected="selected"<?php endif;?>><?=$item['name']?></option>
				<?php endforeach;?>
			</select>&nbsp;&nbsp;
			<a href="javascript:void(0);" onclick="ct.assoc.open('?app=system&controller=watermark&action=index', 'newtab');">管理水印方案</a>
		</td>
	</tr>
	<tr>
		<th></th>
		<td valign="middle">
		  <input type="submit" id="submit" value="保存" class="button_style_2"/>
		</td>
	</tr>
</table>
</form>
<div class="bk_10"></div>
<script type="text/javascript">
$(function(){
	$('#setting_edit_attachment').ajaxForm(function(json){
		if(json.state) ct.tips(json.message);
		else ct.error(json.error);
	});
});
</script>
<?php $this->display('footer', 'system');