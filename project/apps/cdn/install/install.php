<?php
$menu = loader::model('admin/menu', 'system');

$menuids = array();
$menuid = $menu->add(array(
	'parentid'=>5,
	'name'=>'CDN接口',
	'url'=>'?app=cdn&controller=cdn&action=index',
	'sort'=>''
));
$menuids[] = $menuid;
$menuids[] = $menu->add(array(
	'parentid'=>$menuid,
	'name'=>'CDN设置',
	'url'=>'?app=cdn&controller=setting&action=index',
	'sort'=>'1'
));
$installlog = str_replace('\\', '/', dirname(__FILE__)).'/install.log';
write_file($installlog, implode(',', $menuids));