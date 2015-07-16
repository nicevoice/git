<div class="bk_8"></div>
<form name="paper_edition_edit" id="paper_edition_edit" method="POST" action="?app=paper&controller=edition&action=save">
<input type="hidden" name="editionid" value="<?=intval($_GET['editionid'])?>"/>
<input type="hidden" name="paperid" value="<?=$pid?>"/>
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
	<tr>
		<th><span class="c_red">*</span> 总期号：</th>
		<td>
			<input type="text" name="total_number" id="total_number" value="<?=$total_number?>" size="16"/>
			<? if($lastTN) echo '最后期号: '.$lastTN?>
		</td>
	</tr>
	<tr>
		<th width="100"><span class="c_red">*</span> 年度期号：</th>
		<td>
			<input type="text" name="number" id="number" value="<?=$number?>" size="16"/>
			<? if($lastN) echo '最后期号: '.$lastN?>
		</td>
	</tr>
	<tr>
		<th><span class="c_red">*</span> 出版日期：</th>
		<td><input type="text" name="date" class="input_calendar"  value="<?if($date) echo date('Y-m-d', $date)?>" size="14" style="width:65px;" />
		<input type="hidden" name="paperid" value="<?=$paperid?>"/></td>
	</tr>
</table>
</form>