<?php

class model_mobile extends model
{
	public $content, $setting, $cache;
	
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
		$this->setting = setting('mobile');
        $this->cache = & factory::cache();
	}
	
	function ls($q,$order='REL',$orderby='DESC',$page=1)
	{
		$search = loader::model('search', 'search');
		if(!is_array($q))
		{
			$q = array();
		}
		$pagesize = $this->setting['list_pagesize'];
		$result   = $search->page($q,'article','EXT',$order,$orderby,$page,$pagesize);
		$total    = $search->getTotal();

		$new_data = array();
		if(!empty($result))
		{
			foreach($result as $row)
			{
				$thumb = $this->get_thumb($row['thumb'],$this->setting['image_list_width'],$this->setting['image_list_height']);
				if($this->setting['open_base64'] && $thumb)
				{
					$thumb = $this->pic_base64($thumb);
				}
				$new_data[] = array(
					'contentid'=>$row['contentid'],
					'modelid'=>$row['modelid'],
					'title'=>$row['title'],
					'thumb'=>$thumb,
					'description'=>description($row['contentid'],$row['modelid']),
					'published'=>date('Y-m-d H:i:s',$row['published'])
				);
			}
		}
		return array(
			'more'=>max(ceil($total/$pagesize) - $page,0),
			'list'=>$new_data,
		);
	}
	
	function ls_comment($q,$order='REL',$orderby='DESC',$page=1)
	{
		$search = loader::model('search', 'search');
		if(!is_array($q))
		{
			$q = array();
		}
		$pagesize = $this->setting['list_pagesize'];
		$result   = $search->page($q,'article','EXT',$order,$orderby,$page,$pagesize);
		$total    = $search->getTotal();
		$new_data = array();
		if(!empty($result))
		{
			foreach($result as $row)
			{
				$new_data[] = array(
					'contentid'=>$row['contentid'],
					'topicid'=>$row['topicid'],
					'title'=>$row['title'],
					'comments'=>$row['comments']
				);
			}
		}
		return array(
			'more'=>max(ceil($total/$pagesize) - $page,0),
			'list'=>$new_data,
		);
	}
	
	function ls_video($q=array(),$order='REL',$orderby='DESC',$page=1)
	{
		$search = loader::model('search', 'search');
		if(!is_array($q))
		{
			$q = array();
		}
		$pagesize = $this->setting['list_pagesize'];
		$result   = $search->page($q,'mobile','EXT',$order,$orderby,$page,$pagesize);
		$total    = $search->getTotal();
		$new_data = array();
		if(!empty($result))
		{
			foreach($result as $row)
			{
				$video_info = $this->get_video_info($row['contentid']);
				$row['url'] = $video_info['url'];
				$row['playtime'] = $video_info['playtime'];
				$thumb = $this->get_thumb($row['thumb'],$this->setting['image_list_width'],$this->setting['image_list_height']);
				if($this->setting['open_base64'] && $thumb)
				{
					$thumb = $this->pic_base64($thumb);
				}
				$new_data[] = array(
					'contentid'=>$row['contentid'],
					'title'=>$row['title'],
					'thumb'=>$thumb,
					'url'=>$row['url'],
					'playtime'=>$row['playtime'],
					'pv'=>$row['pv']
				);
			}
		}
		return array(
			'more'=>max(ceil($total/$pagesize) - $page,0),
			'list'=>$new_data,
		);
	}
	
	function ls_picture($q=array(),$order='REL',$orderby='DESC',$page=1)
	{
		$search = loader::model('search', 'search');
		if(!is_array($q))
		{
			$q = array();
		}
		$pagesize = $this->setting['list_pagesize'];
		$result   = $search->page($q,'picture','EXT',$order,$orderby,$page,$pagesize);
		$total    = $search->getTotal();
		$new_data = array();
		if(!empty($result))
		{
			foreach($result as $row)
			{
				$thumb = $this->get_thumb($row['thumb'],$this->setting['image_picture_list_width'],$this->setting['image_picture_list_height']);
				if($this->setting['open_base64'] && $thumb)
				{
					$thumb = $this->pic_base64($thumb);
				}
				$new_data[] = array(
					'contentid'=>$row['contentid'],
					'title'=>$row['title'],
					'thumb'=>$thumb
				);
			}
		}
		return array(
			'more'=>max(ceil($total/$pagesize) - $page,0),
			'list'=>$new_data,
		);
	}
	
	/** 
	 * 获取一个视频的MP4可播放地址
	 */
	public function get_video_info($contentid)
	{
		if(!$contentid)
		{
			return FALSE;
		}
		$db = &factory::db();
		$result = $db->select("SELECT video,playtime FROM `#table_video` WHERE contentid=?", array($contentid));
		if(empty($result[0]['video']))
		{
			return FALSE;
		}
		else
		{
			if(strtolower(substr($result[0]['video'],-4)) == '.mp4')
			{
				return $result[0]['video'];
			}
			elseif(strtolower(substr($result[0]['video'],-10)) == '[/ctvideo]')
			{
				$vms = loader::model('admin/vms', 'video');
				$_GET['id'] = str_replace(array('[ctvideo]','[/ctvideo]'),'',$result[0]['video']);
				$info = $vms->info_by_file();
				if(empty($info))
				{
					return FALSE;
				}
				else
				{
					$info = json_decode($info,TRUE);
					if(!$info['state'])
					{
						return FALSE;
					}
					return array(
						'url'=>$info['data']['file'],
						'playtime'=>$result[0]['playtime']
					);
				}
			}
			else
			{
				return FALSE;
			}
		}
	}
	
	/**
	 * 获取指定尺寸的缩略图
	 */
	public function get_thumb($img,$width,$height)
	{
		if(!$img || (!$width && !$height))
		{
			return '';
		}
		// 先生成小图的文件路径，如果文件路径已经存在，直接返回，不存在再生成
		$remote_img = $local_img = '';
        if(strlen($img) > strlen(UPLOAD_URL) && substr($img,0,strlen(UPLOAD_URL)) == UPLOAD_URL)
        {
            $img = substr($img,strlen(UPLOAD_URL));
        }
        // 如果是远程图片，先计算一个本地图片缩略图路径
		if(strpos($img, '://'))
		{
			$remote_img  = $img;
			$remote_info = parse_url($img);
			$fileinfo = pathinfo($remote_info['path']);
            $md5file = md5($img);
			$newimg = 'remote2local/' .substr($md5file,0,1) .'/'.substr($md5file,0,2).'/thumb_'.$width.'_'.$height.'_'.$md5file.'.'.$fileinfo['extension'];
			$local_img = 'tmp/' .$md5file.'.'.$fileinfo['extension'];
		}
		else
		{
			$newimg = dirname($img). '/thumb_'.$width.'_'.$height.'_'.basename($img);
		}
        $newimg = ltrim($newimg, '/');
		if(file_exists(UPLOAD_PATH.$newimg))
		{
            // 如果是远程图片不判断更新，直接返回
			if($remote_img)
			{
				return $this->pic_path($newimg);
			}
            // 如果是本地图片判断一下图片是否有更新
			if(filemtime(UPLOAD_PATH.$newimg) >= @filemtime(UPLOAD_PATH.$img))
			{
				return $this->pic_path($newimg);
			}
		}
		// 开始生成
		if(!extension_loaded('gd'))
		{
			return $img;
		}
        // 如果是远程图片先本地化
		if($remote_img)
		{
			if(!file_exists(dirname(UPLOAD_PATH.$local_img)))
			{
				folder::create(dirname(UPLOAD_PATH.$local_img));
			}
			$ctx = stream_context_create(array(
				'http' => array(
				       'timeout' => 10 //设置一个超时时间，单位为秒
					)
				)
			);
			if(!file_put_contents(UPLOAD_PATH.$local_img, file_get_contents($remote_img, 0, $ctx)))
			{
				return FALSE;
			}
			$img = $local_img;
		}

        if(!file_exists(UPLOAD_PATH.$img))
        {
            return '';
        }
		if(!file_exists(dirname(UPLOAD_PATH.$newimg)))
		{
			folder::create(dirname(UPLOAD_PATH.$newimg));
		}
		$image = factory::image();
		$image->set_thumb($width, $height, 80);
		if(!$image->thumb_cut(UPLOAD_PATH.$img, UPLOAD_PATH.$newimg,0))
		{
			if($remote_img)
			{
				$newimg = $remote_img;
			}
			else
			{
				$newimg = $img;
			}
		}
        // 删除临时文件
		if($local_img)
		{
			unlink(UPLOAD_PATH.$local_img);
		}

		return $this->pic_path($newimg);
	}

	/**
	 * 对内容中的图片做缩放处理
	 */
	function content_thumb($matches)
	{
		$img_url = $matches[1];
		$img_small_url = $this->get_thumb($img_url, $this->setting['image_content_small_width'], $this->setting['image_content_small_height']);
		// 如果小图生成失败，返回原图
        if(!$img_small_url)
        {
            return '<img src="'.$img_url.'" />';
        }
        $img_big_url = $this->get_thumb($img_url, $this->setting['image_content_big_width'], $this->setting['image_content_big_height']);
        // 如果大图生成失败，调用原图信息
        if(!$img_big_url)
        {
            $img_big_url = $img_url;
        }
        $imginfo = $this->_get_pic_info($img_url);
        // 判断BASE64输出
		if($this->setting['open_base64'])
        {
            $img_small_url = $this->pic_base64($img_small_url);
            $img_big_url = $this->pic_base64($img_big_url);
        }
        $return = '<img src="' .$img_small_url .'" data-origin="' .$img_big_url .'" data-width="'.$imginfo[0].'" data-height="'.$imginfo[1].'" />';

        return $return;
	}
	
	/**
	 * 获取图片的信息，数组，包括两个值 0 宽度 1 高度
	 */
	private function _get_pic_info($pic)
	{
        if(strlen($pic) > strlen(UPLOAD_URL) && substr($pic,0,strlen(UPLOAD_URL)) == UPLOAD_URL)
        {
            $pic = substr($pic,strlen(UPLOAD_URL));
            $pic = UPLOAD_PATH .$pic;
        }
		$picinfo = getimagesize($pic);
        if(!$picinfo)
        {
            $picinfo = array(0,0);
        }
		return $picinfo;
	}
		
	/**
	 * 修正图片路径
	 *
	 */
	public function pic_path($pic)
	{
		if(strpos($pic,'://') === FALSE)
		{
			$pic = UPLOAD_URL .$pic;
		}
		return $pic;
	}
	
	/**
	 * 对图片进行BASE64输出
	 * @param string $pic 图片路径
	 */
	public function pic_base64($pic)
	{
		if(!$pic)
		{
			return '';
		}
        $key = md5('pic_base64_'.$pic);
        if($data = $this->cache->get($key))
        {
            return $data;
        }
		$ctx = stream_context_create(array(
			'http' => array(
			       'timeout' => 30 //设置一个超时时间，单位为秒
				)
			)
		);
		if($data = file_get_contents($pic, 0, $ctx))
		{
			$data = 'data:image/png;base64,'.base64_encode($data);
            $this->cache->set($key,$data,0);
		}
		return $data;
	}

    /**
     * 过滤分类ID，去除未选择的分类
     */
    public function get_catids($catid)
    {
        if (!intval($catid))
        {
            return FALSE;
        }
        $catids = $this->filter_catids();
        if(!array_key_exists($catid,$catids))
        {
            return FALSE;
        }
        $childs = $catids[$catid]['childids'];
        $new_catids = array();
        $new_catids[] = $catid;
        if (empty($childs))
        {
            return $new_catids;
        }
        if (strpos($childs,','))
        {
            $childs = explode(',', $childs);
        }
        else
        {
            $childs = array($childs);
        }
        foreach($childs as $key)
        {
            if(in_array($key, $childs))
            {
                $new_catids[] = $key;
            }
        }
        return $new_catids;
    }

    /**
     * 过滤栏目选择
     * 生成
     * @param $data
     */
    public function filter_catids($data=null)
    {
        if($data == null)
        {
            $data = $this->setting['catids'];
        }
        if(!is_array($data))
        {
            return FALSE;
        }
        $catids = array();
        // 取出所有分类
        $category = $this->content->category;
        $catkeys = array_keys($category);
        // 设定允许分类
        foreach($data as $catid)
        {
            if(in_array($catid,$catkeys))
            {
                if($category[$catid]['parentids'])
                {
                    foreach(explode(',',$category[$catid]['parentids']) as $key)
                    {
                        $catids[$key] = $this->_cat_field($category[$key]);
                    }
                }

                $catids[$catid] = $this->_cat_field($category[$catid]);

                if($catids[$catid]['childids'])
                {
                    foreach(explode(',',$catids[$catid]['childids']) as $key)
                    {
                        $catids[$key] = $this->_cat_field($category[$key]);
                    }
                }
            }
        }
        // 重新排序一级栏目
        $new_catids = array();
        foreach($catkeys as $key)
        {
            if(array_key_exists($key,$catids))
            {
                $new_catids[$key] = $catids[$key];
            }
        }
        return $new_catids;
    }

    private function _cat_field($data)
    {
        return array(
            'catid'=>$data['catid'],
            'name'=>$data['name'],
            'parentid'=>$data['parentid'],
            'parentids'=>$data['parentids'],
            'childids'=>$data['childids']
        );
    }
}