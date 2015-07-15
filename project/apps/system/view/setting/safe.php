<?php $this->display('header', 'system');?>
<div class="bk_10"></div>
<form id="setting_edit_safe" action="?app=system&controller=setting&action=edit" method="POST" class="validator">
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
	<caption>安全设置</caption>
	<tr>
		<th width="150"><?=element::tips('一行一条数据 下面相同')?> 禁止访问网站IP列表：</th>
		<td><textarea name="setting[ipbanned]" rows="6" cols="30" class="bdr"><?=$ipbanned?></textarea></td>
	</tr>
	<tr>
		<th>允许登录后台IP列表：</th>
		<td><textarea name="setting[ipaccess]" rows="6" cols="30" class="bdr"><?=$ipaccess?></textarea></td>
	</tr>
	<tr>
		<th>后台登录失败锁定：</th>
		<td><input id="maxloginfailedtimes" type="text" name="setting[maxloginfailedtimes]" value="<?=$maxloginfailedtimes?>" size="3"/> 次</td>
	</tr>
	<tr>
		<th>IP 锁定时间：</th>
		<td><input id="lockedhours" type="text" name="setting[lockedhours]" value="<?=$lockedhours?>" size="3"/> 小时</td>
	</tr>
	<tr>
		<th>页面刷新最短时间间隔：</th>
		<td><input id="minrefreshsecond" type="text" name="setting[minrefreshsecond]" value="<?=$minrefreshsecond?>" size="3"/> 秒</td>
	</tr>
	<tr>
		<th width="150">启用后台操作日志：</th>
		<td><input type="radio" name="setting[enableadminlog]" value="1" class="radio" <?php if ($enableadminlog) echo 'checked';?>/>是 &nbsp;&nbsp;<input type="radio" name="setting[enableadminlog]" value="0" class="radio" <?php if (!$enableadminlog) echo 'checked';?>>否</td>
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
	$('#setting_edit_safe').ajaxForm(function(json){
		if(json.state) ct.tips(json.message);
		else ct.error(json.error);
	});
	$('img.tips').attrTips('tips', 'tips_green', 200, 'top');
});
</script>
<?php $this->display('footer', 'system');