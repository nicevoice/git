<?php

class plugin_html extends object 
{
	private $picture, $category, $uri, $template, $json;
	
	public function __construct(& $picture)
	{
		$this->picture = $picture;
		$this->template = factory::template();
		$this->json = factory::json();
		$this->category = loader::model('category', 'system');
		$this->uri = loader::lib('uri', 'system');
		
		import('helper.folder');
	}
	
	public function after_add()
	{
		$this->write($this->picture->contentid);
	}
	
	public function after_edit()
	{
		$this->write($this->picture->contentid);
		if ($this->picture->data['total'] < $this->picture->data['old_total'])
		{
			$this->unlink($this->picture->contentid, $this->picture->data['total']+1, $this->picture->data['old_total']);
		}
	}
	
	public function after_publish()
	{
		$this->write($this->picture->contentid);
	}
	
	public function after_unpublish()
	{
		$this->delete($this->picture->contentid);
	}
	
	public function after_remove()
	{
		$this->delete($this->picture->contentid);
	}
	
	public function before_delete()
	{
		$this->delete($this->picture->contentid);
	}
	
	public function after_restore()
	{
		$this->write($this->picture->contentid);
	}
	
	public function after_pass()
	{
		$this->write($this->picture->contentid);
	}
	
	public function before_move()
	{
		$this->delete($this->picture->contentid);
	}
	
	public function after_move()
	{
		$this->write($this->picture->contentid);
	}
	
	public function html_write()
	{
		$this->write($this->picture->contentid);
	}
	
	private function write($contentid)
	{
		$r = $this->picture->get($contentid, '*', 'show');
		if (!$r)
		{
			$this->error = $this->picture->error();
			return false;
		}
		if ($r['status'] != 6 || $r['total'] == 0) return false;

		$this->template->assign('pos', $this->category->pos($r['catid']));
		$this->template->assign('head', array('title'=>$r['title']));
		$comment	= setting::get('comment');
		$this->template->assign('isseccode', $comment['isseccode']);
		
		$template = $this->picture->content->template($r['catid'], $r['modelid']);
        if (!$template) $template = 'picture/show.html';

		for ($page = 1; $page <= $r['total']; $page++)
		{
			$picture = array_merge($r, $r['pages'][$page]);
			$filename = $r['pages'][$page]['path'];
			$this->template->assign('page', $page);
			$this->template->assign($picture);
			$data = $this->template->fetch($template);
			if ($page == 1) folder::create(dirname($filename));
			write_file($filename, $data);
		}

		$this->template->assign($r);
		$data = $this->template->fetch('picture/show_all.html');
		write_file($r['all']['path'], $data);
		
		return true;
	}
	
	private function delete($contentid)
	{
		$r = $this->picture->get($contentid, '*');
		if (!$r)
		{
			$this->error = $this->picture->error();
			return false;
		}
		if ($r['total']) $this->unlink($contentid, 1, $r['total']);
		return true;
	}
	
	private function unlink($contentid, $start, $end)
	{
		for ($page = $start; $page <= $end; $page++)
		{
			$r = $this->uri->content($contentid, $page);
			@unlink($r['path']);
			if ($page == 1)
			{
				$ext = pathinfo($r['path'], PATHINFO_EXTENSION);
				$path = str_replace('.'.$ext, '_all.'.$ext, $r['path']);
				@unlink($path);
			}
		}
		return true;
	}
}