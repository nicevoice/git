<div class="bk_8"></div>
<form name="<?=$controller?>_add" id="<?=$controller?>_add" method="POST" action="?app=<?=$app?>&controller=<?=$controller?>&action=<?=$action?>">
	<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
		<input type="hidden" value="<?=$field['projectid']?>" name="pid">
		<tr>
			<th><span class="c_red">*</span> 方案名称：</th>
			<td><input type="text" name="name" value="<?=htmlspecialchars($field['name'])?>" size="28"/></td>
		</tr>
		<tr>
			<th>方案描述：</th>
			<td><textarea name="description" cols="24" rows="6"><?=$field['description']?></textarea></td>
		</tr>
	</table>
</form>