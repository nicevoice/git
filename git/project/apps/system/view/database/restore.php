<?php $this->display('header', 'system');?>
<div class="bk_10"></div>
<div class="suggest mar_l_8" style="width:98%">
	<h2>友情提示</h2>
	<p>
	<?php if(count($files)) { ?>
	    数据恢复操作会以备份的数据覆盖当前数据库数据，请谨慎操作；数据恢复操作需要耗费一些时间，建议您在访问量低的时候进行此操作。
	<?php } else {?>
	
		您还没有进行过数据备份，请先<a id="backupA" href="javascript:;">备份数据</a>
	<?php } ?>
	</p>
</div>
<?php
if(!empty($files)) {?>
<div class="bk_10"></div>
<table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list mar_l_8">
	<thead><tr>
		<th class="t_c bdr_3">备份文件</th>
		<th width="10%"><span>文件大小</span></th>
		<th width="120"><span>备份时间</span></th>
		<th width="100">操作</th>
	</tr></thead>
<?php foreach($files as $id => $file) { $id++; ?>
<tr>
	<td class="t_l" field="name"><?=$file['name']?></td>
	<td class="t_c"><?=$file['size']?></td>
	<td class="t_c" field="maketime"><?=$file['time']?></td>
	<td class="t_c">
		<a href="?app=system&controller=database&action=down&name=<?=$file['name']?>"><img width="16" height="16" class="hand down" alt="下载备份" title="下载备份" src="images/down.gif"></a>&nbsp;
		<img width="16" height="16" class="hand restore" alt="导入备份" title="导入备份" src="images/restore.gif">&nbsp;
		<img width="16" height="16" class="hand delete" alt="删除备份" title="删除备份" src="images/delete.gif">
	</td>
</tr>
<?php }} ?>
</table>
<script type="text/javascript">
$(function (){
	$('#backupA').click(function (){
		top.superAssoc.open('?app=system&controller=database&action=backup');
	});
	
	function dorestore(file,volume) {
		$.getJSON("?app=system&controller=database&action=dorestore",{'name': file,'volume':volume}, function(json){
			if(json.state) {
				ct.ok(json.message);
				if(json.volume) {
					if(json.volume >=json.volumes) {
						ct.ok('导入完成');
					} else {
						dorestore(file,json.volume);  //继续下一分卷
					}
				}
			} else {
				ct.error(json.error);
			}
		});
	}
	
	$('img.restore').click(function (){
		var tr = $(this).parents('tr');
		var file = tr.find('td[field=name]').text();
		var time = tr.find('td[field=maketime]').text();
		ct.confirm(
			'将恢复数据到'+time+'的版本，该操作会覆盖当前数据，是否继续？',
			function() {
				dorestore(file,0);
				/*
				$.getJSON("?app=system&controller=database&action=dorestore",{name: file}, function(json){
					if(json.state) {
						if(json.volume) {
							//继续下一分卷
							
						} else {
							ct.ok(json.message);
						}
					} else {
						ct.error(json.error);
					}
				});
				*/
			}
		);
	});
	
	$('img.delete').click(function (){
		var tr = $(this).parents('tr');
		var file = tr.find('td[field=name]').text();
		ct.confirm(
			'此操作不可恢复，确认删除？',
			function() {
				$.getJSON("?app=system&controller=database&action=remove",{name: file}, function(json){
					if(json.state) {
						ct.ok(json.message);
						tr.remove();
					} else {
						ct.error(json.error);
					}
				});
			}
		);
	});
})

</script>
<?php $this->display('footer', 'system');?>
