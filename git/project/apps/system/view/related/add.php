<div class="bk_8"></div>

<form id="system_related_add" name="system_related_add" method="POST" class="validator" action="?app=system&controller=related&action=add">
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
  <tr>
    <th><span class="c_red">*</span> 标题：</th>
    <td><input type="text" name="title" id="title" value="<?=$title?>" size="50"/></td>
  </tr>
  <tr>
    <th><span class="c_red">*</span> 链接：</th>
    <td><input type="text" name="url" id="url" value="<?=$url?>" size="50"/></td>
  </tr>
  <tr>
    <th>缩略图：</th>
   <td><input type="text" name="thumb" id="thumb" value="<?=$thumb?>" size="50"/></td>
  </tr>
  <tr>
    <th>日期：</th>
    <td><input type="text" name="time" id="time" onclick="DatePicker(this,{'format':'yyyy-MM-dd'});" class="input_calendar" value="<?=$time?>" size="15" style="width:65px;" /></td>
  </tr>
</table>
</form>