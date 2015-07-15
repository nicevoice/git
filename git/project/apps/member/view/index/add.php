<div class="bk_8"></div>
<form name="member_index_add" id="member_index_add" method="POST" class="validator" action="?app=member&controller=index&action=add">
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
	<tr>
		<th width="80">用户名：</th>
		<td><input type="text" name="username" id="username" value="" size="20"/></td>
	</tr>
	<tr>
		<th>密码：</th>
		<td><input type="text" name="password" id="password" value="" size="20" /></td>
	</tr>
	<tr>
		<th>E-mail：</th>
		<td><input type="text" name="email" id="email" value="" size="30" /></td>
	</tr>
	<tr>
		<th>用户组：</th>
		<td id="group_td"><?=element::member_groups();?></td>
	</tr>
</table>
</form>
<script language="JavaScript" type="text/JavaScript">
	$("#group_td > select > option:lt(2)").remove();
	$('#groupid >option[value=6]').attr('selected','selected');
</script>