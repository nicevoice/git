<div class="bk_8"></div>
<form name="member_sendmail" id="member_sendmail" method="POST" action="?app=member&controller=index&action=sendmail&userid=<?=$userid?>">
<table border="0" cellspacing="0" cellpadding="0" class="table_form">
	<tr>
		<th width="80">发件人：</th>
		<td><input type="text" name="from" id="from" value="<?=$from?>" size="40" /></td>
	</tr>
	<tr><th>收件人：</th>
		<td><?=$member['email']?> (<?=$member['username']?>)</td></tr>
	<tr>
		<th>标题：</th>
		<td><input type="text" name="subject" id="subject" value="" size="40" /></td>
	</tr>
	<tr>
		<th>内容：</th>
		<td>
			<textarea name="message" id="message" class="layout" style="width:247px;height:185px"></textarea>
		</td>
	</tr>
</table>
</form>