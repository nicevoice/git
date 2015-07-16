<div class="bk_8"></div>
<form>
	<table width="95%" border="0" cellspacing="0" cellpadding="0">
		<input name="data[x]" type="hidden" id="map_x" value="<?=$data['x']?>"/>
		<input name="data[y]" type="hidden" id="map_y" value="<?=$data['y']?>"/>
		<input name="data[zoom]" type="hidden" id="map_zoom" value="<?=$data['zoom']?>"/>
		<tbody>
			<tr>
				<td>
				<div style="width:560px;height:340px;border:1px solid gray" id="map_container"></div>
				</td>
			</tr>
			<tr>
				<td>
					宽度:<input name="data[width]" value="<?=$data['width'] ? $data['width'] : 500?>"/> px
					高度:<input name="data[height]" value="<?=$data['height'] ? $data['height'] : 300?>"/> px
				</td>
			</tr>
		</tbody>
	</table>
</form>