<div class="tabs">
	<ul target="tbody.method">
		<li>基本设置</li>
	</ul>
</div>
<form name="<?=$controller?>_add" id="<?=$controller?>_add" method="POST" action="?app=<?=$app?>&controller=<?=$controller?>&action=<?=$action?>">
	<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
		<input type="hidden" value="<?=$freelist['flid']?>" name="flid">
		<tr>
			<th><span class="c_red">*</span> 列表页名称：</th>
			<td><input type="text" name="name" id="name" value="<?=htmlspecialchars($freelist['name'])?>" size="40"/></td>
		</tr>
		<tr>
			<th><span class="c_red">*</span> 文件名称：</th>
			<td><input type="text" name="filename" id="filename" value="<?= $freelist['filename'] ? htmlspecialchars($freelist['filename']) : 'index' ?>" size="10"/></td>
		</tr>
		<tr>
			<th>分组：</th>
			<td>
				<select name="gid" id="gid">
				<?php foreach($grouplist as $list):?> 
					<option value="<?=$list['gid']?>"
					<?=$freelist['gid'] == $list['gid'] ? 'selected' : ''?>>
					<?=$list['name']?></option>
				<?php endforeach;?>
				</select>
				<a href="javascript:;" onclick="add_dialog();  return false;">新建分组</a>
			</td>
		</tr>
		<tr>
			<th><span class="c_red">*</span> 网址：</th>
			<td>
				<div id="template_input">
					<?=element::psn('path', 'path', $freelist['path'], $size = 35)?>
				</div>
			</td>			
		</tr>
		<tr>
			<th>列表类型：</th>
			<td>
				<input type="radio" name="type" id="shtml" <?php if(!$freelist['type']):?> checked="checked"<?php endif;?>value="0" /><?=SHTML?>
                <input type="radio" name="type" id="xml" <?=$freelist['type'] == 1 ? 'checked' : ''?> value="1" />.xml
                <input type="radio" name="type" id="json" <?=$freelist['type'] == 2 ? 'checked' : ''?> value="2" />.json
			</td>
		</tr>
		<tr id="use_template">
			<th><span class="c_red">*</span>页面模板：</th>
			<td><?= $freelist['template'] ? element::template('template', 'template', $freelist['template'], 35) : element::template('template', 'template', 'freelist/list.html', 35);?></td>
		</tr>
		<tr>
			<th><span class="c_red">*</span>最大生成页数：</th>
			<td>
				<input type="text" name="maxpage" <?php if ($freelist['maxpage']):?>value="<?=$freelist['maxpage']?>"<?php else:?> value="20" <?php endif;?>size="5" />
			</td>
		</tr>
		<tr>
			<th><span class="c_red">*</span>分页每页条数：</th>
			<td>
				<input type="text" name="pagesize" <?php if ($freelist['pagesize']):?>value="<?=$freelist['pagesize']?>"<?php else:?> value="20" <?php endif;?>size="5" />
			</td>
		</tr>
		<tr>
			<th>更新频率：</th>
			<td>
				<input type="text" name="frequency" <?php if ($freelist['frequency']):?>value="<?= (int)$freelist['frequency']?>"<?php else:?> value="60" <?php endif;?> size="5" /> 分钟 (0表示手动)
			</td>
		</tr>
		<tr>
			<th>页面标题：</th>
			<td>
				<input type="text" maxlength="255" size="50" id="title" name="title" value="<?=htmlspecialchars($freelist['title'])?>" >
			</td>
		</tr>
		<tr>
			<th>关键字：</th>
			<td>
				<textarea rows="3" cols="50" name="keywords"><?=htmlspecialchars($freelist['keywords'])?></textarea>
			</td>
		</tr>
		<tr>
			<th>描述：</th>
			<td>
				<textarea rows="3" cols="50" name="description"><?=htmlspecialchars($freelist['description'])?></textarea>
			</td>
		</tr>
	</table>
</form>
<script type="text/javascript">
// 新建分组 add_dialog
var $gid = $("#gid");
function add_dialog() {
	ct.form('新建分组','?app=<?=$app?>&controller=group&action=add',400,120,function(json){
		$gid.append(['<option value="', json.data.gid, '">', json.data.name, '</option>'].join(''));
		return true;
	});
}

// 页面标题 和 列表页名称保持一直
var $name		= $("#name"),
	$title		= $("#title"),
	$oldname	= $name.val();
$name.keyup(getVal);

function getVal() {
	if (!$title.val() || $title.val()==$oldname) {
		$oldname	= $name.val();
		$title.val($name.val());
	}
}
</script>