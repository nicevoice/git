<?php
$menu = loader::model('admin/menu', 'system');

$menuids = array();
$menuid = $menu->add(array(
	'parentid'=>null,
	'name'=>'页面',
	'url'=>'?app=page&controller=page&action=index',
	'sort'=>'3'
));
$menuids[] = $menuid;

$installlog = str_replace('\\', '/', dirname(__FILE__)).'/install.log';
write_file($installlog, implode(',', $menuids));