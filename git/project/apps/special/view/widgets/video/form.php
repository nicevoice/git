<div class="bk_8"></div>
<form>
	<table width="95%" border="0" cellspacing="0" cellpadding="0">
		<tbody>
			<tr>
				<th width="70">URL：</th>
				<td><input type="text" name="data[video]" id="data_video" value="<?=$data['video']?>" size="56"/></td>
			</tr>
			<tr>
				<th>&nbsp;</th>
				<td><table><tr>
					<td width="70"><button type="button" id="filebtn_flash" style="margin-left: 0;">媒体库</button></td>
					<td><button id="video_select" type="button">从内容选择</button></td>
				</tr></table></td>
			</tr>
			<tr>
				<th>尺寸：</th>
				<td>
					<input type="text" name="data[width]" value="<?=$data['width']?>" size="6"/> × <input type="text" name="data[height]" value="<?=$data['height']?>" size="6"/> px
				</td>
			</tr>
			<tr>
				<th>自动播放：</th>
				<td>
					<input type="checkbox" value="1" name="data[autostart]"  <?php echo empty($data['autostart'])?'':'checked="checked"';?>/>
				</td>
			</tr>
		</tbody>
	</table>
</form>