<div class="bk_8"></div>
<div class="psn">
	<table id="psn_list" class="tablesorter table_list" cellspacing="0" cellpadding="0" width="98%" style="margin-left:6px;">
		<thead>
			<tr>
				<th class="bdr_3" width="5%"><input type="checkbox" id="all_select" /></th>
				<th width="20%">名称</th>
				<th width="30%">路径</th>
				<th width="30%">URL</th>				
			</tr>
		</thead>
		<tbody>
			<?php foreach($psn as $key => $item):?>
			<tr>
				<td class="t_c"><input type="checkbox" name="psn_<?=$key?>" class="add_to_rules" value="1"></td>
				<td class="t_c"><?=$item['name']?></td>
				<td class="t_l path"><?=$item['path']?></td>
				<td class="t_l url"><?=$item['url']?></td>
			</tr>
			<?php endforeach;?>
		</tbody>
	</table>
</div>
<script type="text/javascript">
$(document).ready(function() {
	var psn_list	= $('#psn_list').find('tbody').find('input:checkbox');
	var all_select	= $('#all_select');
	all_select.bind('change', function() {
		if (all_select.attr('checked')) {
			psn_list.attr('checked', 'checked');
			$.each(psn_list, function(i,v) {
				$(v).parent().parent().addClass('row_chked');
			});
		} else {
			psn_list.attr('checked', '');
			$.each(psn_list, function(i,v) {
				$(v).parent().parent().removeClass('row_chked');
			});
		}
	});
	psn_list.bind('change', function(e) {
		var obj	= $(e.target);
		if (obj.attr('checked')) {
			obj.parent().parent().addClass('row_chked');
		} else {
			all_select.attr('checked','');
			obj.parent().parent().removeClass('row_chked');
		}
	});
	$('#psn_list').bind('click', function(e) {
		var obj	= $(e.target).parent('tr');
		if (obj.is('tr')) {
			obj.find('input:checkbox').trigger('click').trigger('change');
		}
	});
});
</script>