<div class="bk_10"></div>
<form name="guestbook_type_edit" id="guestbook_type_edit" method="POST" class="validator" action="?app=guestbook&controller=type&action=edit&typeid=<?=$typeid?>">
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
	<input type="hidden" name="typeid" id="typeid" value="<?=$typeid?>"/>
	<tr>
		<th width="80">类型：</th>
		<td><input type="text" name="name" id="name" value="<?=$name?>" size="22"/></td>
	</tr>
	<tr>
		<th>排序：</th>
		<td><input type="text" name="sort" id="sort" value="<?=$sort?>" size="22"/></td>
	</tr>
</table>
</form>