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
<!--dialog-->
<link rel="stylesheet" type="text/css" href="<?=IMG_URL?>js/lib/jquery-ui/dialog.css" />
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/jquery.ui.js"></script>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.dialog.js"></script>
<!--validator-->
<link rel="stylesheet" type="text/css" href="<?php echo IMG_URL?>js/lib/validator/style.css"/>
<script type="text/javascript" src="<?php echo IMG_URL?>js/lib/cmstop.validator.js"></script>
</head>
<body>
<?php $member = loader::model('member','member')->getProfile($userid);?>
<div class="bk_10"></div>
<div class="tag_1">
	<ul class="tag_list">
	<li><a href="?app=member&controller=index&action=profile&userid=<?=$member['userid']?>">用户资料</a></li>
		<?php if($member['groupid'] == 1) { ?>
	<li><a href="?app=system&controller=administrator&action=stat&userid=<?=$member['userid']?>">工作报表</a></li>
	<li><a href="?app=system&controller=score&action=view&userid=<?=$member['userid']?>">评分记录</a></li>
	<li class="s_3"><a href="?app=system&controller=administrator&action=priv&userid=<?=$member['userid']?>">权限</a></li>
		<?php } ?>
	</ul>
	<input type="button" value="修改资料" class="button_style_1" onclick="member.edit(<?=$member['userid']?>)"/>
	<input type="button" value="修改密码" class="button_style_1" onclick="member.password(<?=$member['userid']?>)"/>
	<input type="button" value="修改头像" class="button_style_1" onclick="member.avatar(<?=$member['userid']?>)"/>
	<input type="button" value="删除" class="button_style_1" onclick="member.del(<?=$member['userid']?>);"/>
	<input name="back" type="button" value="返回" class="button_style_1" onclick="javascript:history.go(-1);"/>
</div>
<div class="bk_8"></div>
<div class="tag_list_1 pad_8 layout">
<a href="?app=system&controller=administrator&action=priv&userid=<?=$userid?>&type=action">操作权限</a>
<a class="s_5" href="?app=system&controller=administrator&action=priv&userid=<?=$userid?>&type=category">栏目权限</a>
<a href="?app=system&controller=administrator&action=priv&userid=<?=$userid?>&type=page">页面权限</a>
    <p class="f_r mar_r_8">
      <span>部门：<?=$department?>，角色：<?=$role?></span>
    </p></div>
<div class="bk_8"></div>
<table width="98%" class="table_list mar_l_8" cellpadding="0" cellspacing="0" style="empty-cells:show;">
  <thead>
    <tr>
      <th class="bdr_3 t_c">栏目</th>
      <th width="100" class="t_c">权限</th>
    </tr>
  </thead>
  <tbody>
  </tbody>
</table>
<script type="text/javascript" src="apps/member/js/member.js"></script>
<script type="text/javascript">
var rowTemplate = '\
<tr id="row_{catid}">\
	<td class="t_l">\
		<label>{name}</label>\
	</td>\
	<td class="t_c"><img class="has" height="16" width="16" src="images/sh.gif"/></td>\
</tr>',
treeOptions = {
    idField:'catid',
	treeCellIndex:0,
	rowIdPrefix:'row_',
	template:rowTemplate,
	baseUrl:'?app=system&controller=administrator&action=priv&type=category',
	rowReady:function(id, tr, json)
	{
		if (!json.allow)
		{
			tr.find('img.has').remove();
		}
	}
};
var tree = new ct.treeTable($('table'), treeOptions);
tree.load('userid=<?=$userid?>');
</script>
</body>
</html>