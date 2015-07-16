<?php
$menu = loader::model('admin/menu', 'system');
$installlog = str_replace('\\', '/', dirname(__FILE__)).'/install.log';
$menuids = @file_get_contents($installlog);
if ($menuids)
{
	$menu->delete($menuids);
}
@unlink($installlog);