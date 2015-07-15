<script type="text/javascript" src="uploader/cmstop.uploader.js"></script>
<div class="bk_8"></div>
<form name="add" id="watermark_add" method="POST" action="?app=<?=$app?>&controller=<?=$controller?>&action=add">
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form" id="add_table">
	<tr>
		<th>水印方案名称：</th>
		<td><input type="text" name="name" id="name" value="" class="w_160" /></td>
	</tr>
	<tr>
		<th>水印图片：</th>
		<td>
			<input type="text" id="watermark_img" name="image" value="" class="w_160" readonly="true" />
			<div id="uploadify" class="uploader" style="margin-bottom: -8px;"><input type="button" value="上传图片" class="button_style_1" /></div>
		</td>
	</tr>
	<tr>
		<th>水印位置：</th>
		<td>
			<table style="margin-bottom: 3px; margin-top:3px;">
				<tr>
					<td><input class="radio" type="radio" name="position" value="1">#1&nbsp;</td>
					<td><input class="radio" type="radio" name="position" value="2">#2&nbsp;</td>
					<td><input class="radio" type="radio" name="position" value="3">#3&nbsp;</td>
				</tr>
				<tr>
					<td><input class="radio" type="radio" name="position" value="4">#4&nbsp;</td>
					<td><input class="radio" type="radio" name="position" value="5">#5&nbsp;</td>
					<td><input class="radio" type="radio" name="position" value="6">#6&nbsp;</td>
				</tr>
				<tr>
					<td><input class="radio" type="radio" name="position" value="7">#7&nbsp;</td>
					<td><input class="radio" type="radio" name="position" value="8">#8&nbsp;</td>
					<td><input class="radio" type="radio" name="position" value="9">#9&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<th>
			<img class="tips hand" width="16" height="16" align="absmiddle" tips="添加水印图片的最小尺寸" src="images/question.gif">水印添加条件：
		</th>
		<td><input type="text" name="minwidth" value="" size="5"/> X <input type="text" name="minheight" value="" size="5"/></td>
	</tr>
	<tr>
		<th>水印不透明度：</th>
		<td><input type="text" name="trans" value="" size="5" /></td>
	</tr>
	<tr>
		<th>JPEG 水印质量：</th>
		<td><input type="text" name="quality" value="" size="5" /></td>
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