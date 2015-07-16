<div class="bk_8"></div>

<form id="system_related_edit" name="system_related_edit" method="POST" class="validator" action="?app=system&controller=related&action=edit">
<input type="hidden" name="rid" id="edit_rid" value="<?=$rid?>" size="50"/>
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
  <tr>
    <th><span class="c_red">*</span> 标题：</th>
    <td><input type="text" name="title" id="edit_title" value="<?=$title?>" size="50"/></td>
  </tr>
  <tr>
    <th><span class="c_red">*</span> 链接：</th>
    <td><input type="text" name="url" id="edit_url" value="<?=$url?>" size="50"/></td>
  </tr>
  <tr>
    <th>缩略图：</th>
    <td><input type="text" name="thumb" id="edit_thumb" value="<?=$thumb?>" size="50"/></td>
  </tr>
  <tr>
    <th>日期：</th>
    <td><input type="text" name="time" id="edit_time" onclick="DatePicker(this,{'format':'yyyy-MM-dd'});" class="input_calendar" value="<?=$time?>" size="15" style="width:65px;" /></td>
  </tr>
</table>
</form>
<script type="text/javascript">
var r = $('#data_<?=$rid?>').val().split('|');
$('#edit_title').val(r[0]);
$('#edit_thumb').val(r[1]);
$('#edit_url').val(r[2]);
$('#edit_time').val(r[3]);
</script>