<div class="bk_8"></div>
<form name="system_menu_edit" id="system_menu_edit" method="post" class="validator" action="?app=<?=$app?>&controller=<?=$controller?>&action=edit&menuid=<?=$menuid?>">
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
	<tr>
		<th>菜单名称：</th>
		<td><input type="text" name="name" id="name" value="<?=$name?>" size="30"/></td>
	</tr>
	<tr>
		<th>菜单地址：</th>
		<td><input type="text" name="url" value="<?=$url?>" size="45"/></td>
	</tr>
	<tr>
		<th>菜单排序：</th>
		<td><input type="text" name="sort" value="<?=$sort?>" size="3" maxlength="2"/></td>
	</tr>
</table>
</form>