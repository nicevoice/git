<form action="?app=system&controller=department&action=<?=$action?>" method="post">
<input type="hidden" name="departmentid" value="<?=$departmentid?>" />
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_10">
  <tr>
    <th><span class="c_red">*</span> 名称：</th>
    <td><input type="text" name="name" value="<?=$name?>" size="30"/></td>
  </tr>
  <?php if ($action == 'edit' || $_departmentid != $departmentid):?>
  <tr>
  	<th>主管角色：</th>
    <td><?=element::role_dropdown('leaderid', $leaderid, $departmentid)?></td>
  </tr>
  <tr>
    <th>上级部门：</th>
    <td><?=element::department_dropdown('parentid',$parentid)?></td>
  </tr>
  <?php endif;?>
</table>
</form>