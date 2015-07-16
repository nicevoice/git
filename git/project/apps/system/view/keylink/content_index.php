<div class="bk_8"></div>
<form method="POST" class="validator" action="?app=system&controller=keylink&action=add">
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
<?php foreach ($keywords as $k):?>
	<tr>
		<th width="80">关键词：</th>
		<td><input type="text" name="name[]" id="name" value="<?=$k['name']?>" size="30"/></td>
	</tr>
	<tr>
		<th>地址：</th>
		<td><input type="text" name="url[]" value="<?=$k['url']?>" size="45"/></td>
	</tr>
<?php endforeach;?>
</table>
</form>