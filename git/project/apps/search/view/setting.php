<?php $this->display('header', 'system');?>
<div class="bk_10"></div>
<div class="suggest w_650 mar_l_8">
	<h2>友情提示</h2>
	<p>Sphinx是一个基于SQL的全文检索引擎，可以结合MySQL,PostgreSQL做全文搜索，它可以提供比数据库本身更专业的搜索功能，使得应用程序更容易实现专业化的全文检索。Sphinx特别为一些脚本语言设计搜索API接口，如PHP,Python,Perl,Ruby等，同时为MySQL 也设计了一个存储引擎插件。</p>
	<p>官方（英文）：<a href="http://www.sphinxsearch.com/">http://www.sphinxsearch.com/</a></p>
	<p>官方（中文）：<a href="http://www.coreseek.cn/">http://www.coreseek.cn/</a> </p>
</div>
<div class="bk_10"></div>
<form id="setting_sphinx" action="?app=search&controller=setting&action=index" method="POST" >
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
	<caption>Sphinx搜索设置</caption>
	<tr>
		<th>开启服务：</th>
		<td><input type="checkbox"  name="setting[open]" id="open" value="1" <?php if($setting['open']){?> checked="checked" <? }?>/></td>
	</tr>
	<tr>
		<th width="100">主机地址：</th>
		<td><input id="host" type="text" name="setting[host]" value="<?=$setting['host']?>" size="20"/></td>
	</tr>
	<tr>
		<th>端口号：</th>
		<td><input id="port" type="text" name="setting[port]" value="<?=$setting['port']?>" size="10"/> <input type="button" value="连接测试" id="test" class="button_style_1"/></td>
	</tr>
	<tr>
		<th>默认排序：</th>
		<td>
		<label><input class="radio radio_style"  type="radio" name="setting[order]" value="rel" <? if($setting['order'] == 'rel' || empty($setting['order']) ){?> checked="checked"<? }?> /> 相关度 </label>
		<label><input class="radio radio_style"  type="radio" name="setting[order]" value="time" <? if($setting['order'] == 'time'){?> checked="checked"<? }?> /> 时间 </label>
		</td>
	</tr>
	<tr>
		<th>主索引名称：</th>
		<td><input id="index" type="text" name="setting[mainindex]" value="<?=$setting['mainindex']?>" size="10" /> </td>
	</tr>
	<tr>
		<th>增量索引名称：</th>
		<td><input id="addindex" type="text" name="setting[addindex]" value="<?=$setting['addindex']?>" size="10" /> <?=element::tips('如果没有使用增量请留空')?></td>
	</tr>
	<tr>
		<th></th>
		<td valign="middle">
		<input type="submit" id="submit" value=" 保存 " class="button_style_2"/>
		</td>
	</tr>
</table>
</form>
<div class="bk_10"></div>
<script type="text/javascript">
$(function(){
	$("#setting_sphinx").ajaxForm('submit_ok');
	$("#test").click(function(){
		var host = $("#host").val();
		var port = $("#port").val();
		$.ajax({
			type : 'POST',
			url :"?app=search&controller=setting&action=test",
			data :{
				host	:host,
				port	:port
			},
			success : submit_ok,
			dataType : 'json'
		});
	});
});
$('img.tips').attrTips('tips', 'tips_green', 200, 'top');
function submit_ok(data) {
	if(data.state) {
		ct.tips(data.message);
	} else {
		ct.error(data.error);
	}
}
</script>
<?php $this->display('footer', 'system');