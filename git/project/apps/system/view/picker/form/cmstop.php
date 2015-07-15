<form>
	<input type="text" name="keywords" />
	<?php
	$param = unserialize($param);
	$url = "?app=system&controller=category&action=cate&dsnid=&catid=%s";
	$model = array();
	if ($param && $param['dsnid']) {
		$url .= '&dsnid='.$param['dsnid'];
		$dsn = loader::model('admin/dsn', 'system')->get($param['dsnid']);
		try {
			$db = factory::db($dsn);
			$model = $db->select('SELECT * FROM #table_model');
		} catch (PDOException $e) {}
	} else {
		$model = table('model');
	}
	?>
	<select name="modelid" multiple alt="选择模型">
		<?php foreach($model as $v):?>
		<option value="<?=$v['modelid']?>" ico="<?=$v['alias']?>"><?=$v['name']?></option>
		<?php endforeach;?>
	</select>
	<input name="catid" width="150"
		url="<?=$url?>"
		paramVal="catid"
		paramTxt="name"
		multiple="1"
		alt="选择栏目"
	/>
</form>