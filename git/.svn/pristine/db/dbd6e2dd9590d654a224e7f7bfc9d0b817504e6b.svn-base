<?php

class plugin_html extends object 
{
	private $vote, $category, $uri, $template, $json;
	
	public function __construct(& $vote)
	{
		$this->vote = $vote;
		$this->template = factory::template();
		$this->json = factory::json();
		$this->category = loader::model('category', 'system');
		$this->uri = loader::lib('uri', 'system');
		
		import('helper.folder');
	}
	
	public function after_add()
	{
		$this->write($this->vote->contentid);
	}
	
	public function after_edit()
	{
		$this->write($this->vote->contentid);
	}
	
	public function after_publish()
	{
		$this->write($this->vote->contentid);
	}
	
	public function after_unpublish()
	{
		$this->unlink($this->vote->contentid);
	}
	
	public function after_remove()
	{
		$this->unlink($this->vote->contentid);
	}
	
	public function before_delete()
	{
		$this->unlink($this->vote->contentid);
	}
	
	public function after_restore()
	{
		$this->write($this->vote->contentid);
	}
	
	public function after_pass()
	{
		$this->write($this->vote->contentid);
	}
	
	public function before_move()
	{
		$this->unlink($this->vote->contentid);
	}
	
	public function after_move()
	{
		$this->write($this->vote->contentid);
	}
	
	public function html_write()
	{
		$this->write($this->vote->contentid);
	}
	
	private function write($contentid)
	{
		$r = $this->vote->get($contentid, '*', 'show');
		if (!$r)
		{
			$this->error = $this->vote->error();
			return false;
		}
		if ($r['status'] != 6) return false;

		$template = $this->vote->content->template($r['catid'], $r['modelid']);
		if (!$template) $template = 'vote/show.html';
		
		$this->template->assign($r);
		$this->template->assign('head', array('title'=>$r['title']));
		$this->template->assign('pos', $this->category->pos($r['catid']));
		$data = $this->template->fetch($template);
		
		$r = $this->uri->content($contentid);
		$filename = $r['path'];
		folder::create(dirname($filename));
		write_file($filename, $data);
		return true;
	}
	
	private function unlink($contentid)
	{
		$r = $this->uri->content($contentid);
		if (!$r)
		{
			$this->error = '投票不存在';
			return false;
		}
		return @unlink($r['path']);
	}
}