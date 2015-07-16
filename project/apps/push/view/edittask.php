<div class="bk_8"></div>
<form method="POST" action="?app=push&controller=push&action=editTask">
<input type="hidden" name="taskid" value="<?=$taskid?>" />
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
  <tr>
    <th width="60"><span class="c_red">*</span> 名称：</th>
    <td><input type="text" name="title" value="<?=$title?>" size="50"/></td>
  </tr>
  <tr>
    <th><span class="c_red">*</span> 规则：</th>
    <td><?=$rule_dropdown?></td>
  </tr>
  <tr>
    <th>栏目：</th>
    <td><?=element::category('catid','catid',$catid)?></td>
  </tr>
  <tr>
    <th>SQL条件语句：</th>
    <td><input type="text" name="extra_condition" value="<?=$extra_condition?>" size="50"/></td>
  </tr>
</table>
</form>