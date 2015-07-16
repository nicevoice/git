<?php
class controller_image extends editor_controller_abstract
{	
	function __construct(& $app)
	{
		parent::__construct($app);
	}
	
	function index()
	{
		$this->view->display('image_frontend');
	}
	
	function upload()
	{
		$attachment = loader::model('admin/attachment', 'system');
		$file = $attachment->upload('ctimg',true, null,'jpg|jpeg|png|bmp|gif',1024*1024*1024,array());
		
		$setting = setting('editor');
		if ($setting['watermark'] || $setting['thumb_width'] || $setting['thumb_height'])
        {
        	$image = & factory::image();
        	if ($setting['thumb_width'] || $setting['thumb_height']) $image->set_thumb($setting['thumb_width'], $setting['thumb_height']);           	
        	$sfile = UPLOAD_PATH.$file;
        	if ($setting['thumb_width'] || $setting['thumb_height']) $image->thumb($sfile);
        	if ($setting['watermark']) {
				$watmark = setting('system');
				if ($watermark['default_watermark'] && $watermark['watermark_enabled'])
				{
					$image->watermark($sfile);
				}
			}
        }
        
		echo UPLOAD_URL.$file;
	}
}