<?php

class plugin_search extends object 
{
	private $special, $search;
	
	public function __construct(& $special)
	{
		$this->special = $special;
		$this->search = loader::model('search', 'search');
	}
	
	public function after_add()
	{
		if ($this->special->data['status'] == 6) $this->search->update($this->special->contentid, $this->special->data['content']);
	}
	
	public function after_publish()
	{
		$content = $this->special->get_field('content', $this->special->contentid);
		if ($content) $this->search->update($this->special->contentid, $content);
	}
	
	public function after_unpublish()
	{
		$this->search->delete($this->special->contentid);
	}
	
	public function after_remove()
	{
		$this->search->delete($this->special->contentid);
	}
	
	public function after_delete()
	{
		$this->search->delete($this->special->contentid);
	}
	
	public function after_restore()
	{
		$content = $this->special->get_field('content', $this->special->contentid);
		if ($content) $this->search->update($this->special->contentid, $content);
	}
	
	public function after_pass()
	{
		$content = $this->special->get_field('content', $this->special->contentid);
		if ($content) $this->search->update($this->special->contentid, $content);
	}
}