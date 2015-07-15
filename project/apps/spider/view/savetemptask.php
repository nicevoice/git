<div class="bk_8"></div>
<form method="POST" action="?app=spider&controller=manager&action=addTask">
<table width="390" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
  <tr>
    <th><span class="c_red">*</span> 名称：</th>
    <td><input type="text" name="title" value="" size="50"/></td>
  </tr>
  <tr>
    <th><span class="c_red">*</span> 目标：</th>
    <td><input type="text" name="url" value="<?=$url?>" size="50"/></td>
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
    <th>更新频率：</th>
    <td><input type="text" name="frequency" value="" size="20"/> 秒</td>
  </tr>
</table>
</form>