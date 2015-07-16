<?php $this->display('header','system'); ?>
<script type="text/javascript" src="apps/push/js/rule_add.js"></script>
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
<form id="ruleForm" name="rule_add" action="?app=push&controller=push&action=add" method="POST">
<table class="table_form mar_l_8" width="98%" border="0" cellpadding="0" cellspacing="0">
	<caption>基本信息</caption>
	<tr>
		<th width="150"><span class="c_red">*</span> 规则名称：</th>
		<td><input type="text" name="name" value="" /></td>
	</tr>
	<tr>
		<th>备注：</th>
		<td><textarea style="width:527px;" name="description"></textarea></td>
	</tr>
	<tr>
		<th><span class="c_red">*</span> 数据源：</th>
		<td><?=element::dsn_select('dsnid')?>&nbsp;&nbsp;<a href="javascript:;" id="adddsn">[添加]</a></td>
	</tr>
	<tbody>
		<tr>
			<th><span class="c_red">*</span> 数据表：</th>
			<td><input name="maintable" type="text" />&nbsp;&nbsp;<a href="javascript:;" id="addtable">[添加]</a></td>
		</tr>
		<tr style="display:none">
			<th>&nbsp;</th>
			<td><input name="jointable[]" disabled="disabled" type="text" />&nbsp;&nbsp;与主表关联条件：&nbsp;&nbsp;<input name="on[]" disabled="disabled" size="50" type="text" />&nbsp;&nbsp;<a onclick="$(this).parents('tr').remove()" href="javascript:;">[删除]</a></td>
		</tr>
	</tbody>
	<tr>
		<th><span class="c_red">*</span> 主键字段：</th>
		<td><input type="text" name="primary" /></td>
	</tr>
	<tr>
		<th>SQL条件语句：</th>
		<td><input name="condition" type="text" size="70" /></td>
	</tr>
	<tr>
		<th>应用插件：</th>
		<td>
			<select name="plugin">
				<option value="other">默认</option>
				<option value="discuz">discuz! 论坛主题</option>
			</select>
		</td>
	</tr>
	<tr>
		<th>原文地址规则：</th>
		<td><input name="linkrule" type="text" size="70" /></td>
	</tr>
	<tr>
		<th>&nbsp;</th>
		<td>
			<label>同时添加监听任务 <input type="checkbox" onclick="this.checked ? $('#tasklabel').show().find('input,select').removeAttr('disabled') : $('#tasklabel').hide().find('input,select').attr('disabled','disabled')" /></label>
			<div id="tasklabel" style="display:none">
				<label>任务名称：<input name="taskname" disabled="disabled" type="text" /></label>
				<label>栏目：<?=element::category('catid','catid',null,1,' disabled="disabled" style="width:100px"')?></label> <br/>
				<label>SQL条件语句：<input disabled="disabled" name="extra_condition" type="text" size="70" /></label>
			</div>
		</td>
		<td>&nbsp;</td>
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
		<td><input type="text" class="field" size="30" name="fields[title]" /></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<th>正文：</th>
		<td><input type="text" class="field" size="30"  name="fields[content]" /></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<th>作者：</th>
		<td><input type="text" class="field" size="30"  name="fields[author]" /></td>
		<td><input type="text" name="defaults[author]" /></td>
	</tr>
	<tr>
		<th>Tags：</th>
		<td><input type="text" class="field" size="30"  name="fields[tags]" /></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<th>摘要：</th>
		<td><input type="text" class="field" size="30"  name="fields[descr]" /></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<th>来源：</th>
		<td><input type="text" class="field" size="30"  name="fields[source]" /></td>
		<td><input type="text" name="defaults[source]" /></td>
	</tr>
	<tr>
		<th>发布时间：</th>
		<td><input type="text" class="field" size="30" name="fields[pubdate]" /></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<th>访问量：</th>
		<td><input type="text" class="field" size="30" name="fields[visitnum]" /></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<th>回复量：</th>
		<td><input type="text" class="field" size="30" name="fields[replynum]" /></td>
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