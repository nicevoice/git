<?php

class plugin_html extends object 
{
	private $activity, $category, $uri, $template, $json;
	
	public function __construct(& $activity)
	{
		$this->activity = $activity;
		$this->template = factory::template();
		$this->json = factory::json();
		$this->category = loader::model('category', 'system');
		$this->uri = loader::lib('uri', 'system');
		import('helper.folder');
	}
	
	public function after_add()
	{
		$this->write($this->activity->contentid);
	}
	
	public function after_edit()
	{
		$this->write($this->activity->contentid);
		if ($this->picture->data['pagecount'] < $this->picture->data['old_pagecount'])
		{
			$this->unlink($this->activity->contentid, $this->picture->data['pagecount']+1, $this->picture->data['old_pagecount']);
		}
	}
	
	public function after_publish()
	{
		$this->write($this->activity->contentid);
	}
	
	public function after_unpublish()
	{
		$this->delete($this->activity->contentid);
	}
	
	public function after_remove()
	{
		$this->delete($this->activity->contentid);
	}
	
	public function before_delete()
	{
		$this->delete($this->activity->contentid);
	}
	
	public function after_restore()
	{
		$this->write($this->activity->contentid);
	}
	
	public function after_pass()
	{
		$this->write($this->activity->contentid);
	}
	
	public function before_move()
	{
		$this->delete($this->activity->contentid);
	}
	
	public function after_move()
	{
		$this->write($this->activity->contentid);
	}
	
	public function html_write()
	{
		$this->write($this->activity->contentid);
	}
	
	private function write($contentid)
	{
		$r = $this->activity->get($contentid, '*', 'show');
		$r['starttime'] = strtotime($r['starttime']);
		$r['endtime'] = !empty($r['endtime'])?strtotime($r['endtime']):'';
		$r['signstart'] = strtotime($r['signstart']);
		$r['signend'] = !empty($r['signend'])?strtotime($r['signend']):'';
		if (!$r)
		{
			$this->error = $this->activity->error();
			return false;
		}
		if ($r['status'] != 6) return false;
		
		$this->sign = loader::model('admin/sign','activity');
		$signed = $this->sign->ls("contentid = $contentid AND  state = 1");
		$r['signed'] = $signed;
		$this->template->assign('pos', $this->category->pos($r['catid']));

		$template = $this->activity->content->template($r['catid'], $r['modelid']);
		if (!$template) $template = 'activity/show.html';
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
			$this->error = '活动不存在';
			return false;
		}
		return @unlink($r['path']);
	}
}