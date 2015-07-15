<div class="bk_8"></div>
<form name="system_menu_add" id="system_menu_add" method="POST" class="validator" action="?app=system&controller=menu&action=add">
<input name="parentid" type="hidden" value="<?=$_GET['parentid']?>"/>
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
	<tr>
		<th width="80">菜单名称：</th>
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