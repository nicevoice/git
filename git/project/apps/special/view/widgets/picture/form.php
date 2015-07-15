<form>
	<table width="95%" border="0" cellspacing="0" cellpadding="0">
		<tbody>
			<tr>
				<th width="80">图片：</th>
				<td>
					<?=element::image('data[thumb]', $data['thumb'], 20, false)?>
				</td>
			</tr>
			<tr>
				<th>大小：</th>
				<td>
					<input type="text" name="data[width]" value="<?=$data['width']?>" size="6"/> &#x00D7; <input type="text" name="data[height]" value="<?=$data['height']?>" size="6"/> px
				</td>
			</tr>
			<tr>
				<th>描述：</th>
				<td>
					<input type="text" name="data[description]" value="<?=$data['description']?>" size="50"/>
				</td>
			</tr>
			<tr>
				<th>链接：</th>
				<td>
					<input type="text" name="data[url]" value="<?=$data['url']?>" size="45"/>
				</td>
			</tr>
			<tr>
				<th>新窗口打开：</th>
				<td>
					<input type="checkbox" value="1" name="data[blank]"  <?php echo empty($data['blank'])?'':'checked="checked"';?>/>
				</td>
			</tr>
		</tbody>
	</table>
</form>