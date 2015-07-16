<?php
$menu = loader::model('admin/menu', 'system');

$menuids = array();
$menuid = $menu->add(array(
	'parentid'=>5,
	'name'=>'评论',
	'url'=>'?app=comment&controller=comment&action=index',
	'sort'=>''
));
$menuids[] = $menuid;
$menuids[] = $menu->add(array(
	'parentid'=>$menuid,
	'name'=>'管理评论',
	'url'=>'?app=comment&controller=comment&action=index',
	'sort'=>'1'
));
$menuids[] = $menu->add(array(
	'parentid'=>$menuid,
	'name'=>'举报评论',
	'url'=>'?app=comment&controller=comment&action=report',
	'sort'=>'2'
));
$menuids[] = $menu->add(array(
	'parentid'=>$menuid,
	'name'=>'敏感评论',
	'url'=>'?app=comment&controller=comment&action=sensitive',
	'sort'=>'3'
));
$menuids[] = $menu->add(array(
	'parentid'=>$menuid,
	'name'=>'设置',
	'url'=>'?app=comment&controller=setting&action=index',
	'sort'=>'4'
));
$installlog = str_replace('\\', '/', dirname(__FILE__)).'/install.log';
write_file($installlog, implode(',', $menuids));