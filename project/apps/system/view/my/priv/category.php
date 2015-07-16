<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$CONFIG['charset']?>" />
<title><?=$head['title']?></title>
<link rel="stylesheet" type="text/css" href="css/admin.css"/>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.js"></script>
<script type="text/javascript" src="<?php echo IMG_URL?>js/config.js"></script>
<script type="text/javascript" src="<?php echo IMG_URL?>js/cmstop.js"></script>
<!--tree-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/tree/style.css"/>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.tree.js"></script>
</head>
<body>
<div class="bk_8"></div>
<div class="tag_1">
	<ul class="tag_list">
		<li><a href="?app=system&controller=my&action=priv&type=action">操作权限</a></li>
		<li class="s_3"><a href="?app=system&controller=my&action=priv&type=category">栏目权限</a></li>
		<li><a href="?app=system&controller=my&action=priv&type=page">页面权限</a></li>
	</ul>
    <p class="f_r mar_r_8">
      <span>部门：<?=$department?>，角色：<?=$role?></span>
    </p>
</div>
<table width="98%" class="table_list mar_l_8" cellpadding="0" cellspacing="0" style="empty-cells:show;">
  <thead>
    <tr>
      <th class="bdr_3 t_c">权限/栏目</th>
    </tr>
  </thead>
  <tbody>
  </tbody>
</table>
<script type="text/javascript">
treeOptions = {
	url:"?app=system&controller=administrator&action=catetree&userid=<?=$_userid?>&catid=%s",
	paramId : 'catid',
	paramHaschild:"hasChildren",
	renderTxt:function(div, id, item){
		var check = item.checked ? 'checked="checked"' : '',
			disable = check ? 'disabled="disabled"': '',
			input = '<input type="checkbox" name="catid[]" '+check+' '+disable+' value="'+id+'" class="radio_style" />';
		return $('<span id="'+id+'">'+input+'<label>'+item.name+'</span></label>');
	}
};
$('.table_list').tree(treeOptions);
</script>
</body>
</html>