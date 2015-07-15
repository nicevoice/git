<select id="project" class="" name="project">
<option value="0">请选择</option>
<?php foreach($projects as $project):?>
	<option value="<?=$project['projectid']?>" <?= $check == $project['projectid'] ? 'selected="selected"' : ''?>><?=$project['name']?></option>
<?php endforeach;?>
</select>