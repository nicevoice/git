<?php

class plugin_search extends object 
{
	private $video, $search;
	
	public function __construct(& $video)
	{
		$this->video = $video;
		$this->search = loader::model('search', 'search');
	}
	
	public function after_add()
	{
		if ($this->video->data['status'] == 6) $this->search->update($this->video->contentid, $this->video->data['content']);
	}
	
	public function after_edit()
	{
		if ($this->video->data['status'] == 6) $this->search->update($this->video->contentid, $this->video->data['content']);
	}
	
	public function after_publish()
	{
		$content = $this->video->get_field('content', $this->video->contentid);
		if ($content) $this->search->update($this->video->contentid, $content);
	}
	
	public function after_unpublish()
	{
		$this->search->delete($this->video->contentid);
	}
	
	public function after_remove()
	{
		$this->search->delete($this->video->contentid);
	}
	
	public function after_delete()
	{
		$this->search->delete($this->video->contentid);
	}
	
	public function after_restore()
	{
		$content = $this->video->get_field('content', $this->video->contentid);
		if ($content) $this->search->update($this->video->contentid, $content);
	}
	
	public function after_pass()
	{
		$content = $this->video->get_field('content', $this->video->contentid);
		if ($content) $this->search->update($this->video->contentid, $content);
	}
}