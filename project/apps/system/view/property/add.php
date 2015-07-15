<form name="add" id="property_add" method="POST" action="?app=system&controller=property&action=add">
<input name="parentid" type="hidden" value="<?=$proid?>"/>
<table border="0" cellspacing="0" cellpadding="0" class="table_form yyss">
  <tr>
    <th width="120">属性名：</th>
    <td><input type="text" name="name" id="name" size="20"/></td>
  </tr>
  <tr>
  	<th>描述：</th>
  	<td>&nbsp;<textarea name="description" cols="60" rows="3"></textarea></td>
  </tr>
  <tr>
  	<th>排序：</th>
  	<td>&nbsp;<input type="text" name="sort" value="0" size="3" maxlength="2"/> 值越大排序越靠后</td>
  </tr>
  <tr>
  	<th></th>
  	<td><input type="submit" value="保存" class="button_style_2"/></td>
  </tr>
</table>
</form>

<script type="text/javascript">
$('#property_add').ajaxForm('property.add_submit');
</script>