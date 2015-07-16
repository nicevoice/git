<?php $this->display('header', 'system');?>
<div class="bk_10"></div>
<form id="digg_setting" action="?app=digg&controller=setting&action=index" method="POST">
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
	<caption>Digg基本设置</caption>
	<tr><th width="120">防刷新限制：</th>
		<td><input type="text" name="setting[refresh]" value="<?php echo $setting['refresh']?>" size="4"/> 秒</td>
	</tr>
	<tr>
		<th></th>
		<td valign="middle"><input type="submit" id="submit" value="保存" class="button_style_2"/></td>
	</tr>
</table>
</form>
<div class="bk_10"></div>
<script type="text/javascript">
$('#digg_setting').ajaxForm(function(json) {
	if(json.state) {
		ct.tips("保存成功");
	} else {
		ct.error(json.error);
	}
});
</script>
<?php $this->display('footer', 'system');?>