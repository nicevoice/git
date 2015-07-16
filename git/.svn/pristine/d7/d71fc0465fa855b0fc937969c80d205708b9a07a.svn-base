<?php $this->display('header', 'system');?>
<div class="bk_10"></div>
<form id="mood_setting" action="?app=mood&controller=setting&action=index" method="POST">
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
	<caption>心情配置</caption>
	<tr><th></th><td valign="middle"></td></tr>
	<tr><th width="120">前台投票时间间隔:</th>
		<td><input type="text" size="3" onkeyup="value=value.replace(/[^0-9]/g,'')"  name="setting[votetime]" value="<?=$setting['votetime']?>"> 秒</td>
	</tr>
	<tr><th></th>
		<td valign="middle"><input type="submit" id="submit" value="保存" class="button_style_2"/>
		</td>
	</tr>
</table>
</form>

<script type="text/javascript">
	$(function(){
		$('#mood_setting').ajaxForm('submit_ok');
	})
	function submit_ok(data) {
		if(data.state) ct.tips("保存成功");
		else ct.error(data.error);
	}
</script>
<?php $this->display('footer', 'system');?>