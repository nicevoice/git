<form id="text_add" method="post" action="?app=<?=$app?>&controller=<?=$controller?>&action=<?=$action?>">
	<input type="hidden" name="type" value="<?=$type?>" />
	<table border="0" cellspacing="0" cellpadding="0" class="table_form">
	  <tr>
		<th width="80"><span class="c_red">*</span> 标题：</th>
		<td><input type="text" name="subject" id="subject" class="bdr inputtit_focus" value="<?=$subject?>" size="100"/> </td>
	  </tr>
        <tr>
            <th width="70"><span class="c_red">*</span> 科目：</th>
            <td><?=property_once('subjectid','subjectid',$pro_ids['subjectid'],$subjectid)?> </td>
        </tr>
        <tr>
            <th width="70"><span class="c_red">*</span> 考点：</th>
            <td><?=property_once('knowledgeid','knowledgeid',$pro_ids['knowledgeid'],$knowledgeid)?> </td>
        </tr>
        <tr>
            <th width="70"><span class="c_red">*</span> 类型：</th>
            <td><?=property_once('qtypeid','qtypeid',$pro_ids['qtypeid'],$qtypeid)?> </td>
        </tr>
        <tr>
            <th width="70"><span class="c_red">*</span> 来源：</th>
            <td><input type="text" name="source" value="<?=$source?>"></td>
        </tr>
	  <tr>
		<th>图片：</th>
		<td><table border="0" cellspacing="2" cellpadding="0"><tr><td><input type="text" name="image" id="image" size="30" value="<?=$image?>" /></td><td><span id="uploadimage" class="uploader"></span></td></tr></table></td>
	  </tr>
	  <tr>
		<th>题目：</th>
          <td><textarea name="description" id="description" style="height:100px; width: 400px;"><?=$description?></textarea><script>$('#description').editor(undefined, {'onchange_callback':'editCallback'});var editCallback = function() {window.changed = true;};</script></td>
        <tr>
            <th>答案：</th>
            <td><textarea name="analysis" id="analysis" style="height:100px; width: 400px;"><?=$analysis?></textarea><script>$('#analysis').editor(undefined, {'onchange_callback':'editCallback'});var editCallback = function() {window.changed = true;};</script></td>
        </tr>
	  </tr>
	  <tr>
		<th>宽度：</th>
		<td><input type="text" name="width" id="width" size="3" value="300" /> px</td>
	  </tr>
	  <tr>
		<th>最大字符数：</th>
		<td><input type="text" name="maxlength" id="maxlength" size="5" value="<?=$maxlength?>" /> 字节（留空表示不限）</td>
	  </tr>
	  <tr>
		<th>数据校验：</th>
		<td><input type="radio" name="validator" value="text" checked="checked" class="bdr_5" /> 无 <input type="radio" name="validator" value="date" class="bdr_5" /> 日期 <input type="radio" name="validator" value="email" class="bdr_5" /> E-mail <input type="radio" name="validator" value="number" class="bdr_5" /> 数字</td>
	  </tr>
	  <tr>
		<th>必填：</th>
		<td><input type="checkbox" id="required" name="required" value="1" class="bdr_5" /> 是</td>
	  </tr>
	</table>
</form>