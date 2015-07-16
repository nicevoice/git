<?php $this->display('header', 'system');?>
<div class="bk_10"></div>
<form id="setting_edit_api" action="?app=system&controller=setting&action=edit" method="POST" class="validator">
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
	<caption>百度地图API</caption>
	<tr>
		<th width="150">密钥：</th>
		<td><input id="baidumapkey" type="text" name="setting[baidumapkey]" value="<?=$baidumapkey?>" size="40"/>&nbsp;&nbsp;&nbsp;<a href="http://openapi.baidu.com/map/signup.html?form=cmstop" target="_blank">立即注册</a>
</td>
	</tr>
</table>
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
	<caption>新浪API</caption>
	<tr>
		<th width="150">App Key：</th>
		<td><input id="sina_key" type="text" name="setting[sina_appkey]" value="<?=$sina_appkey?>" size="40"/>&nbsp;&nbsp;&nbsp;<a href="http://open.weibo.com/" target="_blank">立即申请</a></td>
	</tr>
	<tr>
		<th width="150">App Secret：</th>
		<td><input id="sina_Secret" type="text" name="setting[sina_appsecret]" value="<?=$sina_appsecret?>" size="40"/></td>
	</tr>
</table>
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
	<caption>CC视频API</caption>
	<tr>
		<th width="150">CC用户ID：</th>
		<td><input id="ccid" type="text" name="setting[ccid]" value="<?=$ccid?>" size="40"/> &nbsp;&nbsp;&nbsp;<a href="http://admin.bokecc.com/register.bo?form=cmstop" target="_blank">立即注册</a></td>
	</tr>
	<tr>
		<th></th>
		<td valign="middle"><input type="submit" id="submit" value=" 保存 " class="button_style_2"/></td>
	</tr>
</table>
</form>
<div class="bk_10"></div>
<script type="text/javascript">
$(function(){
	$('#setting_edit_api').ajaxForm(function(json){
		if(json.state) ct.tips(json.message);
		else ct.error(json.error);
	});
});
</script>
<?php $this->display('footer', 'system');