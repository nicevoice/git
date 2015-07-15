<?php
$rules	= 0;
?>
<div class="bk_8"></div>
<form name="rules_add" method="POST" action="?app=cdn&controller=rules&action=edit&id=<?=$id?>">
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
  <tr>
    <th><span class="c_red"></span> CDN：</th>
    <td><input type="text" name="cdnid" value="<?=$cdnid?>" size="2" readonly/></td>
  </tr>
   <tr>
    <th><span class="c_red"></span> 路径:</th>
    <td><input type="text" name="path" value="<?=$path?>" /></td>
  </tr>
  <tr>
    <th><span class="c_red"></span> URL:</th>
    <td><input type="text" name="url" value="<?=$url?>" /></td>
  </tr>
</table>
</form>