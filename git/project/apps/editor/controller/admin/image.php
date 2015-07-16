<?php
/**
 * 图片
 *
 * @aca public 图片
 */
class controller_admin_image extends editor_controller_abstract
{	
	function __construct(& $app)
	{
		parent::__construct($app);
	}
	
	function index()
	{
		$setting	= setting('system');
		$use_watermark = setting('editor', 'watermark') && $setting['watermark_enabled'];
		$watermark	= loader::model('admin/watermark', 'system')->select('disable=0' ,'`watermarkid` as id, `name`');
		$dmsc = setting::get('dmsc', 'status');
		$this->view->assign('dmsc', (bool)$dmsc);
		$this->view->assign('default_watermark', $setting['default_watermark']);
		$this->view->assign('use_watermark', $use_watermark);
		$this->view->assign('watermark', $watermark);
		$this->view->display('image');
	}

    function edit()
	{
		$this->view->display('image_edit');
	}
}