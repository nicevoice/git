<?php

class model_wap extends model 
{
	public $content, $setting;
	
	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'content';
		$this->_primary = 'contentid';
		$this->_fields = array('contentid', 'subtitle', 'spaceid', 'editor', 'description', 'content', 'saveremoteimage', 'createbbsthread');
		$this->_readonly = array('contentid');
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();
		$this->content = loader::model('content', 'system');
		$this->setting = setting('wap');
	}
	
	function get($contentid)
	{
		$content = $this->content->get($contentid);
		if ($content)
		{
			$model = table('model', $content['modelid'], 'alias');
			$model = loader::model('admin/'.$model, $model); 
			$r = $model->get($contentid);
			if ($r)
			{
				$this->_after_read($r, false);
				return $r;
			}
			else 
			{
				$this->error = $model->error();
				return false;
			}
		}
		return false;
	}

	function page($where, $fields, $order, $page, $size)
	{
		$data = $this->content->page($where, $fields, $order, $page, $size);
		$this->_after_read($data, true);
		return $data;
	}

	function pages($r)
	{
		switch ($r['modelid'])
		{
			case 1:
				$pages = $this->_article_page($r);
				break;
			case 2:
				$pages = $this->_picture_page($r);
				break;
			default:
				$pages = array();
				break;
		}
		return $pages;
	}

	function related($contentid)
	{
		$tags = $this->content->get($contentid, 'tags');
		$result = false;
		if ($tags)
		{
			$tags = explode(' ', $tags['tags']);
			$sql = 'SELECT 
					DISTINCT(`c`.`contentid`), `c`.`contentid`, `c`.`title` 
				FROM 
					`#table_content` AS `c`, `#table_content_tag` AS `ct`, `#table_tag` AS `t` 
				WHERE 
					`c`.`contentid` = `ct`.`contentid` AND `ct`.`tagid` = `t`.`tagid` AND `c`.`contentid` <> '.$contentid.' AND (';
			$or = '';
			foreach ($tags as $tag)
			{
				$sql .= " ".$or."`t`.`tag` = '".$tag."'";
				$or = ' OR ';
			}
			$sql .= ') ORDER BY `c`.`contentid` DESC';
			$result = $this->db->select($sql);
		}
		return $result;
	}

	private function _article_page($r)
	{
		$content = $r['content'];
		$pages = array();
		$charset = config('config', 'charset');
		$content_length = mb_strlen($content, $charset);
		if ($content_length > $this->setting['content_words'])
		{
			preg_match_all('/<[^>]*>/i', $content, $tags);
			if ($tags)
			{
				$particular = "\t";

				$tags = $tags[0];
				foreach ($tags as $tag)
				{
					$content = str_replace($tag, $particular, $content);
				}

				$content_length = mb_strlen($content, $charset);
				$per_words_count = $this->setting['content_words'] ? $this->setting['content_words'] : 500;
				$pagecount = ceil($content_length/$per_words_count);

				$p = 0;
				$t = 0;
				for ($i = 0; $i < $content_length; $i = $i+$per_words_count)
				{
					$pcontent = mb_substr($content, $p*$per_words_count, $per_words_count, $charset);
					$result = '';
					if (strpos($pcontent, $particular) === false)
					{
						$result = $pcontent;
					}
					else 
					{
						for ($j = 0; $j < mb_strlen($pcontent, $charset); $j++)
						{
							$d = mb_substr($pcontent, $j, 1, $charset);
							if ($d == $particular)
							{
								$result .= $tags[$t];
								$t++;
							}
							else 
							{
								$result .= $d;
							}
						}
					}

					$prevpage = $p == 0 ? 1 : $p;
					$page = $p + 1;
					$nextpage = $page == $pagecount ? $pagecount : $page + 1;
					$date = getdate($r['created']);
					$path = CACHE_PATH.'wap'.DS.$date['year'].DS.$date['mon'].$date['mday'].DS.$r['contentid'];
					if ($p != 0) $path .= '_'.$page;
					$path .= '.xml';
					$pages[$p] = array('content'=>$result, 'path'=>$path, 'page'=>$page, 'prevpage'=>$prevpage, 'nextpage'=>$nextpage);
					$p++;
				}
			}
		}
		else 
		{
			$date = getdate($r['created']);
			$path = CACHE_PATH.'wap'.DS.$date['year'].DS.$date['mon'].$date['mday'].DS.$r['contentid'].'.xml';
			$pages[0] = array('content'=>$content, 'path'=>$path, 'page'=>0, 'prevpage'=>0, 'nextpage'=>0);
		}
		return $pages;
	}

	private function _picture_page($r)
	{
		$pages = array();
		if ($r['pictures'])
		{
			$pagecount = count($r['pictures']);
			foreach ($r['pictures'] as $p=>$picture)
			{
				$prevpage = $p == 0 ? 1 : $p;
				$page = $p + 1;
				$nextpage = $page == $pagecount ? $pagecount : $page + 1;

				$date = getdate($r['created']);
				$path = CACHE_PATH.'wap'.DS.$date['year'].DS.$date['mon'].$date['mday'].DS.$r['contentid'];
				if ($p != 0) $path .= '_'.$page;
				$path .= '.xml';

				$img_src = thumb($picture['image'], $this->setting['image_width'], $this->setting['image_height']);
				$url = parse_url($img_src);
				if ($url)
				{
					$img_save_path = UPLOAD_PATH.$url['path'];
					$img_info = getimagesize($img_save_path);
				}

				$pages[$p] = array(
							'path'=>$path, 
							'src'=>$img_src, 
							'alt'=>$picture['alt'], 
							'big_image'=>$picture['image'], 
							'width'=>$img_info[0],
							'height'=>$img_info[1], 
							'size'=>round(filesize($img_save_path)/1024, 2), 
							'page'=>$page, 
							'prevpage'=>$prevpage, 
							'nextpage'=>$nextpage
						);
			}
		}
		return $pages;
	}

	function _thumb($matches)
	{
		$view_source_image = '';
		$img_url = $matches[1];
		if (substr($img_url, 0, strlen(UPLOAD_URL)) == UPLOAD_URL)
		{
			$view_source_image = '<br /><a href="'.WAP_URL.'?action=image&amp;path='.str_replace(UPLOAD_URL, '', $img_url).'">查看原图</a>';
			$img_url = thumb($img_url, $this->setting['image_width'], $this->setting['image_height']);
		}
		return '<p align="center"><img src="'.$img_url.'" />'.$view_source_image.'</p>';
	}
	
	function _after_read(& $data, $multiple)
	{
		if (!$data) return ;
		if ($multiple)
		{
			foreach ($data as $k=>$r)
			{
				$this->_filter_output($r);
				$data[$k] = $r;
			}
		}
		else 
		{
			$this->_filter_output($data);
		}
	}
	
	function _filter_output(& $r)
	{
		if (!$r) return false;
		$r['author'] = $r['spaceid'] > 0 ? table('space', $r['spaceid'], 'author') : '';
		if ($r['spaceid'])
		{
			$author = table('space', $r['spaceid']);
			$r['author_url'] = SPACE_URL.$author['alias'];
			$r['author_name'] = $author['name'];
		}
		$r['url'] = WAP_URL.'show.php?contentid='.$r['contentid'];
		if ($r['content'])
		{
			$r['content'] = preg_replace('/<br\s*\/?>/i', '<br />', $r['content']);
			$r['content'] = preg_replace('/<\/p>/i', '<br />', $r['content']);
			$r['content'] = strip_tags($r['content'], '<img><br>');
			$r['content'] = preg_replace_callback('/<img[^>]+src\s*\=\s*[\'"]?(([^>]*)(jpg|gif|png|bmp|jpeg))[\'"]?[^>]*>/i', array($this, '_thumb'), $r['content']);
		}
	}
}