<form action="?app=system&controller=role&action=<?=$action?>" method="POST">
<input type="hidden" name="roleid" value="<?=$roleid?>" />
<table cellspacing="0" cellpadding="0" border="0" width="93%" class="table_form mar_l_8">
	<tbody>
		<tr>
			<th width="80">角色名称：</th>
		    <td><input value="<?=$name?>" name="name"/></td>
		</tr>
		<tr>
			<th valign="top">分配权限：</th>
		    <td>
			    <table width="98%" class="table_list mar_l_8 treeTable" cellpadding="0" cellspacing="0">
				  <tbody>
				  </tbody>
				</table>
		    </td>
		</tr>
	</tbody>
</table>
</form>