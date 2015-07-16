<?php $this->display('header', 'system');?>


<style>
.table_info th {
    text-align: center;
}.table_info td {
    border: 1px solid #D0E6EC !important;
    border-width: 1px 0 0 1px;
    text-align: center;
}
</style>
<div class="bk_10"></div>
<form  id="release_setting" name="release_setting" action="?app=<?=$app?>&controller=<?=$controller?>&action=release" method="POST">
<table class="table_form mar_l_8" cellpadding="0" cellspacing="0" width="98%">
	<caption>APP版本号设置</caption>
    <tr>
        <th>版本号：</th>
        <td><input type="text" name="setting[release]" value="<?=$setting['release']?>" /></td>
    </tr>
	<tr>
		<th>&nbsp;</th>
		<td>
			<input type="submit" class="button_style" value="保    存"/>
		</td>
	</tr>  

</table>
</form>
<script type="text/javascript">
	$(function(){
		$('#release_setting').ajaxForm('submit_ok');
	});
	function submit_ok(json) {
		if(json.state) ct.ok(json.message);
		else ct.error(json.error);
	}
</script>
<?php $this->display('footer', 'system');?>