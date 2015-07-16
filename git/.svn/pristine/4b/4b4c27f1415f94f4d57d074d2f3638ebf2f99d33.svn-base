<?php
// 编辑后保存版本
class plugin_version extends object 
{
	private $article;
	
	public function __construct(& $article)
	{
		$this->article = $article;
	}

	public function after_edit()
	{
		$data = $this->article->data;
		$title = $data['title'];
		$content = $data['content'];
		$contentid = $data['contentid'];
		if($contentid && $title)
		{
			$version = loader::model('admin/version', 'system');
			$old = $version->get_by('contentid', $contentid, 'title, data');
			$olddata= unserialize($old['data']);
			// 标题或者内容不同时 才保存一个版本
			if($title != $old['title'] || $content != $olddata['content'])
			{
				$version->add($contentid, $title, serialize($data));
			}
		}
	}
}