<?php $this->display('header', 'system');?>
<div class="bk_10"></div>
<div class="suggest mar_l_8" style="width:98%">
	<h2>友情提示</h2>
	<p>
		数据表长期运行之后可能数据损坏或速度下降，这时可以尝试修复/优化表
	</p>
</div>
<div class="bk_8"></div>
<form id="database_repair" action="?app=system&controller=database&action=dorepair" method="POST">
<table width="98%" cellpadding="0" cellspacing="0" id="item_list" class="tablesorter table_list mar_l_8">
	<thead><tr>
		<th width="30" class="t_c bdr_3">
			<input type="checkbox" id="check_all" class="checkbox_style"/>
		</th>
		<th>数据库表名</th>
		<th width="100">管理操作</th>
	</tr></thead>
	<tbody id="list_body">
 <?php if(is_array($tables)){ foreach($tables as $k => $table){$k++; ?>
	<tr>
		<td class="t_c"><input type="checkbox" name="tables[]" value="<?=$table?>"></td>
		<td class="t_l"><?=$table?></td>
		<td class="t_c">
			<img width="16" height="16" class="hand repair" value="repair" alt="修复表" title="修复表" src="images/repair.gif">&nbsp;
			<img width="16" height="16" class="hand optimize" value="optimize" alt="优化表" title="优化表" src="images/optimize.gif">
		</td>
	</tr>
<?php }}?>
	</tbody>
</table>
<div class="bk_8"></div>
<div class="table_foot">
	<div class="f_l"> 
		<a href="javascript:void(0);" id="chk_all">全选</a>/
		<a href="javascript:void(0);" id="cancel_all">取消</a>
		<input type="radio" name="operation" value="repair">修复表&nbsp;&nbsp;
		<input type="radio" name="operation" value="optimize" checked>优化表&nbsp;&nbsp;
		<input type="submit" name="dosubmit" value="点击开始" class="button_style_4">
	</div>
</div>
</form>
<script type="text/javascript">
$(function(){
	$('#database_repair').ajaxForm(function(data){
		if(data.state) ct.ok(data.message);
		else ct.error(data.error);
	});	
	$('#list_body input:checkbox').click(function (){
		this.checked = !this.checked;
	});
	$('#list_body>tr').click(function (){
		var input = $(this).find('input:checkbox')[0];
		if(input.checked) {
			input.checked = false;
			$(this).removeClass('row_chked');
		}else{
			input.checked = true;
			$(this).addClass('row_chked');
		}
	}).mouseover(function (){
		$('#list_body>tr.over').removeClass('over');
		$(this).addClass('over');
	});
	$('#check_all').click(function (){
		if($(this).attr('checked')){
			$('input:checkbox').attr('checked', true);
			$('#list_body>tr').addClass('row_chked');
		}else{
			$('input:checkbox').attr('checked', false);
			$('#list_body>tr').removeClass('row_chked');
		}
	});
	$('#chk_all, #cancel_all').click(function (){
		$('#check_all').attr('checked', this.id == 'chk_all').triggerHandler('click');
	});
	
	$('img.repair, img.optimize').click(function (){
		$('input:radio[value='+this.value+']').click();
		$('#cancel_all').click();
		setTimeout("$('#database_repair').submit()", 10);
	});
});
</script>
<?php $this->display('footer', 'system');?>
