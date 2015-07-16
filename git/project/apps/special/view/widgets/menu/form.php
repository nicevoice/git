<div class="bk_8"></div>
<form>
<?php if (isset($_REQUEST['widgetid'])):?>
<input type="hidden" name="widgetid" value="<?=$_REQUEST['widgetid']?>" />
<?php endif;?>
<div style="width:95%;margin:5px auto;">
	<button type="button" title="批量导入数据" id="adder">导入</button>
	<button type="button" title="清空数据" id="clear">清空</button>
	<div class="list-area menulist"><?=isset($list) ? json_encode($list) : ''?></div>
</div>
<table width="95%" border="0" cellspacing="0" cellpadding="0">
	<caption>高级</caption>
	<tbody>
		<tr>
			<td><a id="template" style="cursor:pointer">编辑模板</a></td>
		</tr>
	</tbody>
</table>
</form>
<div class="bk_5"></div>