<?php $this->display('header', 'system');?>
<div class="bk_10"></div>
<form id="contribution_setting" action="?app=contribution&controller=setting&action=index" method="POST">
	<table class="table_form mar_l_8" cellpadding="0" cellspacing="0" width="98%">
	<caption>稿件配置</caption>
	<tr>
		<th width="120">允许游客投稿：</th>
		<td><input type="radio" id="iscontribute" value="1" name="setting[iscontribute]" > 是 <input type="radio" value="0" name="setting[iscontribute]"> 否 </td>
	</tr>
	<tr>
		<th width="120">开启验证码：</th>
		<td><input type="radio" id="isseccode" value="1" name="setting[isseccode]" > 是 <input type="radio" value="0" name="setting[isseccode]"> 否 </td>
	<tr>
		<th></th>
		<td valign="middle"><input type="submit" id="submit" value="保存" class="button_style_2"/></td>
	</tr>
	</table>
</form>
<script type="text/javascript">
function submit_ok(data) {
	if(data.state) ct.tips("保存成功");
	else ct.error(data.error);
}
var iscontribute = <?php echo intval($setting['iscontribute']); ?>;
var isseccode = <?php echo intval($setting['isseccode']); ?>;
$(function(){
	$('input#iscontribute[value="'+iscontribute+'"]').attr('checked',true);
	$('input#isseccode[value="'+isseccode+'"]').attr('checked',true);
	$('#contribution_setting').ajaxForm('submit_ok');
	
});
</script>
<?php $this->display('footer', 'system');?>