<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$CONFIG['charset']?>" />
<title><?=$head['title']?></title>
<link rel="stylesheet" type="text/css" href="css/admin.css"/>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.js"></script>
<script type="text/javascript" src="<?php echo IMG_URL?>js/config.js"></script>
<script type="text/javascript" src="<?php echo IMG_URL?>js/cmstop.js"></script>
<!--tree table-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/treetable/style.css"/>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.treetable.js"></script>
</head>
<body>
<div class="bk_8"></div>
<div class="tag_1" style="margin:8px">
	<ul class="tag_list">
		<li class="s_3"><a href="?app=system&controller=administrator&action=priv&userid=<?=$member['userid']?>&type=action">操作权限</a></li>
		<li><a href="?app=system&controller=administrator&action=priv&userid=<?=$member['userid']?>&type=category">栏目权限</a></li>
		<li><a href="?app=system&controller=administrator&action=priv&userid=<?=$member['userid']?>&type=page">页面权限</a></li>
	</ul>
    <p class="f_r">
      <span>部门：<?=$department?>，角色：<?=$role?></span>
    </p>
</div>
<div class="bk_8"></div>
<table width="98%" class="table_list mar_l_8" cellpadding="0" cellspacing="0" style="empty-cells:show;">
  <thead>
    <tr>
      <th class="bdr_3 t_c">权限</th>
      <th width="100" class="t_c">&nbsp;</th>
    </tr>
  </thead>
  <tbody>
  </tbody>
</table>
<script type="text/javascript">
var rowTemplate = '\
<tr id="acarow_{acaid}">\
	<td class="t_l">\
		<label>{name}</label>\
	</td>\
	<td class="t_c"><img class="has" height="16" width="16" src="images/sh.gif"/></td>\
</tr>',
treeOptions = {
    idField:'acaid',
	treeCellIndex:0,
	rowIdPrefix:'acarow_',
	template:rowTemplate,
	baseUrl:'?app=system&controller=role&action=acatree',
	rowReady:function(id, tr, json)
	{
		if (json.disabled)
		{
			tr.find('img.has').remove();
		}
	}
};
var tree = new ct.treeTable($('table'), treeOptions);
tree.load();
</script>
</body>
</html>