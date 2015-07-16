<div class="bk_8"></div>
<form id="paper_add" name="paper_add" method="POST" class="validator" action="?app=paper&controller=paper&action=save">
<input type="hidden" name="paperid" value="<?=$paper['paperid']?>"/>
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
	<tr>
		<th width="80"><span class="c_red">*</span> 报纸名称：</th>
		<td><input type="text" name="name" value="<?=$paper['name']?>" size="20"/></td>
	</tr>
	<tr>
		<th><span class="c_red">*</span> 别名：</th>
		<td><input type="text" name="alias" value="<?=$paper['alias']?>" size="20"/></td>
	</tr>
	<tr>
		<th><span class="c_red">*</span> 版面数：</th>
		<td><input type="text" name="pages" value="<?=$paper['pages']?>" size="20"/></td>
	</tr>
	<tr>
		<th>内容模板：</th>
		<td><?=element::template("template_content","template_content",$paper['template_content'],25);?></td>
	</tr>
	<tr>
		<th>缩略图：</th>
		<td><?=element::image('logo', $paper['logo'], 25)?></td>
	</tr>
</table>
</form>