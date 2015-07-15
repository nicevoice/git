<form id="select_add"  method="post" action="?app=<?=$app?>&controller=<?=$controller?>&action=<?=$action?>">
	<input type="hidden" name="type" value="<?=$type?>" />
	<table border="0" cellspacing="0" cellpadding="0" class="table_form">
	  <tr>
		<th width="70"><span class="c_red">*</span> 标题：</th>
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
		<th>说明：</th>
          <td><textarea name="description" id="description" style="height:100px; width: 400px;"><?=$description?></textarea><script>$('#description').editor(undefined, {'onchange_callback':'editCallback'});var editCallback = function() {window.changed = true;};</script></td>
	  </tr>
	</table>
	<table border="0" cellspacing="0" cellpadding="0">
	  <tr>
		<th width="70" style="color:#077AC7;font-weight:normal;" class="t_r"><span class="c_red">*</span> 选项：</th>
		<td>
		<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_info mar_t_10">
			<thead>
			  <tr>
				<th width="40">排序</th>
				<th>选项</th>
				<th>图片</th>
				<th width="60">操作</th>
			  </tr>
			</thead>
			<tbody id="options">
			</tbody>
		</table>
		</td>
		<tr><th></th><td><div class="mar_l_8 mar_5"><input name="add_option" type="button" value="增加项" class="button_style" onclick="option.add()"/></div></td></tr>
	</tr>
	</table>
	<table border="0" cellspacing="0" cellpadding="0" class="table_form">
	  <tr>
		<th width="70">必选：</th>
		<td><input type="checkbox" id="required" name="required" value="1" class="bdr_5" /> 是</td>
	  </tr>
	  <tr>
		<th>允许补充：</th>
		<td><input type="checkbox" name="allowfill" id="allowfill" value="1" class="bdr_5" /> 是</td>
	  </tr>
	</table>
</form>