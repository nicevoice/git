<?php

class plugin_search extends object 
{
	private $activity, $search;
	
	public function __construct(& $activity)
	{
		$this->activity = $activity;
		$this->search = loader::model('search', 'search');
	}
	
	public function after_add()
	{
		if ($this->activity->data['status'] == 6) $this->search->update($this->activity->contentid, $this->activity->data['content']);
	}
	
	public function after_edit()
	{
		if ($this->activity->data['status'] == 6) $this->search->update($this->activity->contentid, $this->activity->data['content']);
	}
	
	public function after_publish()
	{
		$content = $this->activity->get_field('content', $this->activity->contentid);
		if ($content) $this->search->update($this->activity->contentid, $content);
	}
	
	public function after_unpublish()
	{
		$this->search->delete($this->activity->contentid);
	}
	
	public function after_remove()
	{
		$this->search->delete($this->activity->contentid);
	}
	
	public function after_delete()
	{
		$this->search->delete($this->activity->contentid);
	}
	
	public function after_restore()
	{
		$content = $this->activity->get_field('content', $this->activity->contentid);
		if ($content) $this->search->update($this->activity->contentid, $content);
	}
	
	public function after_pass()
	{
		$content = $this->activity->get_field('content', $this->activity->contentid);
		if ($content) $this->search->update($this->activity->contentid, $content);
	}
}