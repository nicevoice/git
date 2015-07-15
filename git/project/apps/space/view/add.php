<div class="bk_8"></div>
<form name="column_add" id="column_add" method="POST" class="validator" action="?app=space&controller=index&action=add">
<table border="0" cellspacing="0" cellpadding="0" class="table_form">
	<tbody>
		<tr><th width="80"> 名称：</th><td><input type="text" name="name" value="<?=$name?>" size="30"/></td></tr>
		<tr><th> 作者名：</th><td><input type="text" name="author" value="<?=$author?>" size="30"/></td></tr>
		<tr><th><?=element::tips('个性专栏地址只能由字母和数字组成')?>地址：</th><td> <?php echo SPACE_URL;?><input type="text" name="alias" id="alias" value="<?=$alias?>" size="20"/></td></tr>
		<tr><th>头像：</th><td><?=element::image('photo', '', 30)?></td></tr>
		<tr><th>简介：</th><td><textarea name="description"  style="width:300px;height:100px;"><?=$description?></textarea></tr>
		<tr><th><?=element::tips('关联后该用户将可以管理专栏 如果不关联请留空')?>关联用户：</th>
			<td><input name="username" value="" id="username"/></td>
		</tr>
		<tr><th>状态：</th><td>
		<select name="status">
			<?php 
			foreach($statuss as $k => $v) {
				$selected = ($k==3)?'selected="selected"':'';
				echo '<option value="'.$k.'" '.$selected.'>'.$v.'</option>';
			}
			?>
			</select>
		</td></tr>
		<tr><th>排序：</th><td><input type="text" name="sort" value="1" size="3"/></td></tr>
		<tr><th>发稿权限：</th><td><input type="checkbox" name="iseditor" value="1"/> 免审</td></tr>
	</tbody>
</table>
</form>
<script type="text/javascript">
$(function(){
	$('img.tips').attrTips('tips', 'tips_green', 200, 'top');
	$('#username').autocomplete({
		url : '?app=space&controller=index&action=username&q=%s'
	});
});
</script>