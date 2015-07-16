<?php
$menu = loader::model('admin/menu', 'system');

$menuids = array();
$menuid = $menu->insert(array(
	'parentid'=>5,
	'name'=>'Digg',
	'url'=>'?app=digg&controller=digg&action=index',
	'sort'=>'2'
));
$menuids[] = $menuid;
$menuids[] = $menu->insert(array(
	'parentid'=>$menuid,
	'name'=>'排行榜',
	'url'=>'?app=digg&controller=digg&action=index',
	'sort'=>'1'
));
$menuids[] = $menu->insert(array(
	'parentid'=>$menuid,
	'name'=>'设置',
	'url'=>'?app=digg&controller=setting&action=index',
	'sort'=>'2'
));

$installlog = str_replace('\\', '/', dirname(__FILE__)).'/install.log';
write_file($installlog, implode(',', $menuids));