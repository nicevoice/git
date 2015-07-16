<?php

class plugin_search extends object 
{
	private $article, $search;
	
	public function __construct(& $article)
	{
		$this->article = $article;
		$this->search = loader::model('search', 'search');
	}
	
	public function after_add()
	{
		if ($this->article->data['status'] == 6) $this->search->update($this->article->contentid, $this->article->data['content']);
	}
	
	public function after_edit()
	{
		if ($this->article->data['status'] == 6) $this->search->update($this->article->contentid, $this->article->data['content']);
	}
	
	public function after_publish()
	{
		$content = $this->article->get_field('content', $this->article->contentid);
		if ($content) $this->search->update($this->article->contentid, $content);
	}
	
	public function after_unpublish()
	{
		$this->search->delete($this->article->contentid);
	}
	
	public function after_remove()
	{
		$this->search->delete($this->article->contentid);
	}
	
	public function after_delete()
	{
		$this->search->delete($this->article->contentid);
	}
	
	public function after_restore()
	{
		$content = $this->article->get_field('content', $this->article->contentid);
		if ($content) $this->search->update($this->article->contentid, $content);
	}
	
	public function after_pass()
	{
		$content = $this->article->get_field('content', $this->article->contentid);
		if ($content) $this->search->update($this->article->contentid, $content);
	}
}