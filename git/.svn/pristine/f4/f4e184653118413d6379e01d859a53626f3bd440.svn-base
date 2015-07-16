<?php $this->display('header');?>
<div class="bk_10"></div>
<form name="video_vms_setting" id="video_vms_setting" action="?app=video&controller=vms&action=setting" method="POST">
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
	<caption>视频接口配置</caption>
	<tr>
		<th width="120">开启视频服务器：</th>
		<td colspan="2"><input type="radio" name="setting[openserver]" value="1" <?php if($setting['openserver']){echo "checked";} ?> />开启 &nbsp; <input type="radio"name="setting[openserver]" value="0" <?php if(!$setting['openserver']){echo "checked";} ?> />关闭</td>
	</tr>
	<tr>
		<th>接口地址：</th>
		<td colspan="2"><input type="text" name="setting[apiurl]" value="<?=$setting['apiurl']?>" size="60"/></td>
	</tr>
	<tr>
		<th>接口密钥：</th>
		<td colspan="2"><input type="text" name="setting[apikey]" value="<?=$setting['apikey']?>" size="60"/></td>
	</tr>
	<tr>
		<th>允许上传的格式：</th>
		<td colspan="2"><input type="text" name="setting[filetype]" value="<?=$setting['filetype']?>" size="60"/></td>
	</tr>
	<tr>
		<th width="120">播放器调用接口：</th>
		<td colspan="2"><input type="text" name="setting[player]" value="<?=$setting['player']?>" size="60"/></td>
	</tr>
	<tr>
		<th></th>
		<td colspan="2" valign="middle"><br/>
		<input type="submit" id="submit" value="保存" class="button_style_2"/>
	</td>
	</tr>
</table>
</form>
<script type="text/javascript">
$(function(){
	$('#video_vms_setting').ajaxForm('submit_ok');
})
function submit_ok(data) {
	if(data.state) ct.tips("保存成功");
	else ct.error(data.error);
}
</script>
<?php $this->display('footer');?>