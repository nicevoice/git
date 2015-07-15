<div class="bk_8"></div>
<form>
<table width="95%" border="0" cellspacing="0" cellpadding="0">
	<input type="hidden" name="style" value="<?php echo $data['style'];?>"/>
	<input type="hidden" name="number" value="<?php echo $data['number'];?>"/>
	<tbody>
		<tr>
			<th width="60">样式：</th>
			<td>
				<input name="data[style]" type="radio" id="share_button" value="button" <?php if($data['style'] == 'button' || $data['style'] == '') {echo 'checked';} ?>/> 按钮 <input name="data[style]" type="radio" id="share_float" value="float" <?php if($data['style'] == 'float') {echo 'checked';} ?>/> 浮动
			</td>
		</tr>
		<tr>
			<th>样式选择：</th>
			<td>
				<table width="95%" border="0" cellspacing="0" cellpadding="0">
					<tbody rel="button" <?php echo ($data['style'] == 'button' || $data['style'] == '')?'':'style="display:none;"'; ?>>
						<tr>
							<td><input type="radio" name="data[number]" value="b2" /> <label><img src="<?php echo $path.'b2.gif';?>"/></label></td>
							<td><input type="radio" name="data[number]" value="b1"/> <label><img src="<?php echo $path.'b1.gif';?>"/></label></td>
						</tr>
						<tr>
							<td><input type="radio" name="data[number]" value="b0"/> <label><img src="<?php echo $path.'b0.gif';?>"/></label></td>
							<td><input type="radio" name="data[number]" value="b3"/> <label><img src="<?php echo $path.'b3.gif';?>"/></label></td>
						</tr>
					</tbody>
					<tbody  rel="float" <?php if($data['style'] != 'float') {echo 'style="display:none;"';} ?>>
						<tr>
							<td><input type="radio" name="data[number]" value="f0"/></td>
							<td><label><img src="<?php echo $path.'f0.gif';?>"/></label></td>
							<td><input type="radio" name="data[number]" value="f1"/></td>
							<td><label><img src="<?php echo $path.'f1.gif';?>"/></label></td>
							<td><input type="radio" name="data[number]" value="f2"/></td>
							<td><label><img src="<?php echo $path.'f2.gif';?>"/></label></td>
							<td><input type="radio" name="data[number]" value="f3"/></td>
							<td><label><img src="<?php echo $path.'f3.gif';?>"/></label></td>
						</tr>
						<tr>
							<td><input type="radio" name="data[number]" value="f4"/></td>
							<td><label><img src="<?php echo $path.'f4.gif';?>"/></label></td>
							<td><input type="radio" name="data[number]" value="f5"/></td>
							<td><label><img src="<?php echo $path.'f5.gif';?>"/></label></td>
							<td><input type="radio" name="data[number]" value="f6"/></td>
							<td><label><img src="<?php echo $path.'f6.gif';?>"/></label></td>
							<td><input type="radio" name="data[number]" value="f7"/></td>
							<td><label><img src="<?php echo $path.'f7.gif';?>"/></label></td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>
</form>
