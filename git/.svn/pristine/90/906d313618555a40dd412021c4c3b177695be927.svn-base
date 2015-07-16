<?php $this->display('header', 'system');?>
<div class="bk_10"></div>
<form id="database_export" action="?app=system&controller=database&action=dobackup" method="GET">
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
	<caption>数据库备份</caption>
	<tr>
		<th width="100">备份方式：</th>
		<td><input type="radio" name="type" id="type_common"  checked="checked"  value="1"/> 普通分卷  <input type="radio" name="type" id="type_shell" value="2"/> MySQL Dump (Shell) </td>
	</tr>
	<tr id="tr_sizelimit">
		<th>分卷大小：</th>
		<td><input id="sizelimit" type="text" name="sizelimit" value="4" size="6"/> M 最大值为<?php echo $memory_limit; ?></td>
	</tr>
	<tr>
		<th></th>
		<td><input type="submit" id="submit" value="开始备份" class="button_style_4"/></td>
	</tr>
</table>
</form>
<script type="text/javascript">
var shell = <?php echo ($shellsupport)?1:0;?>;
var memory_limit = <?php echo intval($memory_limit)?intval($memory_limit):8;?>;

$(function(){
	$('#type_common').click(function(){
		$('#tr_sizelimit').show();
	});
	$('#type_shell').click(function(){
		$('#tr_sizelimit').hide();
	});
	$('#sizelimit').keyup(function(){
		if($(this).val() > memory_limit) {
			ct.error('分卷大小大于php可使用的内存，可能导出分卷错误');
		}
	});
	var operation_options = {
		beforeSubmit:  function(){
			ct.ok('备份开始....');
		},
		success:   function(data){
			if(data.state) {
				if(data.volume) {
					//分卷未完 继续请求
					ct.ok(data.message);
					$.getJSON(
						data.nexturl,
						operation_options.success
					);
				} else {
					ct.ok(data.message);
				}
			} else {
				ct.error(data.error);
			}
		},
		dataType: 'json'
	};
	var f = $('#database_export').submit(function(){
		f.ajaxSubmit(operation_options);
		return false;
	});
	if(!shell) {
		$('#type_shell').attr('disabled','disabled');
	}
});
</script>
<?php $this->display('footer', 'system');?>