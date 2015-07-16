<div class="bk_8"></div>
<form name="member_group_edit" id="member_group_edit" method="POST" class="validator" action="?app=member&controller=group&action=edit&groupid=<?=$groupid?>">
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
	<tr>
	<th width="80"><span class="c_red">*</span>名称：</th>
	<td><input type="text" name="name" id="name" value="<?=$name?>" size="16"/></td>
	</tr>
	<tr><th>备注：</th><td>
	<label><textarea name="remarks" id="remarks" style="width:240px;height:60px;overflow:hidden"><?=$remarks?></textarea></label>
	</td></tr>
</table>
</form>