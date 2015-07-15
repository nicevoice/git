<form>
	<table width="95%" border="0" cellspacing="0" cellpadding="0">
		<tbody>
			<tr>
				<th width="80">Flash地址：</th>
				<td>
					<input type="text" name="data[src]" id="src" value="<?=$data['src']?>" size="20" />&nbsp;
					<button type="button" id="filebtn_flash">Flash库</button>
				</td>
			</tr>
			<tr>
				<th>大小：</th>
				<td>
					<input type="text" name="data[width]" value="<?=$data['width']?>" size="6"/> &#x00D7;
					<input type="text" name="data[height]" value="<?=$data['height']?>" size="6"/> px
				</td>
			</tr>
		</tbody>
	</table>
	<input type="hidden" id="upload_max_filesize" value="<?=$upload_max_filesize?>" />
</form>