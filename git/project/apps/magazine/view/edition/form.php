<div class="bk_8"></div>
<form name="magazine_edition_edit" id="magazine_edition_edit" method="POST" action="?app=magazine&controller=edition&action=save">
<input type="hidden" name="eid" value="<?=intval($_GET['eid'])?>"/>
<input type="hidden" name="mid" value="<?=intval($_GET['mid'])?>"/>
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
	<tr>
		<th width="100"> 期标题：</th>
		<td>
			<input type="text" name="title" id="title" value="<?=$title?>" size="20"/>
		</td>
	</tr>
	<tr>
		<th width="100"><span class="c_red">*</span> 总期号：</th>
		<td>
			<input type="text" name="total_number" id="total_number" value="<?=$total_number?>" size="5"/>
			<? if($lastTN) echo '最后期号: '.$lastTN?>
		</td>
	</tr>
	<tr>
		<th width="100"><span class="c_red">*</span> 年度期号：</th>
		<td>
			<input type="text" name="number" id="number" value="<?=$number?>" size="5"/>
			<? if($lastN) echo '最后期号: '.$lastN?>
		</td>
	</tr>
	<tr>
		<th><span class="c_red">*</span> 出版日期：</th>
		<td><input type="text" name="publish" class="input_calendar"  value="<?if($publish > 0) echo $publish?>" size="20" style="width:65px;" />
		</td>
	</tr>
	<tr>
		<th>封面图片：</th>
		<td><?=element::image('image', $image, 20)?></td>
	</tr>
	<tr>
		<th>PDF：</th>
		<td>
			<div class="thumb_cell">
				<input type="text" value="<?=$pdf?>" size="20" name="pdf">
				<span class="uploader" id="pdf">上传</span>
			</div>
		</td>
	</tr>
</table>
</form>
<script>
$(function (){
	setTimeout(pdfUpload, 500);
});
function pdfUpload()
{
	$("#pdf").uploader({
		script		: '?app=system&controller=upload&action=upload',
		fileDesc	: 'PDF文档',
		fileExt		: '*.pdf;',
		buttonImg	: 'images/uppdf.gif',
		multi : false,
		jsonType:1,
		complete:function(json, data)
		 {
		 	if(json.state)
		 	{
		 		$('input[name=pdf]').val(json.file);
		 	}
		 	else
		 	{
		 		ct.error('上传失败');
		 	}
		 },
		 error:function(data)
		 {
		 	alert(data.error.info);
		 }
	});
}
</script>