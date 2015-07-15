<?php

class plugin_search extends object 
{
	private $interview, $search;
	
	public function __construct(& $interview)
	{
		$this->interview = $interview;
		$this->search = loader::model('search', 'search');
	}
	
	public function after_add()
	{
		if ($this->interview->data['status'] == 6)
		{
			$this->search->update($this->interview->contentid, $this->get_content($this->interview->data));
		}
	}
	
	public function after_edit()
	{
		if ($this->interview->data['status'] == 6)
		{
			$data = $this->interview->data;
			$data['chat'] = loader::model('chat','interview')->ls($this->interview->contentid);
			$data['question'] = loader::model('question','interview')->ls($this->interview->contentid);
			$this->search->update($this->interview->contentid, $this->get_content($data));
		}
	}
	
	public function after_publish()
	{
		$this->update($this->interview->contentid);
	}
	
	public function after_unpublish()
	{
		$this->search->delete($this->interview->contentid);
	}
	
	public function after_remove()
	{
		$this->search->delete($this->interview->contentid);
	}
	
	public function after_delete()
	{
		$this->search->delete($this->interview->contentid);
	}
	
	public function after_restore()
	{
		$this->update($this->interview->contentid);
	}
	
	public function after_pass()
	{
		$this->update($this->interview->contentid);
	}
	
	public function review()
	{
		$this->update($this->interview->contentid);
	}
	
	public function notice()
	{
		$this->update($this->interview->contentid);
	}
	
	public function picture()
	{
		$this->update($this->interview->contentid);
	}
	
	public function state()
	{
		$this->update($this->interview->contentid);
	}
	
	private function get_content($data)
	{
		$content = array();
		$content[] = $data['description'];
		$content[] = $data['address'];
		$content[] = $data['compere'];
		$content[] = $data['review'];
		$content[] = $data['notice'];
		if (isset($data['guest']) && is_array($data['guest']))
		{
			foreach ($data['guest'] as $r)
			{
				$content[] = $r['name'];
				$content[] = $r['resume'];
			}
		}
		if (isset($data['chat']) && is_array($data['chat']))
		{
			foreach ($data['chat'] as $r)
			{
				$content[] = $r['content'];
			}
		}
		if (isset($data['question']) && is_array($data['question']))
		{
			foreach ($data['question'] as $r)
			{
				$content[] = $r['nickname'];
				$content[] = $r['content'];
			}
		}
		return implode(' ', $content);
	}
	
	private function update($contentid)
	{
		$data = $this->interview->get($contentid);
		if ($data)
		{
			$data['chat'] = loader::model('chat','interview')->ls($contentid);
			$data['question'] = loader::model('question','interview')->ls($contentid);
		}
		return $this->search->update($contentid, $this->get_content($data));
	}
}