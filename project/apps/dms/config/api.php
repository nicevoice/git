<?php
return array(
	array('group'=>'文章', 'data'=>array(
		array('name'=>'文章添加', 'api'=>'article_add', 'default_priv'=>false),
		array('name'=>'文章获取', 'api'=>'article_get', 'default_priv'=>false),
		array('name'=>'文章查询', 'api'=>'article_query', 'default_priv'=>false),
		array('name'=>'文章编辑', 'api'=>'article_edit', 'default_priv'=>false), 
		array('name'=>'文章删除', 'api'=>'article_delete', 'default_priv'=>false)
	)),
	array('group'=>'图片', 'data'=>array(
		array('name'=>'图片添加', 'api'=>'picture_add', 'default_priv'=>false), 
		array('name'=>'图片获取', 'api'=>'picture_get', 'default_priv'=>false), 
		array('name'=>'图片查询', 'api'=>'picture_query', 'default_priv'=>false),
		array('name'=>'图片删除', 'api'=>'picture_delete', 'default_priv'=>false)
	)),
	array('group'=>'图组', 'data'=>array(
		array('name'=>'图组添加', 'api'=>'picture_group_add', 'default_priv'=>false), 
		array('name'=>'图组获取', 'api'=>'picture_group_get', 'default_priv'=>false), 
		array('name'=>'图组查询', 'api'=>'picture_group_query', 'default_priv'=>false)
	)),
	array('group'=>'附件', 'data'=>array(
		array('name'=>'附件添加', 'api'=>'attachment_add', 'default_priv'=>false),
		array('name'=>'附件获取', 'api'=>'attachment_get', 'default_priv'=>false),
		array('name'=>'附件查询', 'api'=>'attachment_query', 'default_priv'=>false),
		array('name'=>'附件删除', 'api'=>'attachment_delete', 'default_priv'=>false)
	)),
	array('group'=>'引用', 'data'=>array(
		array('name'=>'引用设置', 'api'=>'quote_set', 'default_priv'=>true),
		array('name'=>'引用获得', 'api'=>'quote_get', 'default_priv'=>true),
		array('name'=>'引用更新', 'api'=>'quote_update', 'default_priv'=>true)
	)),
	array('group'=>'系统', 'data'=>array(
		array('name'=>'日志查询', 'api'=>'log_query', 'default_priv'=>false),
		array('name'=>'日志读取', 'api'=>'log_get', 'default_priv'=>false),
		array('name'=>'测试连接', 'api'=>'test', 'default_priv'=>true)
	))
);