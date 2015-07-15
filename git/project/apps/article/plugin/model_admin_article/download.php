<?php

class plugin_download extends object 
{
	private $article;
	
	public function __construct(& $article)
	{
		$this->article = $article;
	}
	
	public function before_add()
	{
		$this->download();
	}
	
	public function before_edit()
	{
		$this->download();
	}
	
	private function download()
	{
		if (isset($this->article->data['saveremoteimage']) && $this->article->data['saveremoteimage'] && preg_match("/<img/i", $this->article->data['content']))
		{
			$attachment = loader::model('admin/attachment', 'system');
			$this->article->data['content'] = $attachment->download_by_content($this->article->data['content'], null, 'jpg|jpeg|gif|png|bmp', IMG_URL.'|'.UPLOAD_URL);
            		
			

			$setting = setting('article');
			// 网络图片本地化添加水印
			if ($setting['thumb_width'] || $setting['thumb_height'])
            {
            	$image = & factory::image();
            	if ($setting['thumb_width'] || $setting['thumb_height']) $image->set_thumb($setting['thumb_width'], $setting['thumb_height']);           	
				
            	$files = $attachment->get_files();
	            foreach ($files as $file)
	            {
	            	$file = UPLOAD_PATH.$file;
	            	if ($setting['thumb_width'] || $setting['thumb_height']) $image->thumb($file);
	            	if ($setting['watermark']) {
						$watermark = setting('system');
						if ($watermark['default_watermark'] && $watermark['watermark_enabled']) {
							$image->watermark($file);
						}
					}
	            }
            }
		}
	}
}