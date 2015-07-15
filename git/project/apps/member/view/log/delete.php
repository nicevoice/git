<div class="bk_8"></div>
<form id="member_log_delete" method="POST" class="validator" action="?app=member&controller=log&action=delete">
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
	<tr>
		<th>用户名：</th>
		<td><input type="text" name="username" id="username" value="" size="20" /></td>
	</tr>
	<tr>
		<th>IP：</th>
		<td><input type="text" name="ip" id="ip" value="" size="20" /></td>
	</tr>
	<tr>
		<th>日期：</th>
		<td><input type="text" name="date" id="date" class="input_calendar hasDatepicker" value="" size="24" /></td>
	</tr>
	<tr>
		<th>结果：</th>
		<td><select name="succeed">
			<option value="">全部</option>
			<option value="1">成功</option>
			<option value="0">失败</option>
		</select></td>
	</tr>
</table>
</form>
<script type="text/javascript">
$('#date').DatePicker({'format':'yyyy-MM-dd HH:mm:ss'});
</script>