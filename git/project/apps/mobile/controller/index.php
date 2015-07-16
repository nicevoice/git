<?php

class controller_index extends mobile_controller_abstract
{
	private $mobile;
	
	function __construct(& $app)
	{
		parent::__construct($app);
		$this->mobile = loader::model('mobile');
		if (!$this->setting['open']) $this->showmessage($this->setting['webname'].'的mobile服务已关闭', 'index.php');
	}

	function index()
	{
		$global_data = $this->_global();
		$global_json = json_encode(array($global_data));
		$this->template->assign('global_json',$global_json);
		$this->template->display('mobile/index.html');
	}

	private function _global()
	{
		$_key = 'mobile_global';
		if($data = $this->cache->get($_key))
		{
			return $data;
		}
		$data = array();
		$data['logo'] = $this->setting['logo'];
		$data['open_base64'] = $this->setting['open_base64'];
		// 过滤掉未选择的分类
        $data['category'] = $this->mobile->filter_catids($this->setting['catids']);
		// 生成树型菜单
		$data['category'] = $this->_tree($data['category']);
		// 去除下标，否则JSON时会输出OBJECT
		$data['category'] = $this->_clear_key($data['category']);
		// 获取开启的模型
		$data['model'] = $this->setting['modelids'];
		// ++绑定模型名称
		foreach($data['model'] as $k=>$v)
		{
			$model_data = table('model',$v);
			$data['model'][$k] = array('modelid'=>$model_data['modelid'],'title'=>$model_data['name']);
		}
		// 输出导航菜单时，不输出文章，过滤掉
		unset($data['model'][0]);
		rsort($data['model']);
		// 插入两个默认按钮
		array_unshift($data['model'],array('modelid'=>100,'title'=>'推荐'));
		array_push($data['model'],array('modelid'=>101,'title'=>'评论'));
		// 获取评论配置，是否允许匿名
		$comment_setting = setting::get('comment');
		$data['anonymous'] = $comment_setting['islogin'];
		// 放入缓存
		if($this->setting['cache']) $this->cache->set($_key,$data,$this->setting['cache']);
		return $data;
	}
	
	function recommend()
	{
		// 获取banner头条
		$banner = $this->_get_banner();
		// 获取推荐list
		$recommend = $this->_get_recommend();
		$recommend_json = json_encode(array(array('banner'=>$banner,'list'=>$recommend)));
		echo $recommend_json;
	}

	/**
	 * 获取推荐头条，必须使用区块
	 */
	private function _get_banner()
	{
		$_key = 'mobile_banner';
		if($data = $this->cache->get($_key))
		{
			return $data;
		}
		$sectionid = $this->setting['index_banner_section'];
		$file = WWW_PATH .'/section/'.$sectionid.'.json';
		$json = file_get_contents($file);
		$data = json_decode($json,TRUE);
		$data = array(
			'contentid'=>$data[0][0]['contentid'],
			'modelid'=>0,
			'title'=>$data[0][0]['title'],
			'thumb'=>$this->mobile->get_thumb($data[0][0]['thumb'],$this->setting['image_banner_width'],$this->setting['image_banner_height']),
			'url'=>''	// 如果为视频，需要提供mp4的可播放地址
		);
		if($this->setting['open_base64'] && $data['thumb'])
		{
			$data['thumb'] = $this->mobile->pic_base64($data['thumb']);
		}
		if(empty($data['contentid']))
		{
			return FALSE;
		}
		else
		{
			$db = & factory::db();
			$result = $db->select("SELECT contentid,modelid FROM `#table_content` WHERE contentid=" .$data['contentid']);
			$result = $result[0];
			$data['modelid'] = $result['modelid'];
			if($data['modelid'] == '4')
			{
				$video_info = $this->mobile->get_video_info($data['contentid']);
				$data['url'] = $video_info['url'];
			}
		}
		// 放入缓存
		if($this->setting['cache']) $this->cache->set($_key,$data,$this->setting['cache']);
		return $data;
	}
	
	/**
	 * 获取推荐列表，支持区块和自动权重调用
	 */
	private function _get_recommend()
	{
		if($this->setting['index_recommend_type'])
		{
			$_key = 'mobile_recommend_section';
			if($data = $this->cache->get($_key))
			{
				return $data;
			}
			// 区块方式
			$sectionid = $this->setting['index_recommend_section'];
			$file = WWW_PATH .'/section/'.$sectionid.'.json';
			$json = file_get_contents($file);
			$data = json_decode($json, TRUE);
			$i = 0;
			foreach($data as $row)
			{
				if(empty($row[0]['contentid']))
				{
					continue;
				}
				$data_i[$i] = $row[0]['contentid'];
				$new_data[$i] = array(
					'contentid'=>$row[0]['contentid'],
					'modelid'=>0,
					'title'=>$row[0]['title'],
					'thumb'=>$this->mobile->get_thumb($row[0]['thumb'],$this->setting['image_list_width'],$this->setting['image_list_height']),
					'url'=>'',	// 如果为视频，需要提供mp4的可播放地址
					'description'=>$row[0]['description'],
					'published'=>date('Y-m-d H:i:s',$row[0]['time'])
				);
				if($this->setting['open_base64'] && $new_data[$i]['thumb'])
				{
					$new_data[$i]['thumb'] = $this->mobile->pic_base64($new_data[$i]['thumb']);
				}
				$i++;
			}
			if(!empty($data_i))
			{
				$db = & factory::db();
				$result = $db->select("SELECT contentid,modelid FROM `#table_content` WHERE contentid IN (" . implode(",",$data_i) .") order by find_in_set(contentid, '" . implode(",",$data_i) ."')");
				foreach($result as $k=>$row)
				{
					$new_data[$k]['modelid'] = $row['modelid'];
					if($row['modelid'] == '4')
					{
						$video_info = $this->mobile->get_video_info($row['contentid']);
						$new_data[$k]['url'] = $video_info['url'];
					}
				}
			}
			// 放入缓存
			if($this->setting['cache']) $this->cache->set($_key,$new_data,$this->setting['cache']);
			return $new_data;
		}
		else
		{
			$_key = 'mobile_recommend_autoweight';
			if($data = $this->cache->get($_key))
			{
				return $data;
			}
			// 自动权重
			$options = array();
			$options['weight'] = $this->setting['index_weight'] .",";
			$options['modelid'] = '1';	// 自动获取内容时，仅取文章模型的
            $catids = $this->mobile->filter_catids($this->setting['catids']);
            $catids = array_keys($catids);
			$options['catid'] = implode(',',$catids);
			$options['size'] = $this->setting['index_recommend_size'];
			$options['orderby'] = "`published` DESC";
			$options['where'] = "`thumb`<>'' and (`thumb` is NOT NULL)";
			$data = tag_content($options);
			$new_data = array();
			foreach($data as $row)
			{
				if($row['modelid'] == '4')
				{
					$video_info = $this->mobile->get_video_info($row['contentid']);
					$row['url'] = $video_info['url'];
				}
				$thumb = $this->mobile->get_thumb($row['thumb'],$this->setting['image_list_width'],$this->setting['image_list_height']);
				if($this->setting['open_base64'] && $thumb)
				{
					$thumb = $this->mobile->pic_base64($thumb);
				}
				$new_data[] = array(
					'contentid'=>$row['contentid'],
					'modelid'=>$row['modelid'],
					'title'=>$row['title'],
					'thumb'=>$thumb,
					'url'=>$row['url'],
					'description'=>description($row['contentid'],$row['modelid']),
					'published'=>date('Y-m-d H:i:s',$row['published'])
				);
			}
			// 放入缓存
			if($this->setting['cache']) $this->cache->set($_key,$new_data,$this->setting['cache']);
			return $new_data;
		}
	}
	
	/**
	 * 生成树型菜单
	 */
	private function _tree($data)
	{
		// 先获取到根级分类
		foreach($data as $catid=>$value)
		{
			if($value['parentid'])
			{
				continue;
			}
			$tree[$catid] = $value;
			// 获取子分类
			$tree[$catid]['children'] = $this->_tree_child($catid,$data);
			// 如果子节点为空，则设为NULL
			if(!$tree[$catid]['children'])
			{
				$tree[$catid]['childids'] = NULL;
			}
		}
		return $tree;
	}
	private function _tree_child($parentid,$data,$tree=array())
	{
		$childids = $data[$parentid]['childids'];
		if (!$parentid || !$childids)
		{
			return $tree;
		}
		$childid_arr = explode(',',$childids);
		foreach($childid_arr as $k)
		{
			if(isset($data[$k]) && ($data[$k]['parentid'] == $parentid))
			{
				$tree[$k] = $data[$k];
				$tree[$k]['children'] = $this->_tree_child($k,$data);
				// 如果子节点为空，则设为NULL
				if(!$tree[$k]['children'])
				{
					$tree[$k]['childids'] = NULL;
				}
			}
		}
		return $tree;
	}

	/**
	 * 递归，去除ARRAY的KEY，避免数据JSON时变成OBJECT
	 */
	private function _clear_key($arr)
	{
		if(!is_array($arr))
		{
			return $arr;
		}
		$keys = array_keys($arr);
		if(is_numeric(implode('',$keys)))
		{
			$value = array_values($arr);
			$arr = $value;
		}
		foreach($arr as $k=>$v)
		{
			if(is_array($v))
			{
				$arr[$k] = $this->_clear_key($v);
			}else{
				
			}
		}
		return $arr;
	}
}