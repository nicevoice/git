<div class="bk_8"></div>
<form name="<?=$app?>_<?=$controller?>_<?=$action?>" id="<?=$app?>_<?=$controller?>_<?=$action?>" method="post" class="validator" action="?app=<?=$app?>&controller=<?=$controller?>&action=edit">
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
	<tr>
		<th width="80"> Tag：</th>
		<td><?=$tag?><input name="tagid" type="hidden" value="<?=$tagid?>" /></td>
	</tr>
	<tr>
		<th>颜色：</th>
		<td><input id="style" name="style" type="text" value="<?=$style?>" size="7"/> <img src="images/color.gif" alt="色板" height="16" width="16" style="cursor:pointer;" /></td>
	</tr>
	<tr>
		<th>排序：</th>
		<td><input type="text" name="sort" value="<?=$sort?>" size="3"/></td>
	</tr>
</table>
</form>