<?php $this->display('header','system'); ?>
<script type="text/javascript" src="apps/spider/js/rule_edit.js"></script>
<link rel="stylesheet" type="text/css" href="apps/spider/css/rule.css" />
<div class="bk_10"></div>
<div class="suggest mar_l_8" style="width:98%">
	<h2>友情提示</h2>
	<p>本采集功能是通过远程获取目标网页内容，经过本地规则解析处理后添加到本地数据库。</p>
	<p>在设置本页之前，您需要基本的WEB前端知识</p>
	<p>在此，需要根据采集目标的页面设置相应的网页地址匹配和内容处理规则，请注意代码的严谨，设置完成后，请先测试无误后再保存。</p>
	<p>加红色"*"号的为必填项</p>
</div>
<div class="bk_8"></div>
<form id="ruleForm" name="rule_add" action="?app=spider&controller=manager&action=editrule" method="POST">
<input type="hidden" value="<?=$ruleid?>" name="ruleid" />
<table class="table_form mar_l_8" width="98%" border="0" cellpadding="0" cellspacing="0">
	<caption>基本设置</caption>
	<tr>
		<th width="150"><span class="c_red">*</span> 所属网站：</th>
		<td width="550">
			<?=$sitedropdown?>
			<a href="javascript:App.addSite();">添加</a>
		</td>
		<td class="c_gray">&nbsp;</td>
	</tr>
	<tr>
		<th><span class="c_red">*</span> 规则名：</th>
		<td><input type="text" name="name" style="width:527px" value="<?=$name?>"></td>
		<td class="c_gray">&nbsp;</td>
	</tr>
	<tr>
		<th><b class="c_red">(1)</b> <span class="c_red">*</span> 规则匹配：</th>
		<td><input type="text" name="enterPattern" style="width:527px" value="<?=$enter_rule?>"></td>
		<td class="c_gray">支持通配符“(*)”</td>
	</tr>
	<tr>
		<th><b class="c_red">(2)</b> 列表所在页测试网址：</th>
		<td>
			<input type="text" style="width:400px" name="listUrl" value="<?=$list_rule['listUrl']?>" />
			&nbsp;&nbsp;<button type="button" class="button_style_1" onclick="App.testEnterRule()" >测试以上规则</button>
		</td>
		<td class="c_gray">根据(2)的确定网址测试(1)的规则是否可用</td>
	</tr>
	<tr>
		<th><b class="c_red">(3)</b> 页面编码：</th>
		<td>
			<label><input class="radio_style" name="charset" <?=strtolower($charset)=='gbk' ? 'checked="checked"' : ''?> value="GBK" type="radio"> GBK</label>
			<label><input class="radio_style" name="charset" <?=strtolower($charset)=='utf-8' ? 'checked="checked"' : ''?> value="UTF-8" type="radio"> UTF-8</label>
		</td>
		<td class="c_gray">&nbsp;</td>
	</tr>
	<tr>
		<th>备注：</th>
		<td><textarea style="width:527px;" name="description"><?=htmlspecialchars($description)?></textarea></td>
		<td class="c_gray">&nbsp;</td>
	</tr>
</table>
<table class="table_form mar_l_8" width="98%" border="0" cellpadding="0" cellspacing="0">
	<caption>列表设置</caption>
	<tr>
		<th width="150">列表页类型：</th>
		<td width="550">
			<label><input type="radio" name="listType" value="0" <?=$list_rule['listType']==0 ? 'checked="checked"' : ''?> /> HTML</label>
			<label><input type="radio" name="listType" value="1" <?=$list_rule['listType']==1 ? 'checked="checked"' : ''?> /> RSS</label>
		</td>
		<td class="c_gray">&nbsp;</td>
	</tr>
	<tbody <?=$list_rule['listType']==1 ? 'style="display:none"' : ''?>>
		<tr>
			<th><b class="c_red">(4)</b> 范围：</th>
			<td>
				从 <textarea name="listStart" class="offsetTag"><?=htmlspecialchars($list_rule['listStart'])?></textarea>
				<span class="split">到</span>
				<textarea name="listEnd" class="offsetTag"><?=htmlspecialchars($list_rule['listEnd'])?></textarea>
			</td>
			<td class="c_gray">设置范围提高准确度，内容网址列表将在此范围内提取，列表页类型为HTML起作用</td>
		</tr>
		<tr>
			<th><b class="c_red">(5)</b> <span class="c_red">*</span> 内容网址规则：</th>
			<td>
				<input type="text" name="urlPattern" style="width:527px;" value="<?=$list_rule['urlPattern']?>" />
			</td>
			<td class="c_gray">支持通配符“(*)”</td>
		</tr>
		<tr>
			<th><b class="c_red">(6)</b> 条数限制：</th>
			<td>
				<input type="text" name="listLimitLength" style="width:527px" value="<?=$list_rule['listLimitLength']?>" />
			</td>
			<td class="c_gray">&nbsp;</td>
		</tr>
		<tr>
			<th><b class="c_red">(7)</b> 下一页：</th>
			<td>
				<input type="text" name="listNextPage" style="width:527px" value="<?=htmlspecialchars($list_rule['listNextPage'])?>" />
			</td>
			<td class="c_gray">正则表达式，格式<b class="c_red">/expresion/</b></td>
		</tr>
	</tbody>
	<tr>
		<th><b class="c_red">(8)</b> 内容测试网址：</th>
		<td>
			<input type="text" name="contentUrl" style="width:400px;" value="<?=$content_rule['contentUrl']?>" />&nbsp;&nbsp;<button type="button" class="button_style_1" <?=$list_rule['listType']==1 ? 'style="display:none"' : ''?> onclick="App.testListRule()" >测试以上规则</button>
		</td>
		<td class="c_gray">根据(8)的网址测试(5)的规则是否可用</td>
	</tr>
	<tr>
		<th>&nbsp;</th>
		<td>
			<button class="button_style_1" type="button" onclick="App.testGetList()" >获取列表测试</button>
			&nbsp;&nbsp;<span class="c_gray">依赖于项(2)、(3)、(4)、(5)、(6)、(7)</span>
		</td>
		<td class="c_gray">&nbsp;</td>
	</tr>
</table>
<table class="table_form mar_l_8" width="98%" border="0" cellpadding="0" cellspacing="0">
	<caption>内容设置</caption>
	<tr>
		<th width="150"><b class="c_red">(9)</b> 范围：</th>
		<td width="550">
			从 <textarea name="rangeStart" class="offsetTag"><?=htmlspecialchars($content_rule['rangeStart'])?></textarea>
			<span class="split">到</span>
			<textarea name="rangeEnd" class="offsetTag"><?=htmlspecialchars($content_rule['rangeEnd'])?></textarea>
		</td>
		<td class="c_gray">设置范围可提高准确度及提取速度，以下“标题”，“正文”，“作者”，“来源”，“发布时间”，“下一页”均包含在此范围中</td>
	</tr>
	<tr>
		<th><b class="c_red">(10)</b> <span class="c_red">*</span> 标题：</th>
		<td>
			从 <textarea name="titleStart" class="offsetTag"><?=htmlspecialchars($content_rule['titleStart'])?></textarea>
			<span class="split">到</span>
			<textarea name="titleEnd" class="offsetTag"><?=htmlspecialchars($content_rule['titleEnd'])?></textarea>
		</td>
		<td class="c_gray">&nbsp;</td>
	</tr>
	<tr>
		<th><b class="c_red">(11)</b> <span class="c_red">*</span> 正文：</th>
		<td>
			从 <textarea name="contentStart" class="offsetTag"><?=htmlspecialchars($content_rule['contentStart'])?></textarea>
			<span class="split">到</span>
			<textarea name="contentEnd" class="offsetTag"><?=htmlspecialchars($content_rule['contentEnd'])?></textarea>
		</td>
		<td class="c_gray">&nbsp;</td>
	</tr>
	<tr>
		<th><b class="c_red">(12)</b> 作者：</th>
		<td>
			从 <textarea name="authorStart" class="offsetTag"><?=htmlspecialchars($content_rule['authorStart'])?></textarea>
			<span class="split">到</span>
			<textarea name="authorEnd" class="offsetTag"><?=htmlspecialchars($content_rule['authorEnd'])?></textarea>
		</td>
		<td class="c_gray">&nbsp;</td>
	</tr>
	<tr>
		<th><b class="c_red">(13)</b> 来源：</th>
		<td>
			从 <textarea name="sourceStart" class="offsetTag"><?=htmlspecialchars($content_rule['sourceStart'])?></textarea>
			<span class="split">到</span>
			<textarea name="sourceEnd" class="offsetTag"><?=htmlspecialchars($content_rule['sourceEnd'])?></textarea>
		</td>
		<td class="c_gray">&nbsp;</td>
	</tr>
	<tr>
		<th><b class="c_red">(14)</b> 发布时间：</th>
		<td>
			从 <textarea name="pubdateStart" class="offsetTag"><?=htmlspecialchars($content_rule['pubdateStart'])?></textarea>
			<span class="split">到</span>
			<textarea name="pubdateEnd" class="offsetTag"><?=htmlspecialchars($content_rule['pubdateEnd'])?></textarea>
		</td>
		<td class="c_gray">&nbsp;</td>
	</tr>
	<tr>
		<th><b class="c_red">(15)</b> 下一页：</th>
		<td>
			<input type="text" style="width:527px" name="nextPage" value="<?=htmlspecialchars($content_rule['nextPage'])?>" />
		</td>
		<td class="c_gray">正则表达式，格式<b class="c_red">/expresion/</b></td>
	</tr>
	<tr>
		<th><b class="c_red">(16)</b> 标签保留：</th>
		<td><input type="text" style="width:527px" name="allowTags" value="<?=$content_rule['allowTags']?>" /></td> 
		<td class="c_gray">多个以半角逗号分割</td>
	</tr>
	<tr>
		<th><b class="c_red">(17)</b> 保存图片：</th>
		<td>
			<input type="checkbox" name="saveRemoteImg" <?=$content_rule['saveRemoteImg']?'checked="checked"':''?> />
		</td>
		<td class="c_gray">本地化远程图片到本地</td>
	</tr>
	<tbody id="replacement">
		<?php
		$rl = count($content_rule['replacement']['source']);
		if ($rl):
		?>
		<tr>
			<th><b class="c_red">(18)</b>内容替换：</th>
			<td>
				将 <textarea name="replacement[source][]" class="offsetTag"><?=htmlspecialchars($content_rule['replacement']['source'][0])?></textarea>
				<span class="split">替换为</span>
				<textarea name="replacement[target][]" class="offsetTag"><?=htmlspecialchars($content_rule['replacement']['target'][0])?></textarea>
			</td>
			<td></td>
		</tr>
		<?php for ($i=1;$i<$rl;$i++):?>
		<tr>
			<th>&nbsp;</th>
			<td>
				将 <textarea name="replacement[source][]" class="offsetTag"><?=htmlspecialchars($content_rule['replacement']['source'][$i])?></textarea>
				<span class="split">替换为</span>
				<textarea name="replacement[target][]" class="offsetTag"><?=htmlspecialchars($content_rule['replacement']['target'][$i])?></textarea>
			</td>
			<td></td>
		</tr>
		<?php endfor; else:?>
		<tr>
			<th><b class="c_red">(18)</b>内容替换：</th>
			<td>
				将 <textarea name="replacement[source][]" class="offsetTag"></textarea>
				<span class="split">替换为</span>
				<textarea name="replacement[target][]" class="offsetTag"></textarea>
			</td>
			<td></td>
		</tr>
		<?php endif;?>
	</tbody>
	<tr>
		<th>&nbsp;</th>
		<td>
			<button class="button_style_1" type="button" onclick="App.testGetDetail()" >获取内容测试</button>
			&nbsp;&nbsp;<span class="c_gray">依赖于项(3)、(8)、(9)、(10)、(11)、(12)、(13)、(14)、(15)、(16)、(17)、(18)</span>
		</td>
		<td class="c_gray">&nbsp;</td>
	</tr>
	<tr>
		<th>&nbsp;</th>
		<td>
			<input type="submit" value="保存" class="button_style_2" />
		</td>
		<td class="c_gray">&nbsp;</td>
	</tr>
</table>
</form>
<script type="text/javascript">
App.init();
</script>
</body>
</html>