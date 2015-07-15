<?php

class controller_article extends mobile_controller_abstract
{
	private $mobile;
	
	function __construct(& $app)
	{
		parent::__construct($app);
		$this->mobile = loader::model('mobile');
		if (!$this->setting['open']) $this->showmessage($this->setting['webname'].'的mobile服务已关闭', 'index.php');
	}

	function index()
	{
		exit;
	}
	
	function show()
	{
		$contentid = isset($_GET['contentid']) ? intval($_GET['contentid']) : 0;
		if (!$contentid)
		{
			exit;
		}
		$_key = 'mobile_article_show_'.$contentid;
		if(!$data = $this->cache->get($_key))
		{
			$data = array();
			$model = loader::model('admin/article', 'article');
			$data  = $model->get($contentid, 'catid,topicid,title,content,sourceid,published,comments','show');
			$data['contentid'] = $contentid;
			$data['published'] = date('Y-m-d H:i:s', $data['published']);
            $data['source'] = $data['source_name'];
            $data['content'] = strip_tags($data['content'],'<p><img><br><table><th><tr><td><b><strong><font>');
            // 处理图片
			$data['content'] = preg_replace_callback('/<img[^>]+src\s*\=\s*[\'"]?(([^>]*)(jpg|gif|png|bmp|jpeg))[\'"]?[^>]*>/i', array($this, 'content_thumb'), $data['content']);
			// 去除缩略图失败时的空图片
			$data['content'] = str_replace('<img src="" data-origin="" data-width="0" data-height="0" />','',$data['content']);
			// 去除不需要返回的字段
			unset($data['sourceid'],$data['source_name'],$data['source_url'],$data['source_logo'],$data['proids'],$data['placeid']);
			// 放入缓存
			if($this->setting['cache']) $this->cache->set($_key,$data,$this->setting['cache']);
		}
		echo json_encode(array($data));
	}
	
	function content_thumb($pic)
	{
		return $this->mobile->content_thumb($pic);
	}
}