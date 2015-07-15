<form name="<?=$controller?>_add" id="<?=$controller?>_add" method="POST" action="?app=<?=$app?>&controller=<?=$controller?>&action=<?=$action?>">
	<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
		<input type="hidden" value="<?=$topic['topicid']?>" name="tid">
		<tr>
			<th><span class="c_red">*</span> 话题名称：</th>
			<td><input type="text" name="title" id="title" value="<?=htmlspecialchars($topic['title'])?>" size="40"/></td>
		</tr>
		<tr>
			<th><span class="c_red">*</span> 网址：</th>
			<td><input type="text" name="url" id="url" value="<?=htmlspecialchars($topic['url'])?>" size="40"/></td>		
		</tr>
		<tr>
			<th>话题描述：</th>
			<td>
				<textarea rows="3" cols="50" name="description"><?=htmlspecialchars($topic['description'])?></textarea>
			</td>
		</tr>
		<th>缩略图：</th>
		<td><?=element::image('thumb', $topic['thumb'], 30)?></td>
		<tr>
			<th>是否可用：</th>
			<td>
				<input type="radio" name="disabled" <?php if(!$topic['disabled']):?> checked="checked"<?php endif;?>value="0" />是
                <input type="radio" name="disabled" <?=$topic['disabled'] == 1 ? 'checked' : ''?> value="1" />否
			</td>
		</tr>
	</table>
</form>