<?php

class plugin_html extends object 
{
	private $survey, $category, $uri, $template, $json;
	
	public function __construct(& $survey)
	{
		$this->survey = $survey;
		$this->template = factory::template();
		$this->json = factory::json();
		$this->category = loader::model('category', 'system');
		$this->uri = loader::lib('uri', 'system');
		
		import('helper.folder');
	}
	
	public function after_add()
	{
		$this->write($this->survey->contentid);
	}
	
	public function after_edit()
	{
		$this->write($this->survey->contentid);
	}
	
	public function after_publish()
	{
		$this->write($this->survey->contentid);
	}
	
	public function after_unpublish()
	{
		$this->unlink($this->survey->contentid);
	}
	
	public function after_remove()
	{
		$this->unlink($this->survey->contentid);
	}
	
	public function before_delete()
	{
		$this->unlink($this->survey->contentid);
	}
	
	public function after_restore()
	{
		$this->write($this->survey->contentid);
	}
	
	public function after_pass()
	{
		$this->write($this->survey->contentid);
	}
	
	public function before_move()
	{
		$this->unlink($this->survey->contentid);
	}
	
	public function after_move()
	{
		$this->write($this->survey->contentid);
	}
	
	public function html_write()
	{
		$this->write($this->survey->contentid);
	}
	
	private function write($contentid)
	{
		$r = $this->survey->get($contentid);
		if (!$r)
		{
			$this->error = $this->survey->error();
			return false;
		}
		if ($r['status'] != 6) return false;
		
		$this->question = loader::model('admin/question','survey');
		
		$questions = $this->question->ls($contentid);

		$template = $r['template'] ? $r['template'] : $this->survey->content->template($r['catid'], $r['modelid']);
        if (!$template) $template = 'survey/show.html';
        
		$this->template->assign($r);
		$this->template->assign('head', array('title'=>$r['title']));
		$this->template->assign('pos', $this->category->pos($r['catid']));
		$this->template->assign('questions', $questions);
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
			$this->error = '调查不存在';
			return false;
		}
		return @unlink($r['path']);
	}
}