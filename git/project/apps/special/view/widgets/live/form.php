<form>
	<table width="95%" border="0" cellspacing="0" cellpadding="0">
		<tbody>
			<tr>
				<th width="80">直播地址：</th>
				<td>
					<input type="text" name="data[src]" id="src" value="<?=$data['src']?>" size="40" />
				</td>
			</tr>
			<tr>
				<th>大小：</th>
				<td>
					<input type="text" name="data[width]" value="<?=$data['width'] ? $data['width'] : '100%'?>" size="6"/> &#x00D7;
					<input type="text" name="data[height]" value="<?=$data['height'] ? $data['height'] : '100%'?>" size="6"/>
				</td>
			</tr>
		</tbody>
	</table>
</form>