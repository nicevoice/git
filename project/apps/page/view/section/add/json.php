<div class="bk_8"></div>
<form method="POST" action="?app=page&controller=section&action=add">
<table width="94%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
  <tr>
    <th width="72">类型：</th>
    <td>
    	<input type="radio" id="type_html" name="type" class="radio_style" value="html" /><label for="type_html" class="html">代码</label>
        <input type="radio" id="type_auto" name="type" class="radio_style" value="auto" /><label for="type_auto" class="auto">自动</label>
        <input type="radio" id="type_hand" name="type" class="radio_style" value="hand" /><label for="type_hand" class="hand">手动</label>
        <input type="radio" id="type_feed" name="type" class="radio_style" value="feed" /><label for="type_feed" class="feed">RSS</label>
        <input type="radio" id="type_json" name="type" class="radio_style" value="json" checked="checked" /><label for="type_json" class="json">JSON</label>
        <input type="radio" id="type_rpc" name="type" class="radio_style" value="rpc" /><label for="type_rpc" class="rpc">RPC</label>
    </td>
  </tr>
  <tr>
    <th><span class="c_red">*</span> 名称：</th>
    <td>
    	<input type="text" name="name" value="" maxlength="30" size="20" />
    	<input type="hidden" name="pageid" value="<?=$pageid?>" />
    </td>
  </tr>
  <tr>
  	<th><span class="c_red">*</span> JSON 源：</th>
    <td><input type="text" name="url" size="64" value="" /></td>
  </tr>
  <tr>
  	<th><span class="c_red">*</span> 模板代码：</th>
    <td>
    	<textarea name="data" class="code" wrap="off" style="width:370px;"><?=$data?></textarea>
    </td>
  </tr>
  <tr>
  	<th>更新频率：</th>
    <td>
    	<input type="text" name="frequency" value="0" size="5" /> 秒 （0表示手动）
    </td>
  </tr>
  <tr>
  	<th>宽度：</th>
    <td>
    	<input type="text" name="width" value="" size="5" /> px
    </td>
  </tr>
  <tr>
  	<th>备注：</th>
    <td><textarea name="description" style="width:370px;height:30px;padding:0;margin:0;"></textarea></td>
  </tr>
</table>
</form>