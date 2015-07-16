<form name="<?=$controller?>_add" id="<?=$controller?>_add" method="POST" action="?app=<?=$app?>&controller=<?=$controller?>&action=<?=$action?>">
<table id="style_1" width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
	<input type="hidden" name="field" value="upload" />
	<input type="hidden" name="projectid" value="<?=$pid?>" />
	<tr>
		<th><span class="c_red">*</span> 字段名称：</th>
		<td><input type="text" name="setting[fieldname]" size="40"/></td>
	</tr>
	<tr>
		<th><span class="c_red">*</span> 变量名：</th>
		<td><input type="text" name="setting[var]" size="40"/></td>
	</tr>
	<tr>
		<th><span class="c_red">*</span> 文本框ID：</th>
		<td><input type="text" name="setting[fieldid]" size="40"/></td>
	</tr>
	<tr>
		<th> 文本框显示长度：</th>
		<td><input type="text" size="30" name="setting[inputsize]" /></td>
	</tr>
	<tr>
		<th> 允许上传文件类型：</th>
		<td><input type="text" size="30" name="setting[uploadtype]" value="gif|jpg|jpeg"/>（*全部）</td>
	</tr>
</table>
</form>