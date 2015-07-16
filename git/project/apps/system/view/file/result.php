<?php if((count($lost)+count($modified)+count($unknown)) == 0) {?>
	没有发现丢失、修改、未知的文件。
<?php }else { ?>

<div class="tag_1">
	<ul class="tag_list" id="tab">
		<li><a href="javascript:void(0);" class="s_3" id="lost">丢失 (<?php echo count($lost);?>)</a></li>
		<li><a href="javascript:void(0);" id="modified">修改 (<?php echo count($modified);?>)</a></li>
		<li><a href="javascript:void(0);" id="unknown">未知 (<?php echo count($unknown);?>)</a></li>
	</ul>
</div>
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_list">
	<thead><tr>
		<th width="90%" class="bdr_3"> 文件路径 </th>
		<th width="150">修改日期</th>
	</tr></thead>
	<tbody id="list_lost" class="__list">
		<?php if(empty($lost)) { ?>
			<tr><td align="center" colspan="99">暂无数据</td></tr>
		<?php } else { ?>
		<?php foreach($lost as $v) {?>
		<tr>
			<td  width="90%"><?=$v?></td>
			<td><?=date('Y-m-d H:i:s',filemtime($v))?></td>
		</tr>
		<?php } } ?>
	</tbody>
		
	<tbody id="list_modified" style="display:none" class="__list">
		<?php if(empty($modified)) { ?>
			<tr><td align="center" colspan="99">暂无数据</td></tr>
		<?php } else { ?>
		<?php foreach($modified as $v) {?>
		<tr>
			<td  width="90%"><?=$v?></td>
			<td><?=date('Y-m-d H:i:s',filemtime($v))?></td>
		</tr>
		<?php } } ?>
	</tbody>
	<tbody id="list_unknown" style="display:none" class="__list">
		<?php if(empty($unknown)) { ?>
			<tr><td align="center" colspan="99">暂无数据</td></tr>
		<?php } else { ?>
		<?php foreach($unknown as $k => $v) {?>
		<tr>
			<td  width="90%"><?=$k?></td>
			<td><?=date('Y-m-d H:i:s',filemtime($k))?></td>
		</tr>
		<?php } } ?>
	</tbody>
</table>
<script type="text/javascript">
	$('#tab a').click(function(){
		$('#tab a').removeClass('s_3');
		$(this).addClass('s_3');
		var id = $(this).attr('id');
		$('#list_'+id).siblings('.__list').hide();
		$('#list_'+id).show();
	});
</script>
<?php } ?>