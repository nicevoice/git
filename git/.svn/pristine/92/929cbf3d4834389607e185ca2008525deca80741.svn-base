<form id="checkbox_edit" method="post" action="?app=<?=$app?>&controller=<?=$controller?>&action=<?=$action?>">
	<input type="hidden" name="questionid" value="<?=$questionid?>" />
    <input type="hidden" name="contentid" value="<?=$contentid?>" />
	<input type="hidden" name="type" value="<?=$type?>" />
	<table border="0" cellspacing="0" cellpadding="0" class="table_form">
	  <tr>
		<th width="70"><span class="c_red">*</span> 标题：</th>
		<td><input type="text" name="subject" id="subject" class="bdr inputtit_focus" size="60" maxlength="80" value="<?=$subject?>" maxlength="80"/></td>
	  </tr>
	  <tr>
		<th>图片：</th>
		<td><table border="0" cellspacing="2" cellpadding="0"><tr><td><input type="text" name="image" id="image" size="30" value="<?=$image?>" /></td><td><span id="uploadimage" class="uploader"></span></td></tr></table></td>
	  </tr>
	  <tr>
		<th>说明：</th>
		<td><input name="description" id="description" type="text" size="65" value="<?=$description?>" /></td>
	  </tr>
	</table>
	<table border="0" cellspacing="0" cellpadding="0">
	  <tr>
		<th width="70" style="color:#077AC7;font-weight:normal;" class="t_r"><span class="c_red">*</span> 选项：</th>
		<td>
		<table id="options" width="100%" border="0" cellspacing="0" cellpadding="0" class="table_info mar_t_10">
			<thead>
			  <tr>
				<th width="40">排序</th>
				<th>选项</th>
				<th>图片</th>
				<th width="60">操作</th>
			  </tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		</td>
		<tr><th></th><td><div class="mar_l_8 mar_5"><input name="add_option" type="button" value="增加项" class="button_style" onclick="option.add()"/></div></td></tr>
	</tr>
	</table>
	<table border="0" cellspacing="0" cellpadding="0" class="table_form">
	  <tr>
		<th width="70">必选：</th>
		<td><input type="checkbox" id="required" name="required" value="1" class="bdr_5" <?=$required ? 'checked' : ''?>/> 是</td>
	  </tr>
	  <tr>
		<th>最少选：</th>
		<td><input type="text" name="minoptions" id="minoptions" size="3" value="<?=$minoptions?>" /> 项（为空表示不限制）</td>
	  </tr>
	  <tr>
		<th>最多可选：</th>
		<td><input type="text" name="maxoptions" id="maxoptions" size="3" value="<?=$maxoptions?>" /> 项（为空表示不限制）</td>
	  </tr>
	  <tr>
		<th>允许补充：</th>
		<td><input type="checkbox" name="allowfill" id="allowfill" value="1" class="bdr_5" <?=$allowfill ? 'checked' : ''?>/> 是</td>
	  </tr>
	</table>
</form>
<script type="text/javascript">
<?php foreach ($option as $o){?>
option.add('<?=$o['name']?>', '<?=$o['image']?>', '<?=$o['sort']?>', '<?=$o['optionid']?>');
<?php }?>
</script>