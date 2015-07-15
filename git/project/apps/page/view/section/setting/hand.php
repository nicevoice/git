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
  	<th><span class="c_red">*</span> 模板代码：</th>
    <td>
    	<textarea name="data" class="code" wrap="off" style="overflow-x:auto;width:345px;height:100px;"><?=htmlspecialchars($section['data'])?></textarea>
    </td>
  </tr>
  <tr>
  	<th>输出格式：</th>
    <td>
        <?php $output = explode(',',$section['output']);?>
    	<label><input type="checkbox" class="checkbox_style" name="output[]" value="html" <?=in_array('html',$output)?'checked="checked" ':''?>/>html</label>
        <label><input type="checkbox" class="checkbox_style" name="output[]" value="xml" <?=in_array('xml',$output)?'checked="checked" ':''?>/>xml</label>
        <label><input type="checkbox" class="checkbox_style" name="output[]" value="json" <?=in_array('json',$output)?'checked="checked" ':''?>/>json</label>
    </td>
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