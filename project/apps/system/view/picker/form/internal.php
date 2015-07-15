<form>
	<input type="text" name="keywords" />
	<?php if ($_GET['modelid']): ?>
	<input type="hidden" name="modelid" value="<?=$_GET['modelid']?>" />
	<?php else:?>
	<select name="modelid" multiple alt="选择模型">
		<?php foreach(table('model') as $id=>$v):?>
		<option value="<?=$id?>" ico="<?=$v['alias']?>"><?=$v['name']?></option>
		<?php endforeach;?>
	</select>
	<?php endif;?>
	<input name="catid" width="150"
		url="?app=system&controller=category&action=cate&dsnid=&catid=%s"
		paramVal="catid"
		paramTxt="name"
		multiple="1"
		alt="选择栏目"
	/>
</form>