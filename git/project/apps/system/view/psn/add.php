<div class="bk_8"></div>
<form name="psn_add" id="psn_add" method="POST" action="?app=system&controller=psn&action=add">
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
  <tr>
    <th><span class="c_red">*</span> 节点名称：</th>
    <td><input type="text" name="name" id="name" value="<?=$name?>" size="20"/></td>
  </tr>
  <tr>
    <th>发布路径：</th>
    <td><input type="text" name="path" value="<?=$path?>" size="40"/></td>
  </tr>
  <tr>
    <th>发布网址：</th>
    <td><input type="text" name="url" value="<?=$url?>" size="40"/></td>
  </tr>
  <tr>
      <th>改写规则：</th>
      <td><?=form_element::select(array('id' => 'rewrite', 'name' => 'rewrite', 'value' => $rewrite, 'options' => $rulers))?></td>
  </tr>
  <tr>
    <th>排序：</th>
    <td><input type="text" name="sort" value="<?=$sort?>" size="3"/></td>
  </tr>
</table>
</form>