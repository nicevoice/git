<div class="bk_8"></div>
<form name="model_add" id="model_add" action="?app=system&controller=model&action=add" method="POST" style="border:0px solid;">
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
  <tr>
    <th>名称：</th>
    <td><input type="text" name="name" size="20" value="<?=$name?>"/></td>
  </tr>
  <tr>
    <th>英文名：</th>
    <td><input type="text" name="alias" size="20" value="<?=$alias?>"/></td>
  </tr>
  <tr>
    <th>描述：</th>
    <td><input type="text" name="description" size="50" value="<?=$description?>"/></td>
  </tr>
  <tr>
    <th>列表页模板：</th>
    <td><?=element::template('template_list', 'template_list', $template_list, 40)?></td>
  </tr>
  <tr>
    <th>内容页模板：</th>
    <td><?=element::template('template_show', 'template_show', $template_show, 40)?></td>
  </tr>
  <tr>
    <th>禁用：</th>
    <td><input type="checkbox" name="disabled" value="1" class="checkbox_style" <?=($disabled ? 'checked' : '')?>/></td>
  </tr>
</table>
</form>