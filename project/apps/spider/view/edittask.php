<div class="bk_8"></div>
<form method="POST" action="?app=spider&controller=manager&action=editTask">
<input type="hidden" name="taskid" value="<?=$taskid?>" />
<table width="390" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
  <tr>
    <th><span class="c_red">*</span> 名称：</th>
    <td><input type="text" name="title" value="<?=$title?>" size="50"/></td>
  </tr>
  <tr>
    <th><span class="c_red">*</span> 目标：</th>
    <td><input type="text" name="url" value="<?=$url?>" size="50"/></td>
  </tr>
  <tr>
    <th><span class="c_red">*</span> 规则：</th>
    <td><?=$rule_dropdown?>&nbsp;&nbsp;<a href="javascript:ct.assoc.open('?app=spider&controller=manager&action=addrule','newtab')">添加规则</a></td>
  </tr>
  <tr>
    <th>默认栏目：</th>
    <td><?=element::category('catid','catid',$catid)?></td>
  </tr>
  <tr>
    <th>更新频率：</th>
    <td><input type="text" name="frequency" value="<?=$frequency?>" size="20"/> 秒</td>
  </tr>
</table>
</form>