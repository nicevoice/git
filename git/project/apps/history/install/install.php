<?php
$menu = loader::model('admin/menu', 'system');

$menuids = array();
$menuid = $menu->add(array(
	'parentid'=>5,
	'name'=>'历史页面',
	'url'=>'?app=history&controller=history&action=index',
	'sort'=>'1'
));
$menuids[] = $menuid;
$installlog = str_replace('\\', '/', dirname(__FILE__)).'/install.log';
write_file($installlog, implode(',', $menuids));