<form method="POST" action="?app=page&controller=section&action=property">
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
  <tr>
    <th width="72"><span class="c_red">*</span> 名称：</th>
    <td>
    	<input type="text" name="name" value="<?=$section['name']?>" maxlength="30" size="20"/>
    	<input type="hidden" name="sectionid" value="<?=$section['sectionid']?>" />
    </td>
  </tr>
  <tr>
  	<th>更新频率：</th>
    <td><input type="text" name="frequency" size="5" value="<?=$section['frequency']?>" /> 秒 （0表示手动）</td>
  </tr>
  <tr>
  	<th>宽度：</th>
    <td>
    	<input type="text" name="width" value="<?=$section['width']?>" size="5" /> px
    </td>
  </tr>
  <tr>
  	<th>备注：</th>
    <td><textarea name="description" style="width:345px;height:30px;padding:0;margin:0;"><?=$section['description']?></textarea></td>
  </tr>
</table>
</form>