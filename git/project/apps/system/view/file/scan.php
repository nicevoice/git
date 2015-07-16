<?php $this->display('header', 'system');?>
<div class="bk_10"></div>
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
	<caption>扫描选项</caption>
	<tbody>
		<tr>
			<th width="100"><?=element::tips('public为发布目录<br/>文件较多可能会花费较多的时间')?> 扫描范围：</th>
			<td><input type="checkbox"  id="checkAll" value="/"><label for="checkAll">全选</label><br/>
			<?php foreach($file as $value):?>
			<input type="checkbox" name="directory[]" value="<?php echo $value;?>" <?php echo $value==ROOT_PATH.'public'?'':'checked="checked"';?>/>&nbsp;&nbsp;<?php echo str_replace(ROOT_PATH,'',$value);?><br />
			<?php endforeach;?></td>
		</tr>
		<tr>
			<th><?=element::tips('多个使用 | 作为分割，以下相同')?> 文件后缀：</th>
			<td><input type="text" value="<?php echo $exts;?>" name="exts" id="exts" size="100"></td>
		</tr>
		<tr>
			<th>特征码：</th>
			<td><input type="text" value="<?php echo $code;?>" name="code" id="code" size="100"></td>
		</tr>
		<tr>
			<th>特征函数：</th>
			<td><input type="text" value="<?php echo $func;?>" name="func" id="func" size="100"></td>
		</tr>
		<tr>
			<th></th>
			<td><input type="button" value="扫描" class="button_style_2" id="scan"></td>
		</tr>
	</tbody>
</table>

<table id="info" width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8" style="display:none">
	<caption>基本信息</caption>
	<tr>
		<th width="100">文件总数：</th>
		<td id="scanstat">&nbsp;</td>
	</tr>
	<tr>
		<th>扫描进度：</th>
		<td><div class="speed w_200 f_l" style="margin-top:0;"><div style="width:0;"></div></div><div class="f_l mar_l_8" id="currentstat"></div></td>
	</tr>
	<tr>
		<th>文件名：</th>
		<td id="scanlist">&nbsp;</td>
	</tr>
	<tr>
		<th></th>
		<td><input type="button" value="暂 停" class="button_style_1" id="scanStop"></td>
	</tr>
</table>
<script type="text/javascript">
var siv;
var total;

$('#checkAll').click(function(){
	if($(this).attr('checked') == true) {
		$("input[name='directory\[\]']").attr('checked','checked');
	} else {
		$("input[name='directory\[\]']").attr('checked','');
	}
});
$("#scan").click(function() {
	$(this).attr('disabled',true);
	var data = new Array();
	var i = 0;
	$("input[name='directory\[\]']").each(function(key, value) {
		if(value.checked) {
			i++;
			data[i] = value.value;
		}
	});
	if(data.length <1) {
		ct.error('请选择需要扫描的目录');
	} else {
		$.post(
			'?app=system&controller=file&action=getlist',
			{data:data.join(','),exts:$("#exts").val(),func:$("#func").val(),code:$("#code").val()},
			function(json) {
				total = parseInt(json);
				$("#scanstat").html(json);
				$("#info").show();
				if(parseInt(json) > 0) {
					scanfile();
				} else {
					$("#scanlist").html('没有符合条件需要扫描的文件<br/>');
				}
			}
		);
	}

});
var i = 0;
var stop = false;
$("#scanStop").click(function() {
	if(stop == true) {
		stop = false;
		$(this).val('暂 停');
		scanfile();
	} else {
		stop = true;
		$(this).val('继 续');
	}
});
function viewunsafe() {
	ct.assoc.open('?app=system&controller=file&action=viewunsafe', 'newtab');
}

function scanfile() {
	if(total > i) {
		$.get(
			'?app=system&controller=file&action=scan',
			{list:i,rand:Math.random()},
			function(json) {
				i++;
				var w = ((i*100)/total).toFixed(1);
				if(w >= 100) w = 100;
				$(".speed div").width(w*2);
				$("#scanlist").html(json+'<br/>');
				$("#currentstat").html(w+'% ('+i+'/'+total+')');
				stop || scanfile();
			}
		)
	} else {
		i = 0;
		$('#scan').attr('disabled',false);
		$('#scanStop').attr('disabled',true);
		$("#currentstat").append('&nbsp;扫描完成 <a href="javascript:;" onclick="viewunsafe()">查看结果</a><br/>');
	}
}
$('img.tips').attrTips('tips', 'tips_green', 200, 'top');
</script>
<?php $this->display('footer', 'system'); ?>