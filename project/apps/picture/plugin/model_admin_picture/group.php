<?php
class plugin_group extends object 
{
	private $picture, $group;
	
	public function __construct(& $picture)
	{
		$this->picture = $picture;
		$this->group = loader::model('admin/picture_group','picture');
	}
	
	public function after_add()
	{
		if (!isset($this->picture->data['pictures']) || empty($this->picture->data['pictures'])) return false;
		foreach ($this->picture->data['pictures'] as $k=>$r)
		{
			$r['contentid'] = $this->picture->contentid;
			$this->group->add($r);
		}
	}
	
	public function after_edit()
	{
		if (!isset($this->picture->data['pictures']) || empty($this->picture->data['pictures'])) return false;
		
		$uri = loader::lib('uri', 'system');
		
		$page = 1;
		$pictureid = array();
		foreach ($this->picture->data['pictures'] as $k=>$r)
		{
			$u = $uri->content($this->picture->contentid, $page);
			$r['url'] = $u['url'];
			if ($r['pictureid'])
			{
				$this->group->edit($r['pictureid'], $r);
				$pictureid[] = $r['pictureid'];
			}
			else 
			{
				$r['contentid'] = $this->picture->contentid;
				$pictureid[] = $this->group->add($r);
			}
			$page++;
		}
		$this->group->delete_by($this->picture->contentid, $pictureid);
	}
	
	public function after_get()
	{
		$this->picture->data['pictures'] = $this->group->ls($this->picture->contentid);
		
		if ($this->picture->action == 'show')
		{
			$uri = loader::lib('uri', 'system');
			$pagecount = count($this->picture->data['pictures']);
			$pages = array();
			foreach ($this->picture->data['pictures'] as $k=>$r)
			{
				$page = $k+1;
				$r['prevpage'] = $page == 1 ? '' : $page-1;
				$r['nextpage'] = $page == $pagecount ? '' : $page+1;
				$u = $uri->content($this->picture->contentid, $page);
				$r['path'] = $u['path'];
				$r['url'] = $u['url'];
				$pages[$page] = $r;
			}
			$this->picture->data['pages'] = $pages;
			
			$ext = pathinfo($pages[1]['path'], PATHINFO_EXTENSION);
			$this->picture->data['all']['path'] = str_replace('.'.$ext, '_all.'.$ext, $pages[1]['path']);
			$this->picture->data['all']['url'] = str_replace('.'.$ext, '_all.'.$ext, $pages[1]['url']);
		}
	}
	
	public function after_delete()
	{
		$this->group->rm($this->picture->contentid);
	}
}