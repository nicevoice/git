<?php $this->display('header', 'system');?>
<script type="text/javascript" src="uploader/cmstop.uploader.js"></script>
<script type="text/javascript" src="imageEditor/cmstop.imageEditor.js"></script>
<script type="text/javascript" src="js/cmstop.filemanager.js"></script>
<link rel="stylesheet" type="text/css" href="<?=IMG_URL?>js/lib/datepicker/style.css"/>
<script type="text/javascript" src="<?=IMG_URL?>js/lib/cmstop.datepicker.js"></script>
<div class="bk_10"></div>
<form name="profile" id="profileForm" action="?app=system&controller=my&action=profile" method="POST">
<table width="98%" border="0" cellspacing="0" cellpadding="0" class="table_form mar_l_8">
  <caption>修改资料</caption>
  <tr>
    <th>姓名：</th>
    <td><input type="text" name="name" value="<?=$data['name']?>" /></td>
  </tr>
  <tr>
    <th>入职时间：</th>
    <td><?=$data['regtime']?></td>
  </tr>
  <tr>
    <th>部门：</th>
    <td><?=$data['department']?></td>
  </tr>
  <tr>
    <th>职位：</th>
    <td><?=$data['role']?></td>
  </tr>
  <tr>
    <th valign="top">头像：</th>
    <td>
    	<?=element::photo('photo', $data['photo'])?>
    	<p style="margin: 10px 0 0 4px;">注：允许上传图格式为JPG，BMP，GIF，PNG文件大小不可超过2048K</p>
    </td>
  </tr>
  <tr>
    <th>生日：</th>
    <td><input type="text" name="birthday" onfocus="DatePicker(this, {format:'yyyy-MM-dd'})" value="<?=$data['birthday']?>" size="20"/></td>
  </tr>
  <tr>
    <th>Email：</th>
    <td><input type="text" name="email" value="<?=$data['email']?>" size="40"/></td>
  </tr>
  <tr>
    <th>电话：</th>
    <td><input type="text" name="telephone" value="<?=$data['telephone']?>" size="20"/></td>
  </tr>
  <tr>
    <th>手机：</th>
    <td><input type="text" name="mobile" value="<?=$data['mobile']?>" size="20"/></td>
  </tr>
  <tr>
    <th>MSN：</th>
    <td><input type="text" name="msn" value="<?=$data['msn']?>" size="40"/></td>
  </tr>
  <tr>
    <th>QQ：</th>
    <td><input type="text" name="qq" value="<?=$data['qq']?>" size="20"/></td>
  </tr>
  <tr>
    <th>地址：</th>
    <td><input type="text" name="address" value="<?=$data['address']?>" size="50"/></td>
  </tr>
  <tr>
    <th>邮编：</th>
    <td><input type="text" name="zipcode" value="<?=$data['zipcode']?>" size="20"/></td>
  </tr>
  
  <tr>
    <th>&nbsp;</th>
    <td>
      <input type="submit" value="保存" class="button_style_2"/>
      </td>
  </tr>
</table>
</form>
<script type="text/javascript">
$.validate.setConfigs({
    xmlPath:'<?=ADMIN_URL?>apps/<?=$app?>/validators/my/detail/'
});
$('#profileForm').ajaxForm(function(json){
	if (json.state) {
		ct.ok(json.info);
	}
	else
	{
		ct.error(json.error);
	}
});
</script>
<?php $this->display('footer', 'system');