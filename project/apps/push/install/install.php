<?php
$menu = loader::model('admin/menu', 'system');

$menuids = array();
$menuid = $menu->add(array(
	'parentid'=>5,
	'name'=>'文章推送',
	'url'=>'?app=push&controller=push',
	'sort'=>'5'
));
$menuids[] = $menuid;
$menuids[] = $menu->add(array(
	'parentid'=>$menuid,
	'name'=>'推送',
	'url'=>'?app=push&controller=push&action=index',
	'sort'=>'1'
));
$menuids[] = $menu->add(array(
	'parentid'=>$menuid,
	'name'=>'添加规则',
	'url'=>'?app=push&controller=push&action=add',
	'sort'=>'2'
));
$menuids[] = $menu->add(array(
	'parentid'=>$menuid,
	'name'=>'管理规则',
	'url'=>'?app=push&controller=push&action=manager',
	'sort'=>'3'
));
$installlog = str_replace('\\', '/', dirname(__FILE__)).'/install.log';
write_file($installlog, implode(',', $menuids));