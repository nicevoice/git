<?php
/* 
 * 模型的右键菜单定义,由apps/system/lib/common.php的right_menu函数调用
 * 通常复制article模型的配置即可，详细说明及特殊菜单项的设置参考本文件
 * 
 * class: li的类名 *
 * text:  显示文本 *
 * handler: 触发的函数或方法名,如: edit或article.edit, 如果不定义,默认为content.class名
 * aca: 调用priv::aca方法检查权限,默认检查model/model/class
 *		例1: 有interview/chat/index权限才显示访谈的文字实录菜单项
 *			array('class' => 'chat', 'text' => '文字实录', 'aca'=>'interview/chat/index')
 *			因为app与模型名相同,aca可简化为chat/index
 *		例2: 有interview/interview/abc权限才显示访谈的文字实录菜单项
 *			array('class' => 'chat', 'text' => '文字实录', 'aca'=>'interview/interview/abc')
 *			因为app,controller都和模型名相同,aca可简化为abc
 *		例3: 有interview/interview/edit权限才显示访谈的编辑菜单项
 *			array('class' => 'edit', 'text' => '文字实录', 'aca'=>'interview/interview/edit')
 *			因为app,controller都和模型名相同,action与class相同,所以此时aca可以完全省略,这是最通常的情况
 * status: 根据状态显示菜单项,如果不限制可省略此参数
 *		例1: 只在4,5,6状态显示
 *			array('class' => 'createhtml', 'text' => '生成', status=>'5,6'),
 *		例2: 状态0,1时不显示删除
 *			array('class' => 'remove', 'text' => '删除', 'status'=>'!0,1'),
 * separator: 添加一条横线(加一个类：separator)
 */
return array(
	array('class' => 'view', 'text' => '查看'),
	array('class' => 'edit', 'text' => '编辑'),
	array('class' => 'design', 'text' => '设计'),

    array('class' => 'unpublish', 'text' => '下线', 'status' => '6'),
	array('class' => 'remove', 'text' => '删除', 'status' => '!0'),
	array('class' => 'del', 'text' => '删除', 'status' => '0'),
	array('class' => 'restore', 'text' => '还原', 'status' => '0'),
	array('class' => 'approve', 'text' => '送审', 'status' => '1,2'),
	array('class' => 'pass', 'text' => '通过', 'status' => '2'),
	array('class' => 'publish', 'text' => '发布', 'status' => '!6'),

	array('class' => 'forward', 'text' => '转发', 'status' => '6', 'separator' => 1),
	array('class' => 'move', 'text' => '移动'),
	array('class' => 'copy', 'text' => '复制'),
	array('class' => 'reference', 'text' => '引用'),
	array('class' => 'score', 'text' => '评分'),
	array('class' => 'note', 'text' => '批注')
);