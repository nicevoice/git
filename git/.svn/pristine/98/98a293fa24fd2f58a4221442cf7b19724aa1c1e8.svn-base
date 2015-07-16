<script type="text/javascript" src="uploader/cmstop.uploader.js"></script>
<div class="bk_8"></div>
<form name="add" id="watermark_add" method="POST" action="?app=<?=$app?>&controller=<?=$controller?>&action=edit&id=<?=$id?>">
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form" id="add_table">
	<tr>
		<th>水印方案名称：</th>
		<td><input type="text" name="name" id="name" value="<?=$watermark['name']?>" class="w_160" />&nbsp;<input type="checkbox" name="disable" value="1"<?php if($watermark['disable']):?> checked="checked"<?php endif;?> />&nbsp;禁用</td>
	</tr>
	<tr>
		<th>水印图片：</th>
		<td>
			<input type="text" id="watermark_img" name="image" value="<?=$watermark['image']?>" class="w_160" readonly="true" />
			<div id="uploadify" class="uploader" style="margin-bottom: -8px;"><input type="button" value="上传图片" class="button_style_1" /></div>
		</td>
	</tr>
	<tr>
		<th>水印位置：</th>
		<td>
			<table style="margin-bottom: 3px; margin-top:3px;">
				<tr>
					<td><input class="radio" type="radio" name="position" value="1"<?php if($watermark['position']==1):?> checked="checked"<?php endif;?>>#1&nbsp;</td>
					<td><input class="radio" type="radio" name="position" value="2"<?php if($watermark['position']==2):?> checked="checked"<?php endif;?>>#2&nbsp;</td>
					<td><input class="radio" type="radio" name="position" value="3"<?php if($watermark['position']==3):?> checked="checked"<?php endif;?>>#3&nbsp;</td>
				</tr>
				<tr>
					<td><input class="radio" type="radio" name="position" value="4"<?php if($watermark['position']==4):?> checked="checked"<?php endif;?>>#4&nbsp;</td>
					<td><input class="radio" type="radio" name="position" value="5"<?php if($watermark['position']==5):?> checked="checked"<?php endif;?>>#5&nbsp;</td>
					<td><input class="radio" type="radio" name="position" value="6"<?php if($watermark['position']==6):?> checked="checked"<?php endif;?>>#6&nbsp;</td>
				</tr>
				<tr>
					<td><input class="radio" type="radio" name="position" value="7"<?php if($watermark['position']==7):?> checked="checked"<?php endif;?>>#7&nbsp;</td>
					<td><input class="radio" type="radio" name="position" value="8"<?php if($watermark['position']==8):?> checked="checked"<?php endif;?>>#8&nbsp;</td>
					<td><input class="radio" type="radio" name="position" value="9"<?php if($watermark['position']==9):?> checked="checked"<?php endif;?>>#9&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<th>
			<img class="tips hand" width="16" height="16" align="absmiddle" tips="添加水印图片的最小尺寸" src="images/question.gif">水印添加条件：
		</th>
		<td><input type="text" name="minwidth" value="<?=$watermark['minwidth']?>" size="5"/> X <input type="text" name="minheight" value="<?=$watermark['minheight']?>" size="5"/></td>
	</tr>
	<tr>
		<th>水印不透明度：</th>
		<td><input type="text" name="trans" value="<?=$watermark['trans']?>" size="5" /></td>
	</tr>
	<tr>
		<th>JPEG 水印质量：</th>
		<td><input type="text" name="quality" value="<?=$watermark['quality']?>" size="5" /></td>
	</tr>
</table>
</form>
<script type="text/javascript">
(function() {
	var uploadBtn		= $('#uploadify');
	var waterMarkImg	= $('#watermark_img');
	uploadBtn.uploader({
		script			: '?app=<?=$app?>&controller=<?=$controller?>&action=upload',
		fileDesc		: '图像',
		fileExt			: '*.gif;*.png;',
		multi			: false,
		complete:function(response,data){
			waterMarkImg.val(response);
			wm.thumb(waterMarkImg, "<?=UPLOAD_URL?>"+response);
		},
		error:function(data) {
			alert(data.error.type);
		}
	});
	$('.tips').attrTips('tips', 'tips_green');
	window.formReady = function() {
		wm.thumb(waterMarkImg, "<?php echo UPLOAD_URL.$watermark['image']?>");
		window.formReady = undefined;
	};
	$('input[name=ext]').change(function() {
		if (this.value == 'gif') {
			$('input[name=trans]').attr('disabled', '');
		}
		if (this.value == 'png') {
			$('input[name=trans]').val(100).attr('disabled', 'disabled');
		}
	});
})()
</script>