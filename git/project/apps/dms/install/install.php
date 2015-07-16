<?php
$menu = loader::model('admin/menu', 'system');

$menuids = array();
$menuid = $menu->add(array(
	'parentid'=>5,
	'name'=>'数据中心',
	'url'=>'?app=dms&controller=article&action=index',
	'sort'=>''
));
$menuids[] = $menuid;
$installlog = str_replace('\\', '/', dirname(__FILE__)).'/install.log';
write_file($installlog, implode(',', $menuids));