<div class="bk_8"></div>
<form name="<?=$app?>_<?=$controller?>_<?=$action?>" id="<?=$app?>_<?=$controller?>_<?=$action?>" method="post" class="validator" action="?app=<?=$app?>&controller=<?=$controller?>&action=add">
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
	<tr>
		<th width="100"><span class="c_red">*</span> 来源名称：</th>
		<td><input type="text" name="name" id="name" value="<?=$name?>" size="20"/></td>
	</tr>
	<tr>
		<th>链接地址：</th>
		<td><input type="text" name="url" value="http://" size="40"/></td>
	</tr>
	<tr>
		<th>Logo地址：</th>
		<td><input type="text" name="logo" value="" size="40"/></td>
	</tr>
</table>
</form>