<?php

class plugin_html extends object 
{
	private $article, $category, $uri, $template, $json, $box=array(), $i=0;
	
	public function __construct(& $article)
	{
		$this->article = $article;
		$this->template = factory::template();
		$this->json = factory::json();
		$this->category = loader::model('category', 'system');
		$this->uri = loader::lib('uri', 'system');
		
		import('helper.folder');
	}
	
	public function after_add()
	{
		$this->write($this->article->contentid);
	}
	
	public function after_edit()
	{
		$this->write($this->article->contentid);
		if ($this->article->data['pagecount'] < $this->article->data['old_pagecount'])
		{
			$this->unlink($this->article->contentid, $this->article->data['pagecount']+1, $this->article->data['old_pagecount']);
		}
	}
	
	public function after_publish()
	{
		$this->write($this->article->contentid);
	}
	
	public function after_unpublish()
	{
		$this->delete($this->article->contentid);
	}
	
	public function after_remove()
	{
		$this->delete($this->article->contentid);
	}
	
	public function before_delete()
	{
		$this->delete($this->article->contentid);
	}
	
	public function after_restore()
	{
		$this->write($this->article->contentid);
	}
	
	public function after_pass()
	{
		$this->write($this->article->contentid);
	}
	
	public function after_copy()
	{
		$this->write($this->article->contentid);
	}
	
	public function before_move()
	{
		$this->delete($this->article->contentid);
	}
	
	public function after_move()
	{
		$this->write($this->article->contentid);
	}
	
	public function html_write()
	{
		$this->write($this->article->contentid);
	}
	
	private function keywords($content)
	{
		$keyword = $url = '';
		$data = table('keyword');
		if (!$data) return $content;
		$this->box = array();
		$content = preg_replace("/(<a\s[^>]*>.*<\/a>|<img\s[^>]*\/>)/iseU", '$this->_store("\1")', $content);
		foreach ($data as $k)
		{
			$keyword = $k['name'];
			$url = '<a href="'.$k['url'].'" target="_blank">'.$k['name'].'</a>';
			$content = str_replace_once($keyword, $url, $content);
		}
		$this->i = 0;
		return  preg_replace("/__ct__/se", '$this->_output()', $content);
		
	}
	
	private function _store($ht)
	{
		$this->box[] = $ht;
		return '__ct__';
	}
	
	private function _output()
	{
		return $this->box[$this->i++];	
	}

	private function write($contentid)
	{
		$r = $this->article->get($contentid, '*', 'show');
		if (!$r)
		{
			$this->error = $this->article->error();
			return false;
		}
		$field = $this->get_content_file($contentid);
		$field && $r['field'] = $field;	// 将自定义字段合并到 $r

		if ($r['status'] != 6) return false;

		$this->template->assign('pos', $this->category->pos($r['catid']));
		$comment	= setting::get('comment');
		$this->template->assign('isseccode', $comment['isseccode']);

		$template = $this->article->content->template($r['catid'], $r['modelid']);
		if (!$template) $template = 'article/show.html';
	    $r['content'] = $this->keywords($r['content']);
		if ($r['pagecount'])
		{
			$pages = $r['pages'];
			for ($page = 1; $page <= $r['pagecount']; $page++)
			{
				$data = $pages[$page];
				$this->template->assign('page', $page);
				$this->template->assign('pages', $pages);
				$data['title'] = ($page == 1)?$r['title']:$r['title'].'（'.$page.'）';
				$data['content'] = $this->keywords($data['content']);
				$article = array_merge($r, $data);
				$filename = $data['path'];
				$this->template->assign($article);
				$this->template->assign('head', array('title'=>$article['title']));
				$data = $this->template->fetch($template);
				if ($page == 1) folder::create(dirname($filename));
				write_file($filename, $data);
			}
		}
		else
		{
			$r['hastitles'] = $r['page'] = $r['pages'] = 0;
			$this->template->assign($r);
			$this->template->assign('head', array('title'=>$r['title']));
			$data = $this->template->fetch($template);
			$r = $this->uri->content($contentid);
			$filename = $r['path'];
			folder::create(dirname($filename));
			write_file($filename, $data);
		}
		return true;
	}
	
	private function delete($contentid)
	{
		$r = $this->article->get($contentid, '*');
		if (!$r)
		{
			$this->error = $this->article->error();
			return false;
		}
		if ($r['pagecount'])
		{
			$start = 1;
			$end = $r['pagecount'];
		}
		else
		{
			$start = $end = null;
		}
		$this->unlink($contentid, $start, $end);
		return true;
	}
	
	private function unlink($contentid, $start = null, $end = null)
	{
		if (is_null($start))
		{
			$r = $this->uri->content($contentid);
			@unlink($r['path']);
		}
		else
		{
			for ($page = $start; $page <= $end; $page++)
			{
				$r = $this->uri->content($contentid, $page);
				@unlink($r['path']);
			}
		}
		return true;
	}

	/**
	 * 获取自定义字段信息
	 */
	private function get_content_file($contentid)
	{
		$field = unserialize(table('content_meta', $contentid, 'data'));
		return $this->_flatarr($field);
	}

	/**
	 * 多维数据转换为一维
	 * 索引相同则替换
	 * @param array
	 */
	private function _flatarr($arrays)
	{
		static $newarr = array();
		foreach($arrays as $key => $arr) {
			if(is_array($arr)) {
				$this->_flatarr($arr);
			} else {
				$newarr[$key] = $arr;
			}
		}
		return $newarr;
	}
}