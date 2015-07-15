<?php $this->display('header', 'system');?>
<div class="bk_10"></div>
<form id="setting_edit_mail" action="?app=system&controller=setting&action=edit" method="POST">
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
	<caption>邮件设置</caption>
	<tr>
		<th width="150">发送方式：</th>
		<td>
		<ul>
			<li><input type="radio" name="setting[mail][mailer]" value="1" class="radio" <?php if ($mail['mailer'] == 1) echo 'checked';?>/> 通过 PHP 函数的 sendmail 发送(推荐此方式)</li>
			<li><input type="radio" name="setting[mail][mailer]" value="2" class="radio" <?php if ($mail['mailer'] == 2) echo 'checked';?>> 通过 SOCKET 连接 SMTP 服务器发送(支持 ESMTP 验证)</li>
			<li><input type="radio" name="setting[mail][mailer]" value="3" class="radio" <?php if ($mail['mailer'] == 3) echo 'checked';?>> 通过 PHP 函数 SMTP 发送 Email(仅 Windows 主机下有效, 不支持 ESMTP 验证)</li>
		</ul>
		</td>
	</tr>
	<tr>
		<th>SMTP 服务器：</th>
		<td><input id="smtp_host" type="text" name="setting[mail][smtp_host]" value="<?=$mail['smtp_host']?>" size="50"/></td>
	</tr>
	<tr>
		<th>SMTP 端口：</th>
		<td><input id="smtp_port" type="text" name="setting[mail][smtp_port]" value="<?=$mail['smtp_port']?>" size="50"/></td>
	</tr>
	<tr>
		<th>SMTP 身份验证：</th>
		<td><input type="radio" name="setting[mail][smtp_auth]" value="1" class="radio" <?php if ($mail['smtp_auth']) echo 'checked';?>/>是 <input type="radio" name="setting[mail][smtp_auth]" value="0" class="radio" <?php if (!$mail['smtp_auth']) echo 'checked';?>>否</td>
	</tr>
	<tr>
		<th>SMTP 用户名：</th>
		<td><input id="smtp_username" type="text" name="setting[mail][smtp_username]" value="<?=$mail['smtp_username']?>" size="50"/></td>
	</tr>
	<tr>
		<th>SMTP 密码：</th>
		<td><input id="smtp_password" type="text" name="setting[mail][smtp_password]" value="<?=$mail['smtp_password']?>" size="50"/></td>
	</tr>
	<tr>
		<th>发件人：</th>
		<td><input type="text" name="setting[mail][from]" value="<?=$mail['from']?>" size="50"/></td>
	</tr>
	<tr>
		<th>邮件头分隔符：</th>
		<td>
		<ul>
		  <li><input type="radio" name="setting[mail][delimiter]" value="1" class="radio" <?php if ($mail['delimiter'] == 1) echo 'checked';?>/> 使用 CRLF 作为分隔符(通常为 Windows 主机)</li>
		  <li><input type="radio" name="setting[mail][delimiter]" value="2" class="radio" <?php if ($mail['delimiter'] == 2) echo 'checked';?>> 使用 LF 作为分隔符(通常为 Unix/Linux 主机)</li>
		  <li><input type="radio" name="setting[mail][delimiter]" value="3" class="radio" <?php if ($mail['delimiter'] == 3) echo 'checked';?>> 使用 CR 作为分隔符(通常为 Mac 主机)</li>
		</ul>
		</td>
	</tr>
	<tr>
		<th>邮件签名：</th>
		<td><textarea name="setting[mail][sign]" rows="6" cols="50" class="bdr"><?=$mail['sign']?></textarea></td>
	</tr>
	<tr>
		<th></th>
		<td valign="middle">
		  <input type="submit" id="submit" value="保存" class="button_style_2"/>
		  </td>
	</tr>
</table>
</form>
<div class="bk_10"></div>
<script type="text/javascript">
$(function(){
	$('#setting_edit_mail').ajaxForm(function(json){
		if(json.state) ct.tips(json.message);
		else ct.error(json.error);
	});
});
</script>
<?php $this->display('footer', 'system');