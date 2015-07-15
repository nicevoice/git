<div class="bk_8"></div>
<form name="<?=$controller?>_add" id="<?=$controller?>_add" method="POST" action="?app=<?=$app?>&controller=<?=$controller?>&action=<?=$action?>">
	<input type="hidden" name="cronid" value="<?=$data['cronid']?>"/>
	<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
		<tr>
			<th width="100"><span class="c_red">*</span> 名称：</th>
			<td><input type="text" name="name" id="name" value="<?=$data['name']?>" size="40"/></td>
		</tr>
		<tr>
			<th><img align="absmiddle" tips="请保证App,Controller,action有效，计划任务会运行脚本如：<br/>?app=app&controller=controller&action=action" class="tips hand" src="images/question.gif"/> <span class="c_red">*</span> App：</th>
			<td><input type="text" name="app" id="app" value="<?=$data['app']?>" size="40"/></td>
		</tr>
		<tr>
			<th><span class="c_red">*</span> Controller：</th>
			<td><input type="text" name="controller" id="controller" value="<?=$data['controller']?>" size="40"/></td>
		</tr>
		<tr>
			<th><span class="c_red">*</span> Action：</th>
			<td><input type="text" name="action" id="action" value="<?=$data['action']?>" size="40"/></td>
		</tr>
		<tr>
			<th> <img align="absmiddle" tips="其余GET参数,格式如: type=15&id=10" class="tips hand" src="images/question.gif"/> 其它参数：</th>
			<td><input type="text" name="param" id="param" value="<?=$data['param']?>" size="40"/></td>
		</tr>
		<tr>
			<th><img align="absmiddle" tips="定时运行: 指定时间运行一次<br/>循环运行：每隔N分钟执行一次<br/>定时循环运行：指定运行时段，符合条件就执行" class="tips hand" src="images/question.gif"/> 运行模式：</th>
			<td>
				<? foreach ($modeMap as $k=>$v): ?>
					<label><input class="mode radio_style" name="mode" type="radio" value="<?=$k?>"/><?=$v?></label>&nbsp;&nbsp;
				<? endforeach; ?>
			</td>
		</tr>
		<tr class="mode1">
			<th><span class="c_red">*</span> 运行时间：</th>
			<td><input class="input_calendar" type="text" name="time" value="<?=$data['time']?>" style="width:100px;"/></td>
		</tr>
		<tr class="mode2">
			<th><img align="absmiddle" tips="每隔多少分钟运行一次" class="tips hand" src="images/question.gif"/> <span class="c_red">*</span> 间隔：</th>
			<td><input type="text" name="interval" value="<?=$data['interval']?>" size="5"/> 分钟</td>
		</tr>
		<tr class="mode2">
			<th><img align="absmiddle" tips="如果为空则不限制" class="tips hand" src="images/question.gif"/> 最多次数：</th>
			<td><input type="text" name="times" value="<?=$data['times']?>" size="5"/></td>
		</tr>
		<tr class="mode3">
			<th>
				<img align="absmiddle" tips="设置每月运行日，全不选则不限制" class="tips hand" src="images/question.gif"/> 按月设置：<br/>
				<a class="checkAll" href="javascript:;">全选</a>/<a class="cancelAll" href="javascript:;">取消</a>&nbsp;&nbsp; 
			</th>
			<td>
			<? for($i=1; $i<=31; $i++): ?>
				<? if($i<10) echo '0';?><?=$i?> <input <? if(in_array($i, $data['day'])) echo 'checked '; ?>type="checkbox" class="radio_style" name="day[]" value="<?=$i?>"/>
				<? if($i % 8 == 0) echo '<br/>';?>
			<? endfor; ?>
			</td>
		</tr>
		<tr class="mode3">
			<th>
				<img align="absmiddle" tips="设置每周运行日，全不选则不限制，可与每月运行日共同生效" class="tips hand" src="images/question.gif"/> 按周设置：<br/>
				<a class="checkAll" href="javascript:;">全选</a>/<a class="cancelAll" href="javascript:;">取消</a>&nbsp;&nbsp; 
			</th>
			<td>
			<? for($i=0; $i<=6; $i++): ?>
				<?=$weekMap[$i]?><input <? if(in_array($i, $data['weekday'])) echo 'checked '; ?>type="checkbox" class="radio_style" name="weekday[]" value="<?=$i?>"/>
			<? endfor; ?>
			</td>
		</tr>
		<tr class="mode3">
			<th>
				<img align="absmiddle" tips="设置运行小时，全不选则不限制" class="tips hand" src="images/question.gif"/> 运行时段：<br/>
				<a class="checkAll" href="javascript:;">全选</a>/<a class="cancelAll" href="javascript:;">取消</a>&nbsp;&nbsp; 
			</th>
			<td>
			<? for($i=0; $i<=23; $i++): ?>
				<? if($i<10) echo '0';?><?=$i?> <input <? if(in_array($i, $data['hour'])) echo 'checked '; ?>type="checkbox" class="radio_style" name="hour[]" value="<?=$i?>"/>
				<? if($i % 8 == 7) echo '<br/>';?>
			<? endfor; ?>
			</td>
		</tr>
		<tr class="mode3">
			<th>
				<img align="absmiddle" tips="设置运行分钟，全不选则不限制" class="tips hand" src="images/question.gif"/> 运行分钟：<br/>
				<a class="checkAll" href="javascript:;">全选</a>/<a class="cancelAll" href="javascript:;">取消</a>&nbsp;&nbsp; 
			</th>
			<td>
			<? for($i=0; $i<=50; $i+=10): ?>
				<? if($i==0) echo '0';?><?=$i?> <input <? if(in_array($i, $data['minute'])) echo 'checked '; ?>type="checkbox" name="minute[]" class="radio_style" value="<?=$i?>"/>
			<? endfor; ?>
			</td>
		</tr>
		<tr class="mode2 mode3">
			<th><img align="absmiddle" tips="如果为空则立即生效" class="tips hand" src="images/question.gif"/> 开始时间：</th>
			<td><input class="input_calendar" type="text" name="starttime" id="starttime" value="<?=$data['starttime']?>" style="width:100px;"/></td>
		</tr>
		<tr class="mode2 mode3">
			<th><img align="absmiddle" tips="如果为空则不限制" class="tips hand" src="images/question.gif"/> 结束时间：</th>
			<td><input class="input_calendar" type="text" name="endtime" id="endtime" value="<?=$data['endtime']?>" style="width:100px;"/></td>
		</tr>
		<tr class="mode4">
			<th><img align="absmiddle" tips="crontab 格式的规则，如 @weekly、@daily、*/5 * * * *、20-50/3 2 * * * 等" class="tips hand" src="images/question.gif"/> <span class="c_red">*</span> 执行规则：</th>
			<td><input type="text" name="rule" id="rule" value="<?=$data['rule']?>" size="40"/></td>
		</tr>
	</table>
</form>
<script type="text/javascript">
$(function (){
	setTimeout(function (){
		var mode = "<?=intval($data['mode'])?>";
		if(mode == 0) mode = 1;
		$('input.mode[value=' + mode + ']').click()
	}, 30);
	$('.tips').attrTips('tips', 'tips_green', 200, 'top');
	$.validate.setConfigs({
	    xmlPath:'<?=ADMIN_URL?>apps/<?=$app?>/validators/cron/'
	});
})
</script>
<style>
tr.mode1,tr.mode2,tr.mode3,tr.mode4 {
	display: none;
}
</style>