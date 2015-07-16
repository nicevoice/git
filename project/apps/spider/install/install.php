<?php
$menu = loader::model('admin/menu', 'system');

$menuids = array();
$menuid = $menu->add(array(
	'parentid'=>5,
	'name'=>'文章采集',
	'url'=>'?app=spider&controller=spider',
	'sort'=>''
));
$menuids[] = $menuid;
$menuids[] = $menu->add(array(
	'parentid'=>$menuid,
	'name'=>'采集',
	'url'=>'?app=spider&controller=spider',
	'sort'=>'4'
));
$menuids[] = $menu->add(array(
	'parentid'=>$menuid,
	'name'=>'添加规则',
	'url'=>'?app=spider&controller=manager&action=addrule',
	'sort'=>'2'
));
$menuids[] = $menu->add(array(
	'parentid'=>$menuid,
	'name'=>'管理规则',
	'url'=>'?app=spider&controller=manager&action=index',
	'sort'=>'3'
));
$menuids[] = $menu->add(array(
	'parentid'=>$menuid,
	'name'=>'站点管理',
	'url'=>'?app=spider&controller=manager&action=sites',
	'sort'=>'4'
));
$installlog = str_replace('\\', '/', dirname(__FILE__)).'/install.log';
write_file($installlog, implode(',', $menuids));