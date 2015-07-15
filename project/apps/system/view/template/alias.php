<div class="bk_8"></div>
<form name="template_alias" id="template_alias" method="POST" action="?app=system&controller=template&action=alias">
<input type="hidden" name="path" value="<?=$path?>" />
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
  <tr>
    <th>别名：</th>
    <td><input type="text" name="alias" id="alias" value="<?=$alias?>" size="15" /></td>
  </tr>
  <tr>
    <th></th>
    <td>可输入中文，描述模板用途</td>
  </tr>
</table>
</form>