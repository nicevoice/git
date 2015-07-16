<div class="bk_8"></div>
<form name="comment_ipedit" id="comment_ipedit" method="post" class="validator" action="?app=comment&controller=comment&action=ip_edit&commentid=<?=$comment['commentid']?>">
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form">
	<input type="hidden" name="commentid" id="commentid" value="<?=$comment['commentid']?>">
	<tr>
		<th>IP：</th>
		<td><input id="ip" name="ip" value="<?=$comment['ip']?>" /></td>
	</tr>
	<tr>
		<th></th>
		<td><input type="button" class="button_style_1" value="随机" id="ip_random" name="button3"/></td>
	</tr>
</table>
</form>
<script type="text/javascript">
	$("#ip_random").click(function(){
		$("#ip").val(Math.ceil(Math.random()*255)+'.'+Math.ceil(Math.random()*255)+'.'+(Math.ceil(Math.random()*255))+'.'+Math.ceil(Math.random()*255));
	})
</script>