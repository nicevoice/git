<table width="96%" cellpadding="0" cellspacing="0" class="tablesorter table_list" style="margin-left:6px;">
	<thead>
		<tr>
			<th>应用</th>
			<th width="80">操作人</th>
			<th width="80">状态</th>
			<th width="80">时间</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($data as $item):?>
		<tr>
			<td class="t_c"><?=$item['app']?></td>
			<td class="t_c"><?=$item['operator']?></td>
			<td class="t_c"><?=$item['status']?></td>
			<td class="t_c"><?=$item['time']?></td>
		</tr>
		<?php endforeach;?>
	</tbody>
</table>