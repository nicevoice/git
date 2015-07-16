<div class="bk_8"></div>
<form name="cdn_add" id="cdn_add" method="POST" action="?app=cdn&controller=cdn&action=add">
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form" id="add_table">
  <tr>
    <th><span class="c_red">*</span> CDN名称：</th>
    <td><input type="text" name="name" id="name" value="" size="20"/></td>
  </tr>
  <tr>
    <th><span class="c_red">*</span> 类型：</th>
    <td>
		<select name="tid">
			<option value=''>请选择</option>
			<?php foreach($type as $item):?>
			<option value="<?=$item['tid']?>" par='<?=$item["parameter"]?>'><?=$item['name']?></option>
			<?php endforeach;?>
		</select>
	</td>
  </tr>
</table>
</form>
<script type="text/javascript">
var select = $("select[name='tid']");
function selectChange() {
	var par	= eval('('+select.find(':selected').attr('par')+')');
	if (!par) return;
	$("tr.par").remove();
	$.each(par, function(k,v) {
		var s	= '<tr class="par"><th>'+v+'</th><td><input type="text" name="par['+k+']" value="" size="20"/></td></tr>';
		$("#add_table").append(s);
	});
}
$(document).ready(function() {
	select.change(selectChange);
});
</script>