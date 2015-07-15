<?php
$menu = loader::model('admin/menu', 'system');

$menuids = array();
$menuid = $menu->add(array(
	'parentid'=>5,
	'name'=>'心情',
	'url'=>'?app=mood&controller=data&action=index&range=1',
	'sort'=>'3'
));
$menuids[] = $menuid;
$menuids[] = $menu->add(array(
	'parentid'=>$menuid,
	'name'=>'排行榜',
	'url'=>'?app=mood&controller=data&action=index&range=1',
	'sort'=>'1'
));
$menuids[] = $menu->add(array(
	'parentid'=>$menuid,
	'name'=>'方案设置',
	'url'=>'?app=mood&controller=mood',
	'sort'=>'2'
));
$menuids[] = $menu->add(array(
	'parentid'=>$menuid,
	'name'=>'设置',
	'url'=>'?app=mood&controller=setting&action=index',
	'sort'=>'3'
));
$installlog = str_replace('\\', '/', dirname(__FILE__)).'/install.log';
write_file($installlog, implode(',', $menuids));