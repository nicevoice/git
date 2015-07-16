<?php $this->display('header', 'system');?>
<style type="text/css">
#pwgrade_message {
	background:url(apps/system/images/levels.gif) no-repeat 0 0;
	height:20px;
	width:200px;
}
</style>
<div class="bk_10"></div>
<form name="password" id="passwordForm" action="?app=system&controller=my&action=password" method="POST">
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
  <caption>修改密码</caption>
  <tr>
    <th width="100">原密码：</th>
    <td><input type="password" name="last_password" value="" size="15" /></td>
  </tr>
  <tr>
    <th>新密码：</th>
    <td><input type="password" id="password" name="password" value="" size="15" /></td>
  </tr>
  <tr>
    <th>&nbsp;</th>
    <td><img width="200" height="20" alt="密码强度" vertical-align="middle" id="pwgrade_message" src="images/space.gif"></td>
  </tr>
  <tr>
    <th>确认输入：</th>
    <td><input type="password" name="confirm_password" value="" size="15" /></td>
  </tr>
  <tr>
    <th>&nbsp;</th>
    <td>
      <input type="submit" name="submit" value=" 确定 " class="button_style_2"/>
    </td>
  </tr>
</table>
</form>
<div class="bk_10"></div>
<script type="text/javascript">
$.validate.setConfigs({
    xmlPath:'<?=ADMIN_URL?>apps/<?=$app?>/validators/my/detail/'
});
$('#passwordForm').ajaxForm(function(json){
	if (json.state) {
		ct.ok(json.info);
	}
	else
	{
		ct.error(json.error);
	}
});
function pwGrade(password) {
	if(password.length <6) {
		return 0;
	}
	var nums	= /[0-9]/;
	var wordsS	= /[a-z]/;
	var wordsB	= /[A-Z]/;
	var wordsO	= /[^0-9a-zA-Z]/;
	var sign = 0;
	
	if (nums.test(password) == true) sign ++;
	if (wordsS.test(password) == true) sign ++;
	if (wordsB.test(password) == true) sign ++;
	if (wordsO.test(password) == true) sign ++;
	
	if (sign > 3) { sign =4 }		//强
	return sign;
}
$("#password").keyup(function() {
	var grade = pwGrade(this.value);
	var pwchar = ["0 0","0 25%","0 50%","0 75%","0 100%"];
	$("#pwgrade_message").css("background-position", pwchar[grade]);
});

</script>
<?php $this->display('footer', 'system');