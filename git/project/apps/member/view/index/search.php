<div class="bk_8"></div>
<form name="member_search" id="member_search" class="validator" method="GET" action="?app=member&controller=index&action=search&search=do">
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
	<tr>
		<th  width="80">I D：</th>
		<td><input type="text" name="userid" value="" size="20"/></td>
	</tr>
	<tr>
		<th>用户名：</th>
		<td><input type="text" name="keywords" value="" size="20"/></td>
	</tr>
	<tr>
		<th>E-mail：</th>
		<td><input type="text" name="email" value="" size="30"/></td>
	</tr>
	<tr>
		<th>用户组：</th>
		<td>
		<select id="groupid" name="groupid">
			<option selected="selected" value="0">全部</option>
			<?php 
			$member_group = table('member_group');
			foreach ($member_group as $value) {
			?>
				<option value="<?php echo $value['groupid'];?>"><?php echo $value['name'];?></option>
			<?php
			}
			?>
		</select>
	</td>
	</tr>
</table>
</form>
<script language="JavaScript" type="text/JavaScript">
</script>