<?php $this->display('header', 'system');?>
<div class="bk_10"></div>
<div class="suggest w_650 mar_l_8">
	<h2>友情提示</h2>
	<p>
        <strong>标题重复检测说明</strong><br />
        不检测：不对重复内容进行提示<br />
	    手动检测：在标题输入框后显示检测按钮，需手工点击触发检测<br />
	    自动检测：当输入标题时即会自动触发检测，类似百度输入提示框<br />
	    <br />
	    注：要开启重复检测需要事先配置好 “扩展-搜索”中的coreseek服务。
    </p>
</div>
<div class="bk_10"></div>
<form id="setting_edit_content" action="?app=system&controller=setting&action=edit" method="POST">
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
	<caption>发稿设置</caption>
    <tr>
        <th width="150">权重提示：</th>
        <td><textarea name="setting[weight]" rows="6" cols="50" class="bdr"><?=$weight?></textarea></td>
    </tr>
    <tr>
        <th>默认权重值：</th>
        <td><input type="text" name="setting[defaultwt]" value="<? if($defaultwt) echo $defaultwt; else echo '60';?>" size="10"/>&nbsp;&nbsp;（0-100）</td>
    </tr>
	<tr>
		<th>标题重复检测：</th>
		<td>
			<label><input type="radio" name="setting[repeatcheck]" value="0" size="80"<?php if($repeatcheck == 0):?> checked="checked"<?php endif;?> />不检测</label>
			<label><input type="radio" name="setting[repeatcheck]" value="1" size="80"<?php if($repeatcheck == 1):?> checked="checked"<?php endif;?>/>手动检测</label>
			<label><input type="radio" name="setting[repeatcheck]" value="2" size="80"<?php if($repeatcheck == 2):?> checked="checked"<?php endif;?>/>自动检测</label>
		</td>
	</tr>
	<tr>
		<th></th>
		<td><input id="submit" class="button_style_2" type="submit" value="保存" /></td>
	</tr>
</table>
</form>
<script type="text/javascript">
$(function(){
	$('#setting_edit_content').ajaxForm(function(json){
		if(json.state) ct.tips(json.message);
		else ct.error(json.error);
	});
});
</script>
<?php $this->display('footer', 'system');