<?php $this->display('header', 'system');?>
<div class="bk_10"></div>
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_list mar_l_8">
	<thead><tr>
	<th class="bdr_3">文件地址</th><th>特征函数</th><th>特征代码</th><th width="60">Zend加密</th><th width="80">操作</th>
	</tr></thead>
	<?php $i =1; foreach($list as $key => $value) { ?>
	<tr id="row_<?php echo $i;?>">
		<td><?php echo $key;?></td>
		<td style="color:red;"><?php foreach($value['func'] as $func){ if(!in_array($func[1],$temp)){ $temp[] = $func[1]; echo $func[1].' ';}} $temp='';?> <?php if($value['func']) {?>(<?php echo count($value['func']);?>)<?php }?></td>
		
		<td style="color:red;"><?php foreach($value['code'] as $code) { if(!in_array($code[1],$temp)){ $temp[] = $code[1]; echo $code[1].' ';}} $temp='';?> <?php if($value['code']) {?>(<?php echo count($value['code']);?>)<?php }?></td>
		<td><?php echo $value['zend'] ? '是' : '否';?></td>
		<td class="t_c"><img src="images/edit.gif" alt="编辑" width="16" height="16" class="manage hand" onclick="common_edit('<?php echo $key;?>');"/> <img src="images/delete.gif" alt="删除" width="16" height="16" class="manage hand" onclick="del_row(<?php echo $i;?>,'<?php echo $key;?>');"/></td>
	</tr>
	<?php $i++;} ?>
</table>
<script type="text/javascript">
function del_row(id,key) {
	var url = "?app=system&controller=file&action=delunsafe&file="+key; 
	ct.confirm('确认删除文件'+key+'吗？',function(){
		$.getJSON(url,function(json){
			if(json.state) {
				ct.ok('删除完毕');
				$('#row_'+id).remove();
			} else {
				ct.error(json.error);
			}
		});
	});
}
function common_edit(key) {
	ct.assoc.open('?app=system&controller=file&action=editunsafe&file='+key, 'newtab');
}
$(function() {

});
</script>
<?php $this->display('footer', 'system'); ?>