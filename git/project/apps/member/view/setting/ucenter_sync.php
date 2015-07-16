<div class="bk_10"></div>
<form method="POST" class="validator" action="?app=member&controller=setting&action=merge&type=<?php echo $_GET['type']?>">
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
<input type="hidden" name="start" value="0"/>
<input type="hidden" name="total" value="<?php echo intval($ct_num);?>"/>
<tr>
	<th width="100">每次处理条数：</th>
	<td><input type="text" value="200" name="perpage"/> </td>
</tr>
</table>
</form>