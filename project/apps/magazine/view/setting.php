<?php $this->display('header', 'system');?>
<!--- Psn 相关-->
<script type="text/javascript" src="apps/system/js/psn.js"></script>

<div class="bk_10"></div>
<form id="magazine_setting" action="?app=magazine&controller=setting&action=index" method="POST">
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
	<caption>杂志设置</caption>
	<tr>
		<th width="120">发布路径：</th>
		<td colspan="2">
            <?=element::psn('path', 'path', $setting['path'], $size = 35)?>
        </td>
	</tr>
	<tr>
		<th></th>
		<td colspan="2" valign="middle"><br/>
		<input type="submit" id="submit" value="保存" class="button_style_2"/>
	</td>
	</tr>
</table>
</form>
<script type="text/javascript">
$(function(){
	$("#path").click(function(){
		if($("#isgender").attr("checked")==true) {
			$("#isgender").attr("checked",false);
		}
	});
	$('#magazine_setting').ajaxForm(function(json){
        if(json.state)
        {
            ct.ok(json.message);
        }else{
            ct.error(json.error);
        }
    },null,function(){
        if($('#path').val() == '')
        {
            ct.error('发布路径不能为空');
            return false;
        }
    });
});
</script>
<?php $this->display('footer', 'system');?>
