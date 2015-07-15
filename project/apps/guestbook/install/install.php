<?php
$menu = loader::model('admin/menu', 'system');

$menuids = array();
$menuid = $menu->add(array(
	'parentid'=>5,
	'name'=>'留言本',
	'url'=>'?app=guestbook&controller=guestbook&action=index',
	'sort'=>'7'
));
$menuids[] = $menuid;
$menuids[] = $menu->add(array(
	'parentid'=>$menuid,
	'name'=>'管理留言',
	'url'=>'?app=guestbook&controller=guestbook&action=index',
	'sort'=>'1'
));
$menuids[] = $menu->add(array(
	'parentid'=>$menuid,
	'name'=>'类别设置',
	'url'=>'?app=guestbook&controller=type&action=index',
	'sort'=>'2'
));
$menuids[] = $menu->add(array(
	'parentid'=>$menuid,
	'name'=>'设置',
	'url'=>'?app=guestbook&controller=setting&action=index',
	'sort'=>'3'
));

$installlog = str_replace('\\', '/', dirname(__FILE__)).'/install.log';
write_file($installlog, implode(',', $menuids));