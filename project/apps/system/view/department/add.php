<form action="?app=system&controller=department&action=<?=$action?>" method="post">
<input type="hidden" name="parentid" value="<?=$parentid?>" />
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_t_10">
  <tr>
    <th><span class="c_red">*</span> 名称：</th>
    <td><input type="text" name="name" value="" size="30"/></td>
  </tr>
  <tr>
    <th>上级部门：</th>
    <td><?=element::department_dropdown('parentid',$parentid)?></td>
  </tr>
</table>
</form>