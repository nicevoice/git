<div class="bk_8"></div>
<form name="<?=$app?>_<?=$controller?>_<?=$action?>" id="<?=$app?>_<?=$controller?>_<?=$action?>" method="post" class="validator" action="?app=<?=$app?>&controller=<?=$controller?>&action=add">
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
	<tr>
		<th width="80">IP：</th>
		<td><input type="text" name="ip" id="ip" value=""></td>
	</tr>
	<tr>
		<th>解封时间：</th>
		<td><input onclick="DatePicker(this,{'format':'yyyy-MM-dd HH:mm:ss'});"  name="expires" value="" class="input_calendar"/></td>
	</tr>
</table>
</form>