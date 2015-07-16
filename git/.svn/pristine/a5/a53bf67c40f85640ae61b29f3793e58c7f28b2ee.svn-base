<?php $this->display('header','system'); ?>
<script type="text/javascript" src="apps/push/js/rule_edit.js"></script>
<style type="text/css">
#tasklabel {
	margin-top:2px;
	line-height:30px;
}
#tasklabel label {
	margin-right:5px;
}
</style>
<div class="bk_10"></div>
<div class="suggest mar_l_8" style="width:98%">
  <h2>友情提示</h2>
  <p>文章推送功能将外部第三方数据库，根据预置好的数据库对应规则，将外部文章推送到网站。对于同一个网站拥有多个应用程序，能实现无缝整合与数据共享</p>
  <p>用此功能之前，用户需要了解第三方数据库结构</p>
</div>
<div class="bk_8"></div>
<form id="ruleForm" name="rule_add" action="?app=push&controller=push&action=edit" method="POST">
<input type="hidden" name="ruleid" value="<?=$ruleid?>" />
<table class="table_form mar_l_8" width="98%" border="0" cellpadding="0" cellspacing="0">
	<caption>基本信息</caption>
	<tr>
		<th width="150"><span class="c_red">*</span> 规则名称：</th>
		<td><input type="text" name="name" value="<?=$name?>" /></td>
	</tr>
	<tr>
		<th>备注：</th>
		<td><textarea style="width:527px;" name="description"><?=htmlspecialchars($description)?></textarea></td>
	</tr>
	<tr>
		<th><span class="c_red">*</span> 数据源：</th>
		<td><?=element::dsn_select('dsnid',$dsnid)?>&nbsp;&nbsp;<a href="javascript:;" id="adddsn">[添加]</a></td>
	</tr>
	<tbody>
		<tr>
			<th><span class="c_red">*</span> 数据表：</th>
			<td><input name="maintable" type="text" value="<?=$maintable?>" />&nbsp;&nbsp;<a href="javascript:;" id="addtable">[添加]</a></td>
		</tr>
		<?php foreach ($jointable as $table => $on):?>
		<tr>
			<th>&nbsp;</th>
			<td><input name="jointable[]" type="text" value="<?=$table?>" />&nbsp;&nbsp;与主表关联条件：&nbsp;&nbsp;<input name="on[]" value="<?=$on?>" size="50" type="text" />&nbsp;&nbsp;<a onclick="$(this).parents('tr').remove()" href="javascript:;">[删除]</a></td>
		</tr>
		<?php endforeach;?>
		<tr style="display:none">
			<th>&nbsp;</th>
			<td><input name="jointable[]" disabled="disabled" type="text" />&nbsp;&nbsp;与主表关联条件：&nbsp;&nbsp;<input name="on[]" disabled="disabled" size="50" type="text" />&nbsp;&nbsp;<a onclick="$(this).parents('tr').remove()" href="javascript:;">[删除]</a></td>
		</tr>
	</tbody>
	<tr>
		<th><span class="c_red">*</span> 主键字段：</th>
		<td><input type="text" name="primary" value="<?=$primary?>" /></td>
	</tr>
	<tr>
		<th>SQL条件语句：</th>
		<td><input name="condition" type="text" value="<?=$condition?>" size="70" /></td>
	</tr>
	<tr>
		<th>应用插件：</th>
		<td>
			<select name="plugin">
				<option value="other" <?=$plugin=='other' ? 'selected' : ''?>>默认</option>
				<option value="discuz" <?=$plugin=='discuz' ? 'selected' : ''?>>discuz posts</option>
			</select>
		</td>
	</tr>
	<tr>
		<th>原文地址规则：</th>
		<td><input name="linkrule" value="<?=$linkrule?>" type="text" size="70" /></td>
	</tr>
</table>
<table id="fields" class="table_form mar_l_8" width="98%" border="0" cellpadding="0" cellspacing="0">
	<caption>字段对应</caption>
	<tr>
		<th width="150">&nbsp;</th>
		<td width="200">数据表字段</td>
		<td class="t_l">默认值</td>
	</tr>
	<tr>
		<th width="150">标题：</th>
		<td><input type="text" class="field" size="30" name="fields[title]" value="<?=$fields['title']?>" /></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<th>正文：</th>
		<td><input type="text" class="field" size="30"  name="fields[content]" value="<?=$fields['content']?>" /></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<th>作者：</th>
		<td><input type="text" class="field" size="30"  name="fields[author]" value="<?=$fields['author']?>" /></td>
		<td><input type="text" name="defaults[author]" value="<?=$defaults['author']?>" /></td>
	</tr>
	<tr>
		<th>Tags：</th>
		<td><input type="text" class="field" size="30"  name="fields[tags]" value="<?=$fields['tags']?>" /></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<th>摘要：</th>
		<td><input type="text" class="field" size="30"  name="fields[descr]" value="<?=$fields['descr']?>" /></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<th>来源：</th>
		<td><input type="text" class="field" size="30"  name="fields[source]" value="<?=$fields['source']?>" /></td>
		<td><input type="text" name="defaults[source]" value="<?=$defaults['source']?>" /></td>
	</tr>
	<tr>
		<th>发布时间：</th>
		<td><input type="text" class="field" size="30" name="fields[pubdate]" value="<?=$fields['pubdate']?>" /></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<th>访问量：</th>
		<td><input type="text" class="field" size="30" name="fields[visitnum]" value="<?=$fields['visitnum']?>" /></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<th>回复量：</th>
		<td><input type="text" class="field" size="30" name="fields[replynum]" value="<?=$fields['replynum']?>" /></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<th>&nbsp;</th>
		<td>
			<input type="submit" value="保存" class="button_style_2" /> &nbsp;&nbsp;
			<button onclick="App.testGetList()" type="button" class="button_style_1">获取测试</button> &nbsp;&nbsp;
		</td><td>&nbsp;</td>
	</tr>
</table>
</form>
<script type="text/javascript">
App.init();
</script>
</body>
</html>