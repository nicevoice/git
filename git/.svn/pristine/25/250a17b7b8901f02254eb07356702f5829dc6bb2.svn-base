<?php

class controller_picture extends mobile_controller_abstract
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
        $catids = $this->mobile->filter_catids($this->setting['catids']);
        $catids = array_keys($catids);
		$q = array(
			'weight'=>$this->setting['weight'],
			'catid'=>implode(',',$catids)
		);
		$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
		$order = 'published';
		$orderby = 'DESC';
		$data = $this->mobile->ls_picture($q,$order,$orderby,$page);
		echo json_encode(array($data));
	}
	
	function show()
	{
		$contentid = isset($_GET['contentid']) ? intval($_GET['contentid']) : 0;
		if (!$contentid)
		{
			exit;
		}
		$_key = 'mobile_picture_show_'.$contentid;
		if(!$data = $this->cache->get($_key))
		{
			$model = loader::model('admin/picture', 'picture');
			$data  = $model->get($contentid, '*','show');
			if(!empty($data['pictures']))
			{
				foreach($data['pictures'] as &$row)
				{
					$new_row = array($this->mobile->get_thumb($row['image'],$this->setting['image_picture_show_width'],$this->setting['image_picture_show_height']),$row['note']);
					if($this->setting['open_base64'] && $new_row)
					{
						$new_row[0] = $this->mobile->pic_base64($new_row[0]);
					}
					$row = $new_row;
				}
			}
			$new_data = array(
				'contentid'=>$contentid,
				'catid'=>$data['catid'],
				'topicid'=>$data['topicid'],
				'title'=>$data['title'],
				'description'=>$data['description'],
				'source'=>$data['source_name'],
				'published'=>$data['published'],
				'comments'=>$data['comments'],
				'images'=>$data['pictures']
			);
			$data = $new_data;
			unset($new_data);
			// 放入缓存
			if($this->setting['cache']) $this->cache->set($_key,$data,$this->setting['cache']);
		}
		echo json_encode(array($data));
	}
}