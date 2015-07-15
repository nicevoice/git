<div class="bk_8"></div>
<form name="system_source_search" id="system_source_search" method="GET" action="?app=system&controller=source&action=page">
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
	<tr>
		<th width="100">首字母：</th>
		<td><input type="text" name="initial" value="<?=$initial?>" size="2" maxlength="1"/></td>
	</tr>
	<tr>
		<th>来源名称：</th>
		<td><input type="text" name="keywords" value="<?=$name?>" size="20"/></td>
	</tr>
	<tr>
		<th>链接地址：</th>
		<td><input type="text" name="url" value="<?=$url?>" size="40"/></td>
	</tr>
</table>
</form>