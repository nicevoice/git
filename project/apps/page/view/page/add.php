<div class="bk_8"></div>
<form method="POST" action="?app=page&controller=page&action=add">
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
  <tr>
    <th><span class="c_red">*</span> 名称：</th>
    <td><input type="text" name="name" id="name" value="<?=$name?>" size="20"/></td>
  </tr>
  <tr>
    <th><span class="c_red">*</span> 模板：</th>
    <td>
    <?=element::template('template', 'template', '', 24);?>
    </td>
  </tr>
  <tr>
    <th><span class="c_red">*</span> 网址：</th>
    <td>
    	<?=element::psn('path', 'path', '', $size = 24, $type = 'file')?>
    	<input type="hidden" name="parentid" value="<?=$parentid?>" />
    </td>
  </tr>
  <tr>
  	<th>更新频率：</th>
    <td>
    	<input type="text" name="frequency" value="3600" size="5" /> 秒 (0表示手动)
    </td>
  </tr>
  <tr>
  	<th>排序</th>
    <td><input name="sort" value="0" size="5" /></td>
  </tr>
</table>
</form>