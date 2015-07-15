<?php

class plugin_search extends object 
{
	private $picture, $search;
	
	public function __construct(& $picture)
	{
		$this->picture = $picture;
		$this->search = loader::model('search', 'search');
	}
	
	public function after_add()
	{
		if ($this->picture->data['status'] == 6)
		{
			$this->search->update($this->picture->contentid, $this->get_content($this->picture->data));
		}
	}
	
	public function after_edit()
	{
		if ($this->picture->data['status'] == 6)
		{
			$this->search->update($this->picture->contentid, $this->get_content($this->picture->data));
		}
	}
	
	public function after_publish()
	{
		$data = $this->picture->get($this->picture->contentid);
		if ($data) $this->search->update($this->picture->contentid, $this->get_content($data));
	}
	
	public function after_unpublish()
	{
		$this->search->delete($this->picture->contentid);
	}
	
	public function after_remove()
	{
		$this->search->delete($this->picture->contentid);
	}
	
	public function after_delete()
	{
		$this->search->delete($this->picture->contentid);
	}
	
	public function after_restore()
	{
		$data = $this->picture->get($this->picture->contentid);
		if ($data) $this->search->update($this->picture->contentid, $this->get_content($data));
	}
	
	public function after_pass()
	{
		$data = $this->picture->get($this->picture->contentid);
		if ($data) $this->search->update($this->picture->contentid, $this->get_content($data));
	}
	
	private function get_content($data)
	{
		$content = array();
		$content[] = $data['description'];
		if (is_array($data['pictures']))
		{
			foreach ($data['pictures'] as $r)
			{
				$content[] = $r['note'];
			}
		}
		return implode(' ', $content);
	}
}