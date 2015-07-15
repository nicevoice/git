<div class="bk_10"></div>
<style>
.mpic{}
.mpic ul li{float:left;width:120px;height:110px;padding:5px 8px;text-align:center;}
.mpic ul li img{width:120px;height:90px;border:1px solid #ccc;padding:2px;margin-bottom:2px;}
.mpic .trip{width:400px;height:30px;line-height:30px;color:red;text-align:center;}
</style>
<form name="<?=$app?>_<?=$controller?>_edit" id="<?=$app?>_<?=$controller?>_edit" method="POST" class="validator" action="?app=<?=$app?>&controller=<?=$controller?>&action=setinfo&vid=<?=$vid?>">
<input type="hidden" name="vid" id="vid" value="<?=$vid?>"/>
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
	<tr>
		<th width="80">视频名称：</th>
		<td><input type="text" name="title" id="title" value="" size="53"/></td>
	</tr>
	<tr>
		<th>视频标签：</th>
		<td><input type="text" name="tags" id="tags" value="" size="53"/></td>
	</tr>
	<tr>
		<th>显示图片：</th>
		<td><font color="#666666">用于在视频推荐、调用的时候显示的图片</font></td>
	</tr>
	<tr>
		<td colspan="2"><div class="mpic"><ul id="pic"></ul></div></td>
	</tr>
</table>
</form>
