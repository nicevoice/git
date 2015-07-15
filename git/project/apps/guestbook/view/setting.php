<?php $this->display('header', 'system');?>
<div class="bk_10"></div>
<form id="guestbook_setting" action="?app=guestbook&controller=setting&action=index" method="POST">
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
	<caption>留言本基本设置</caption>
	<tr>
		<th width="120">留言本标题：</th>
		<td colspan="2"><input type="text" name="setting[guestbookname]" value="<?=$setting['guestbookname']?>" size="30"/></td>
	</tr>
	<tr>
		<th>每页显示条数：</th>
		<td colspan="2"><input type="text" name="setting[pagesize]" value="<?=$setting['pagesize']?>" size="10"/></td>
	</tr>
	<tr>
		<th>留言最大字数：</th>
		<td colspan="2"><input type="text" name="setting[replymax]" value="<?=$setting['replymax']?>" size="10"/></td>
	</tr>
	<tr>
		<th>管理员名单：</th>
		<td colspan="2"><input type="text" name="setting[managelist]" value="<?=$setting['managelist']?>" size="20"/><span class="aler">&emsp;多个名字间用逗号分开</span></td>
	</tr>
	<tr>
		<th>显示管理员名字：</th>
		<td colspan="2"><input type="text" name="setting[showmanage]" value="<?=$setting['showmanage']?>" size="20"/></td>
	</tr>
	<tr>
		<th>回复后才显示：</th>
		<td colspan="2"><?php echo $repayedshow;?>
		<input type="radio" name="setting[repliedshow]" value="1" class="radio" <?php if ($setting['repliedshow']) echo 'checked';?>/> <label for="l1">是</label>
		<input type="radio" name="setting[repliedshow]" value="0" class="radio" <?php if (!$setting['repliedshow']) echo 'checked';?>> <label for="l2">否</label>
		</td>
	</tr>
	<tr>
		<th>仅会员能留言：</th>
		<td colspan="2">
		<input type="radio" id="l3" name="setting[memberguest]" value="1" class="radio" <?php if ($setting['memberguest']) echo 'checked';?>/> <label for="l3">是</label>
		<input type="radio" id="l4" name="setting[memberguest]" value="0" class="radio" <?php if (!$setting['memberguest']) echo 'checked';?>> <label for="l4">否</label>
		</td>
	</tr>
	<tr>
		<th>显示留言列表：</th>
		<td colspan="2">
		<input type="radio" id="l5" name="setting[unguestlist]" value="1" class="radio" <?php if ($setting['unguestlist']) echo 'checked';?>/> <label for="l5">是</label>
		<input type="radio" id="l6" name="setting[unguestlist]" value="0" class="radio" <?php if (!$setting['unguestlist']) echo 'checked';?>> <label for="l6">否</label>
		</td>
	</tr>
	<tr>
		<th>验证码是否开启：</th>
		<td colspan="2">
		<input type="radio" id="l7" name="setting[iscode]" value="1" class="radio" <?php if ($setting['iscode']) echo 'checked';?>/> <label for="l7">是</label>
		<input type="radio" id="l8" name="setting[iscode]" value="0" class="radio" <?php if (!$setting['iscode']) echo 'checked';?>> <label for="l8">否</label>
		</td>
	</tr>
	<tr>
		<th class="vtop">留言时选填项：<input type="hidden" name="setting[option][reply]"></th>
		<td>
			<table width="140" border="0" cellpadding="4" cellspacing="0" id="innerTable">
				<tr>
				  <td><input type="checkbox" id="gender" name="setting[option][gender]" value="1" class="checkbox" <?php if ($setting['option']['gender']) echo 'checked';?> />
					<label for="gender">性别</label></td>
				  <td><input type="checkbox" id="isgender" name="setting[option][isgender]" value="1" <?php if ($setting['option']['isgender']) echo 'checked';?> />
					<label for="isgender">必填</label></td>
				</tr>
				<tr>
				  <td><input type="checkbox" id="email" name="setting[option][email]" value="1" <?php if ($setting['option']['email']) echo 'checked';?> class="checkbox" />
					<label for="email">Email</label></td>
				  <td><input type="checkbox"id="isemail" name="setting[option][isemail]" value="1" <?php if ($setting['option']['isemail']) echo 'checked';?> />
					<label for="isemail">必填</label></td>
				</tr>
				<tr>
				  <td><input type="checkbox"id="address" name="setting[option][address]" value="1" <?php if ($setting['option']['address']) echo 'checked';?> class="checkbox" />
					<label for="address">地址</label></td>
				  <td><input type="checkbox" id="isaddress"name="setting[option][isaddress]" value="1" <?php if ($setting['option']['isaddress']) echo 'checked';?> />
					<label for="isaddress">必填</label></td>
				</tr>
				<tr>
				  <td><input type="checkbox" id="telephone"name="setting[option][telephone]" value="1" <?php if ($setting['option']['telephone']) echo 'checked';?> class="checkbox" />
					<label for="telephone">电话</label></td>
				  <td><input type="checkbox"id="istelephone" name="setting[option][istelephone]" values="1" <?php if ($setting['option']['istelephone']) echo 'checked';?> />
					<label for="istelephone">必填</label></td>
				</tr>
				<tr>
				  <td><input type="checkbox"id="qq" name="setting[option][qq]" value="1" <?php if ($setting['option']['qq']) echo 'checked';?> class="checkbox" />
					<label for="qq">Q Q</label></td>
				  <td><input type="checkbox" id="isqq"name="setting[option][isqq]" value="1" <?php if ($setting['option']['isqq']) echo 'checked';?> />
					<label for="isqq">必填</label></td>
				</tr>
				<tr>
				  <td><input type="checkbox"id="msn" name="setting[option][msn]" value="1" <?php if ($setting['option']['msn']) echo 'checked';?> class="checkbox" />
					<label for="msn">MSN</label></td>
				  <td><input type="checkbox"id="ismsn" name="setting[option][ismsn]" value="1" <?php if ($setting['option']['ismsn']) echo 'checked';?> />
					<label for="ismsn">必填</label></td>
				</tr>
				<tr>
				  <td><input type="checkbox" id="homepage"name="setting[option][homepage]" value="1" <?php if ($setting['option']['homepage']) echo 'checked';?> class="checkbox" />
					<label for="homepage">个人主页</label></td>
				  <td><input type="checkbox"id="ishomepage" name="setting[option][ishomepage]" value="1" <?php if ($setting['option']['ishomepage']) echo 'checked';?> />
					<label for="ishomepage">必填</label></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<th class="vtop">过滤敏感字符：</th>
		<td colspan="2"><textarea id="sensekeyword" name="setting[sensekeyword]" rows="8" cols="32"><?php echo $setting['sensekeyword']?></textarea><span onclick="zoomin(this);" style="cursor:pointer">+</span></td>
	</tr>
	<tr>
		<th>Email通知：</th>
		<td colspan="2"><input id="email1" type="checkbox" name="setting[emailnotice]" value="1"  class="radio" <?php if ($setting['emailnotice']) echo 'checked';?>/>
		<label class="aler" for="email1">&nbsp;本选项只有在EMAIL为必填项时才有效</label> </td>
	</tr>
	<tr>
		<th>邮件标题：</th>
		<td colspan="2"><input id="email2" type="text" name="setting[emailtitle]"  value="<?php echo $setting['emailtitle'] ? $setting['emailtitle'] : '您在'.$setting['guestbookname'].'的留言已经有回复';?>" size="32"/></td>
	</tr>
	<tr>
		<th class="vtop">邮件内容：</th>
		<td colspan="2"><textarea id="email3"  name="setting[emailcontent]" rows="8"  cols="32">
		<?php echo $setting['emailcontent'] ? $setting['emailcontent']:'HI，'.$_username.' 您好  您在我们网站上的留言已有回复。你的留言内容：$content    '.$setting['showmanage'].' 回复：';?></textarea> </td>
	</tr>
	<tr>
		<th></th>
		<td colspan="2" valign="middle"><br/>
		<input type="submit" id="submit" value="保存" class="button_style_2"/>
	</td>
	</tr>
</table>
</form>
<script type="text/javascript">
$(function(){
	$("#gender").click(function(){
		if($("#isgender").attr("checked")==true) {
			$("#isgender").attr("checked",false);
		}
	});
	$("#isgender").click(function() {
		if($("#gender").attr("checked")!==true) {
			$("#isgender,#gender").attr("checked",true);
		}
	});

	$("#telephone").click(function(){
		if($("#istelephone").attr("checked")==true) {
			$("#istelephone").attr("checked",false);
		}
	});
	$("#istelephone").click(function(){
		if($("#telephone").attr("checked")!==true) {
			$("#istelephone,#telephone").attr("checked",true);
		}
	});

	$("#homepage").click(function(){
		if($("#ishomepage").attr("checked")==true) {
			$("#ishomepage").attr("checked",false);
		}
	});
	$("#ishomepage").click(function(){
		if($("#homepage").attr("checked")!==true) {
			$("#ishomepage,#homepage").attr("checked",true);
		}
	});
	$("#address").click(function(){
		if($("#isaddress").attr("checked")==true) {
			$("#isaddress").attr("checked",false);
		}
	});
	$("#isaddress").click(function(){
		if($("#address").attr("checked")!==true) {
			$("#isaddress,#address").attr("checked",true);
		}
	});

	$("#qq").click(function() {
		if($("#isqq").attr("checked")==true) {
			$("#isqq").attr("checked",false);
		}
	});
	$("#isqq").click(function() {
		if($("#qq").attr("checked")!==true) {
			$("#isqq,#qq").attr("checked",true);
		}
	});
	$("#msn").click(function() {
		if($("#ismsn").attr("checked")==true) {
			$("#ismsn").attr("checked",false);
		}
	});
	$("#ismsn").click(function() {
		if($("#msn").attr("checked")!==true) {
			$("#ismsn,#msn").attr("checked",true);
		}
	});
	$("#email").click(function() {
		if($("#isemail").attr("checked")==true) {
			$("#isemail").attr("checked",false);
			$('#email1,#email2,#email3').attr("disabled","disabled");
		}
	});

	$("#isemail").click(function(){
		if ($("#email").attr("checked")!==true) {
			$("#isemail,#email").attr("checked",true);
		}
		if ($("#isemail").attr("checked")==true) {
			$('#email1,#email2,#email3').removeAttr('disabled');
		} else {
			$('#email1,#email2,#email3').attr("disabled","disabled");
		}
	});
	
	if($("#isemail").attr("checked")==true) {
		$('#email1,#email2,#email3').removeAttr('disabled');
	} else {
		$('#email1,#email2,#email3').attr("disabled","disabled");
	}
	$('#guestbook_setting').ajaxForm('submit_ok');
});
function zoomin(obj) {
	var d = $('#sensekeyword');
	var rows = d.attr('rows');
	d.attr('rows',rows+1);
}
function submit_ok(json) {
	if(json.state) ct.ok(json.message);
	else ct.error(json.error);
}
</script>
<?php $this->display('footer', 'system');?>
