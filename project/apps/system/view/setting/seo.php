<?php $this->display('header', 'system');?>
<div class="bk_10"></div>
<form id="setting_edit_seo" action="?app=system&controller=setting&action=edit" method="POST">
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
	<caption>SEO 设置</caption>
	<tr>
		<th width="120">标题附加字：</th>
		<td><input type="text" name="setting[seotitle]" value="<?=$seotitle?>" size="50"/></td>
	</tr>
	<tr>
		<th>Meta Keywords：</th>
		<td><input type="text" name="setting[seokeywords]" value="<?=$seokeywords?>" size="80"/></td>
	</tr>
	<tr>
		<th>Meta Description：</th>
		<td><textarea  name="setting[seodescription]" rows="8" cols="80" class="bdr"><?=$seodescription?></textarea></td>
	</tr>
	<tr>
		<th></th>
		<td valign="middle"><input type="submit" id="submit" value="保存" class="button_style_2"/></td>
	</tr>
</table>
</form>
<div class="bk_10"></div>
<script type="text/javascript">
$(function(){
	$('#setting_edit_seo').ajaxForm(function(json){
		if(json.state) ct.tips(json.message, 'success', 'center');
		else ct.error(json.error);
	});
});
</script>
<?php $this->display('footer', 'system');