<?php $this->display('header', 'system');?>
<div class="bk_10"></div>
<form id="setting_edit_optimize" action="?app=system&controller=setting&action=edit" method="POST" class="validator">
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
	<caption>基本设置</caption>
	<tr>
		<th width="150">开启页面Gzip压缩：</th>
		<td><input type="radio" name="setting[gzip]" value="1" class="radio" <?php if ($gzip) echo 'checked';?>/>是 &nbsp;&nbsp;<input type="radio" name="setting[gzip]" value="0" class="radio" <?php if (!$gzip) echo 'checked';?>>否</td>
	</tr>
	<tr>
		<th>在线保持时间：</th>
		<td><input id="onlinehold" type="text" name="setting[onlinehold]" value="<?=$onlinehold?>" size="5"/> 分钟</td>
	</tr>
	<tr>
		<th>滚动更新每次生成天数：</th>
		<td><input id="rolldays" type="text" name="setting[rolldays]" value="<?=$rolldays?>" size="5"/> 天</td>
	</tr>
	<tr>
		<th>栏目列表页生成页数：</th>
		<td><input id="listpages" type="text" name="setting[listpages]" value="<?=$listpages?>" size="5"/> 页</td>
	</tr>
	<tr>
		<th>列表页每页信息数：</th>
		<td><input id="pagesize" type="text" name="setting[pagesize]" value="<?=$pagesize?>" size="5"/> 条</td>
	</tr>
</table>
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
	<caption>缓存设置</caption>
	<tr>
		<th width="150">开启动态页面缓存：</th>
		<td><input type="radio" name="setting[pagecached]" value="1" class="radio" <?php if ($pagecached) echo 'checked';?>/>是 &nbsp;&nbsp;<input type="radio" name="setting[pagecached]" value="0" class="radio" <?php if (!$pagecached) echo 'checked';?>>否</td>
	</tr>
	<tr>
		<th>缓存有效期：</th>
		<td><input id="pagecachettl" type="text" name="setting[pagecachettl]" value="<?=$pagecachettl?>" size="5"/> 秒</td>
	</tr>
	<tr>
		<th></th>
		<td valign="middle"><input type="submit" id="submit" value=" 保存 " class="button_style_2"/></td>
	</tr>
</table>
</form>
<div class="bk_10"></div>
<script type="text/javascript">
$(function(){
	$('#setting_edit_optimize').ajaxForm(function(json){
		if(json.state) ct.tips(json.message);
		else ct.error(json.error);
	});
});
</script>
<?php $this->display('footer', 'system');