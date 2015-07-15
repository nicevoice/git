<div class="bk_8"></div>
<form>
<input type="hidden" name="city1" value="<?php echo $data['city1'];?>"/>
<input type="hidden" name="city2" value="<?php echo $data['city2'];?>"/>
<input type="hidden" name="city3" value="<?php echo $data['city3'];?>"/>
<table width="95%" border="0" cellspacing="0" cellpadding="0">
	<tbody>
		<tr>
			<th width="60">样式：</th>
			<td>
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td width="20"><input name="data[style]" type="radio" value="pn7" <?php if($data['style'] == 'pn7' || $data['style'] == '') {echo 'checked';} ?>/></td>
						<td><img style="border:1px solid #eee;" src="<?php echo IMG_URL;?>apps/special/widget/weather/images/indexPic7.gif"/></td>
						<td width="20"><input name="data[style]" type="radio" value="pn12" <?php if($data['style'] == 'pn12' ) {echo 'checked';} ?>/></td>
						<td><img style="border:1px solid #eee;" src="<?php echo IMG_URL;?>apps/special/widget/weather/images/indexPic12.gif"/></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<th>选择城市：</th>
			<td><div id="city"></div></td>
		</tr>
		<tr>
			<th>宽度：</th>
			<td>
				<input name="data[width]" value="<?php echo $data['width']?$data['width']:'100%';?>" size="10"/>
			</td>
		</tr>
		<tr>
			<th>高度：</th>
			<td>
				<input name="data[height]" value="<?php echo $data['height'];?>" size="10"/>
			</td>
		</tr>
		<tr style="display:none;">
			<th>代码：</th>
			<td>
				<textarea name="data[code]" id="weather_code" cols="46" rows="5"><?php echo $data['code'];?></textarea>
			</td>
		</tr>
	</tbody>
</table>
</form>
