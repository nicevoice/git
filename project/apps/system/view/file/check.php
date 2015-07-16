<?php $this->display('header', 'system');?>
<div class="bk_10"></div>
<div class="suggest w_650 mar_l_8">
	<h2>友情提示</h2>
	<p>该功能提供给用户检测系统文件是否丢失或者修改</p>
	<ul>
		<li>文件校验前请先创建文件镜像。</li>
		<li>系统只使用一个文件镜像，重新创建会覆盖原来的镜像</li>
	</ul>
</div>
<div class="bk_8"></div>
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
	<caption>操作</caption>
	<tr>
		<th width="120"><input type="button" value="创建镜像" class="button_style_4" id="createfilemd5"></th>
		<td class="lh_24">
		<?php if(file_exists($file)) { ?>
			<input type="button" value="校验文件" class="button_style_4" id="checkfilemd5" /> 
			当前文件镜像创建时间为 <?php echo date('Y-m-d H:i:s', filemtime($file));?>
		<?php } else {?>
			系统当前镜像文件丢失，请先创建镜像文件。
		<?php } ?>
		</td>
	</tr>
	<tr>
		<th width="80"></th>
		<td></td>
	</tr>
</table>
<div class="bk_10"></div>
<div id="result" class="mar_l_8"></div>
<script type="text/javascript">
$(function() {
	$("#createfilemd5").click(function() {
		$.getJSON(
			"?app=system&controller=file&action=createfilemd5",function(json){
				if(json.state) {
					ct.ok('操作成功');
					setTimeout(function(){location.reload()},500);
				} else {
					ct.error(json.error);
				}
		});
	}); 
	$("#checkfilemd5").click(function() {
		$.get(
			"?app=system&controller=file&action=checkfilemd5",
			function(json){
				$("#result").html('<p>'+json+'</p>');
				
			});
	});
});
</script>
<?php $this->display('footer', 'system'); ?>