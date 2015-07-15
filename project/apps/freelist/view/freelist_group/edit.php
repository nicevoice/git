<div class="bk_8"></div>
<form name="group_add" id="group_add" method="POST" class="validator" action="?app=freelist&controller=group&action=edit">
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
	<input type="hidden" value="<?=$group['gid']?>" name="gid">
	<tr>
		<th width="80">分组名称：</th>
		<td><input type="text" name="name" id="name" value="<?=$group['name']?>" size="20"/></td>
	</tr>
</table>
</form>