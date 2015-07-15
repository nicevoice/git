<?php
class controller_article extends article_controller_abstract
{
	private $article;

	function __construct(&$app)
	{
		parent::__construct($app);
		$this->article = loader::model('admin/article');
		$this->category = loader::model('category', 'system');
	}
	
	function fulltext()
	{
		$contentid = $_GET['contentid'];
		$r = $this->article->get($contentid, 'content');
		$r['content'] = preg_replace('/<p\s*[^>]*>(\s|&nbsp;)*<\/p>/isU', '', $r['content']);
		$r = $this->json->encode($r);
		echo $_GET['jsoncallback']."($r);";
	}
	
	function printing()
	{
		$contentid = intval($_GET['contentid']);
		if ($this->system['pagecached'])
		{
			$keyid = md5('pagecached_article_article_printing_' .$contentid);
			cmstop::cache_start($this->system['pagecachettl'], $keyid);
		}
				
		$data = $this->article->get($contentid);
		if($data['modelid']!=1) return $this->showmessage('没有此打印内容！');
		$this->template->assign('pos', $this->category->pos($data['catid']));
		$this->template->assign($data);
		$this->template->display('article/print.html');
		
		if ($this->system['pagecached']) cmstop::cache_end();
	}
}