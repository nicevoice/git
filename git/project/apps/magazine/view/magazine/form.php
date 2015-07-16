<div class="bk_8"></div>
<form id="magazine_add" name="magazine_add" method="POST" class="validator" action="?app=magazine&controller=magazine&action=save">
<input type="hidden" name="mid" value="<?=$magazine['mid']?>"/>
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
	<tr>
		<th width="80"><span class="c_red">*</span> 杂志名称：</th>
		<td><input type="text" name="name" value="<?=$magazine['name']?>" size="40"/></td>
	</tr>
	<tr>
		<th><span class="c_red">*</span> 别名：</th>
		<td><input type="text" name="alias" value="<?=$magazine['alias']?>" size="40"/></td>
	</tr>
	<tr>
		<th><span class="c_red">*</span> 类型：</th>
		<td>
			<input id="suggestInput" type="text" name="type" value="<?=$magazine['type']?>" size="40"/><br/>
			<ul id="suggest" class="ac_results" style="display: none; width: 80px;">
				<li class="ac_over"><span class="ac_match">月刊</span></li>
				<li><span class="ac_match">半月刊</span></li>
				<li><span class="ac_match">周刊</span></li>
			</ul>
		</td>
	</tr>
	<tr>
		<th><span class="c_red">*</span> 发行时间：</th>
		<td><input type="text" name="publish" value="<?=$magazine['publish']?>" size="40"/></td>
	</tr>
	<tr>
		<th><span class="c_red">*</span> 列表模板：</th>
		<td><?=element::template("template_list","template_list",$magazine['template_list'],28);?></td>
	</tr>
	<tr>
		<th><span class="c_red">*</span> 内页模板：</th>
		<td><?=element::template("template_content","template_content",$magazine['template_content'],28);?></td>
	</tr>
	<tr>
		<th>缩略图：</th>
		<td><?=element::image('logo', $magazine['logo'], 28)?></td>
	</tr>
	<tr>
		<th> 网址：</th>
		<td><input type="text" name="url" value="<?=$magazine['url']?>" size="40"/></td>
	</tr>
	<tr>
		<th> 简介：</th>
		<td>
			<textarea name="memo" style="width: 336px;height:60px;"><?=$magazine['memo']?></textarea>
		</td>
	</tr>
</table>
</form>