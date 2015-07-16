<?php

class plugin_html extends object 
{
	private $interview, $category, $uri, $template, $json;
	
	public function __construct(& $interview)
	{
		$this->interview = $interview;
		$this->template = factory::template();
		$this->json = factory::json();
		$this->category = loader::model('category', 'system');
		$this->uri = loader::lib('uri', 'system');
		
		import('helper.folder');
	}
	
	public function after_add()
	{
		$this->write($this->interview->contentid);
	}
	
	public function after_edit()
	{
		$this->write($this->interview->contentid);
	}
	
	public function after_publish()
	{
		$this->write($this->interview->contentid);
	}
	
	public function after_unpublish()
	{
		$this->unlink($this->interview->contentid);
	}
	
	public function after_remove()
	{
		$this->unlink($this->interview->contentid);
	}
	
	public function before_delete()
	{
		$this->unlink($this->interview->contentid);
	}
	
	public function after_restore()
	{
		$this->write($this->interview->contentid);
	}
	
	public function after_pass()
	{
		$this->write($this->interview->contentid);
	}
	
	public function before_move()
	{
		$this->unlink($this->interview->contentid);
	}
	
	public function after_move()
	{
		$this->write($this->interview->contentid);
	}
	
	public function review()
	{
		$this->write($this->interview->contentid);
	}
	
	public function notice()
	{
		$this->write($this->interview->contentid);
	}
	
	public function picture()
	{
		$this->write($this->interview->contentid);
	}
	
	public function state()
	{
		$this->write($this->interview->contentid);
	}
	
	public function html_write()
	{
		$this->write($this->interview->contentid);
	}
	
	private function write($contentid)
	{
		$r = $this->interview->get($contentid, '*', 'show');
		if (!$r)
		{
			$this->error = $this->interview->error();
			return false;
		}
		if ($r['status'] != 6) return false;

		$this->template->assign('pos', $this->category->pos($r['catid']));
		
		$template = $r['template'] ? $r['template'] : $this->interview->content->template($r['catid'], $r['modelid']);
		if (!$template) $template = 'interview/show.html';
		
		$r['autostart'] = 'true';
		$videoext = array(
			'rm' => 'rmrmvb',
			'rmvb' => 'rmrmvb',
			'swf' => 'flash',
			'flv' => 'flv',
			'wmv' => 'wmv',
			'avi' => 'wmv'
		);
		$fileext = fileext($r['video']);
		if(preg_match('/^(\[cc\])([^\[]+)(\[\/cc\])$/i', $r['video'], $matches)) 
		{
			$r['video'] = $matches[2];
			$r['player'] = 'cc';
		}
		elseif(array_key_exists($fileext, $videoext))
		{
			$r['player'] = $videoext[$fileext];	
		}
		else 
		{
			$r['player'] = 'flash';
		}
		
		$this->template->assign($r);
		$this->template->assign('head', array('title'=>$r['title']));
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
			$this->error = '访谈不存在';
			return false;
		}
		return @unlink($r['path']);
	}
}