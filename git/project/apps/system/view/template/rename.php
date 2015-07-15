<div class="bk_8"></div>
<form name="template_rename" id="template_rename" method="POST" action="?app=system&controller=template&action=rename">
<input type="hidden" name="type" value="<?=$type?>" />
<input type="hidden" name="path" value="<?=$path?>" />
<input type="hidden" name="dir" value="<?=$dir?>" />
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
  <tr>
    <th>重命名：</th>
    <td><input type="text" name="name" id="name" value="<?=$name?>" size="15" /></td>
  </tr>
  <tr>
    <th>描述：</th>
    <td><input type="text" name="note" id="note" value="<?=$note?>" size="30" /></td>
  </tr>
</table>
</form>