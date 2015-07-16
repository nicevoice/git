<?php $this->display('header', 'system');?>
<div class="bk_8"></div>

<?php 
$this->assign('tab',array('member' => 'class="s_3"'));
$this->display('setting/header', 'member');
?>
<form id="member_setting" method="POST" action="?app=member&controller=setting&action=index">
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
	<caption>注册设置</caption>
	<tr>
		<th width="120">用户注册：</th>
		<td>
		<label><input class="radio radio_style"  type="radio" name="setting[allowreg]" value="1" <? if($setting['allowreg'] == 1){?> checked<? }?> /> 允许 </label>
		<label><input class="radio radio_style"  type="radio" name="setting[allowreg]" value="0" <? if($setting['allowreg'] == 0){?> checked<? }?> /> 禁止 </label>
		</td>
	</tr>
	<tr>
		<th>登录限制：</th>
		<td>登录失败 <input type="text" name="setting[log_max]" size="2" value="<?=$setting['log_max']?>"> 次后，拒绝登录 <input type="text" name="setting[lock_minute]" size="2" value="<?=$setting['lock_minute']?>" /> 分钟</td>
	</tr>
	<tr><th>用户注册需审核：</th><td><input type="checkbox"  name="need_audit" value="1" <? if($setting['default_group'] == 4){?> checked="checked"<? }?>/> 是</td></tr>
	<tr>
		<th><img height="16" width="16" align="absmiddle" tips="每个关键字一行,*为多位通配符,?为单字通配符" class="tips hand" src="images/question.gif"/> 禁用用户名：<br/></th>
		<td><textarea name="setting[ban_name]" class="layout" cols="60" rows="10"><?=$setting['ban_name']?></textarea></td>
	</tr>
	<tr>
		<th>注册协议：</th>
		<td><textarea name="setting[agreement]" class="layout" cols="100" rows="10"><?=$setting['agreement']?></textarea></td>
	</tr>
	<tr>
		<th>关闭注册时显示：</th>
		<td><textarea name="setting[closereason]" class="layout" cols="100" rows="10"><?=$setting['closereason']?></textarea></td>
	</tr>
	<tr><th>&nbsp;</th><td  class="t_c"><input type="submit" class="button_style_2" value="保存" /></td></tr>
</table>
</form>
<script type="text/javascript">
$(function(){
	$('#member_setting').ajaxForm('member_setting_ok');
	$('.tips').attrTips('tips', 'tips_green', 200, 'top');
});
function member_setting_ok(data){
	ct.tips('操作成功');
}
</script>
<?php $this->display('footer', 'system');?>