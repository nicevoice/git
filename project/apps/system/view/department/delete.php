<p class="confirm"><sup>&nbsp;</sup>确定要删除部门：<span style="color:red"><?=$name?></span>吗？</p>
<form method="POST" class="validator" action="?app=system&controller=department&action=<?=$action?>">
<input type="hidden" name="departmentid" value="<?=$departmentid?>" />
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
  <tr>
    <th>处理子部门：</th>
    <td><?=$deal_subdepartment_radio?></td>
  </tr>
  <tr>
    <th>处理此部门人员：</th>
    <td><?=$deal_user_radio?></td>
  </tr>
</table>
</form>