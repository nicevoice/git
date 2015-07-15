<?php
//专栏

class plugin_space extends object 
{
	private $article;
	
	public function __construct(& $article)
	{
		$this->article = $article;
	}
	
	public function before_add()
	{
		$this->spaceid();
	}
	
	public function before_edit()
	{
		$this->spaceid();
	}
	
	public function after_get()
	{
		$this->space();
	}
	
	private function spaceid()
	{
		$space = loader::model('space', 'space');
		$this->article->data['spaceid'] = $space->spaceid($this->article->data['author']);
	}
	
	private function space()
	{
		if (!$this->article->data['spaceid']) return false;
		if ($this->article->data['spaceid'])
		{
			$r = table('space', $this->article->data['spaceid']);
			$this->article->data['author_url'] = SPACE_URL.$r['alias'];
			$this->article->data['author_name'] = $this->article->data['author'] = $r['author'];
		}
	}
	
	public function after_add()
	{
		$this->renew_stat();
	}
	
	public function after_edit()
	{
		$this->renew_stat();
	}
	
	public function after_publish()
	{
		$this->renew_stat();
	}
	
	public function after_unpublish()
	{
		$this->renew_stat();
	}
	
	public function after_restore()
	{
		$this->renew_stat();
	}
	
	public function after_pass()
	{
		$this->renew_stat();
	}
	
	public function after_remove()
	{
		$this->renew_stat();
	}
	
	public function after_delete()
	{
		
	}
	
	private function renew_stat($action,$contentid)
	{
		$spaceid = $this->article->content->get_field('spaceid',$this->article->contentid);
		if(empty($spaceid)) return;
		$space = loader::model('space', 'space');
		$total = $this->article->content->count("`spaceid`=$spaceid AND status=6");
		$space->set_field('posts',$total,"`spaceid`=$spaceid");
	}
}