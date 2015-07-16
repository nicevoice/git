<?php

class plugin_html extends object 
{
	private $video, $category, $uri, $template, $json;
	
	public function __construct(& $video)
	{
		$this->video = $video;
		$this->template = factory::template();
		$this->json = factory::json();
		$this->category = loader::model('category', 'system');
		$this->uri = loader::lib('uri', 'system');
		
		import('helper.folder');
	}
	
	public function after_add()
	{
		$this->write($this->video->contentid);
	}
	
	public function after_edit()
	{
		$this->write($this->video->contentid);
		if ($this->video->data['pagecount'] < $this->video->data['old_pagecount'])
		{
			$this->unlink($this->video->contentid, $this->video->data['pagecount']+1, $this->video->data['old_pagecount']);
		}
	}
	
	public function after_publish()
	{
		$this->write($this->video->contentid);
	}
	
	public function after_unpublish()
	{
		$this->delete($this->video->contentid);
	}
	
	public function after_remove()
	{
		$this->delete($this->video->contentid);
	}
	
	public function before_delete()
	{
		$this->delete($this->video->contentid);
	}
	
	public function after_restore()
	{
		$this->write($this->video->contentid);
	}
	
	public function after_pass()
	{
		$this->write($this->video->contentid);
	}

	public function before_move()
	{
		$this->delete($this->video->contentid);
	}
	
	public function after_move()
	{
		$this->write($this->video->contentid);
	}
	
	public function html_write()
	{
		$this->write($this->video->contentid);
	}
	
	private function write($contentid)
	{
		$r = $this->video->get($contentid, '*', 'show', true, true);
		if (!$r)
		{
			$this->error = $this->video->error();
			return false;
		}
		if ($r['status'] != 6) return false;

		$this->template->assign('pos', $this->category->pos($r['catid']));
		
		$template = $this->video->content->template($r['catid'], $r['modelid']);
		if (!$template) $template = 'video/show.html';
		
		$r['autostart'] = 'true';
		
		$fileext = fileext($r['video']);
		if(preg_match('/^(\[cc\])([^\[]+)(\[\/cc\])$/i', $r['video'], $matches)) 
		{
			$r['video'] = $matches[2];
			$r['player'] = 'cc';
		}
		elseif(preg_match('/^(\[ctvideo\])([^\[]+)(\[\/ctvideo\])$/i', $r['video'], $matches)) 
		{
			$r['video'] = $matches[2];
			$r['player'] = 'ctvideo';
			$new_setting = new setting();
			$video_setting = $new_setting->get('video');
			$r['playerurl'] = $video_setting['player'];
			$new_setting = $video_setting = NULL;
		}
		elseif($fileext && strlen($fileext)<7)
		{
			$r['player'] = $fileext;	
		}
		else 
		{
			$r['player'] = 'swf';
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
	
	private function delete($contentid)
	{
		$r = $this->uri->content($contentid);
		if (!$r)
		{
			$this->error = '视频不存在';
			return false;
		}
		return @unlink($r['path']);
	}
}