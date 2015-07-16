<?php
$rules	= 0;
?>
<div class="bk_8"></div>
<form name="rules_add" id="rules_add" method="POST" action="?app=cdn&controller=rules&action=add">
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
  <tr>
    <th><span class="c_red"></span> CDN：</th>
    <td><input type="text" name="cdnid" value="" size="2" readonly/></td>
  </tr>
  <tr>
    <th><span class="c_red"></span> 路径:</th>
    <td><input type="text" name="path" value="" /></td>
  </tr>
  <tr>
    <th><span class="c_red"></span> URL:</th>
    <td><input type="text" name="url" value="" /></td>
  </tr>
</table>
</form>
<script type="text/javascript">
$('input[name="cdnid"]').val(cdnid);
</script>