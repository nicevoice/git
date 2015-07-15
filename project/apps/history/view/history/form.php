<div class="bk_8"></div>
<form name="<?=$controller?>_save" id="<?=$controller?>_save" method="POST" action="?app=<?=$app?>&controller=<?=$controller?>&action=<?=$action?>">
	<input type="hidden" name="hid" value="<?=$hid?>"/>
	<input type="hidden" name="cronid" value="<?=$cronid?>"/>
	<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
		<tr>
			<th width="100"><span class="c_red">*</span> 任务名称：</th>
			<td><input type="text" name="name" id="name" value="<?=$name?>" size="40"/></td>
		</tr>
		<tr>
			<th width="100"><img align="absmiddle" tips="即保存页面的目录名,不可重复,如果不存在会自动生成" class="tips hand" src="images/question.gif"/> <span class="c_red">*</span> 别名：</th>
			<td><input type="text" name="alias" id="alias" value="<?=$alias?>" size="40"/></td>
		</tr>
		<tr>
			<th width="100"><span class="c_red">*</span> 页面url：</th>
			<td><input type="text" name="url" id="url" value="<?=$url?>" size="40"/></td>
		</tr>
		<tr>
			<th>
				<img align="absmiddle" tips="设置运行时段，如果为空则每小时都运行" class="tips hand" src="images/question.gif"/> 运行时段：<br/>
				<a class="checkAll" href="javascript:;">全选</a>/<a class="cancelAll" href="javascript:;">取消</a>&nbsp;&nbsp; 
			</th>
			<td>
			<? for($i=0; $i<=23; $i++): ?>
				<? if($i<10) echo '0';?><?=$i?> <input <? if(in_array($i, $hourArr)) echo 'checked '; ?>type="checkbox" class="radio_style" name="hour[]" value="<?=$i?>"/>
				<? if($i % 8 == 7) echo '<br/>';?>
			<? endfor; ?>
			</td>
		</tr>
		<tr>
			<th> 开始日期：</th>
			<td><input class="input_calendar" size="20" type="text" name="starttime" id="starttime" value="<?=$starttime?>" style="width:65px;" /></td>
		</tr>
		<tr>
			<th><img align="absmiddle" tips="如果为空则不限制" class="tips hand" src="images/question.gif" /> 结束日期：</th>
			<td><input class="input_calendar" size="20" type="text" name="endtime" id="endtime" value="<?=$endtime?>" style="width:65px;" /></td>
		</tr>
	</table>
</form>
<script type="text/javascript">
$(function (){
	$('.tips').attrTips('tips', 'tips_green', 200, 'top');
})
</script>
<style>
tr.mode1,tr.mode2,tr.mode3 {
	display: none;
}
</style>