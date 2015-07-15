<?php
class widgetEngine_slider extends widgetEngine
{
	public function _render($widget)
	{
		$data = decodeData($widget['data']);
		$rows = intval($data['rows']);
		$length = intval($data['length']);
		$height = intval($data['height']);
		$width = intval($data['width']);
		if ($height < 5) {
			$height = 250;
		}
		if ($width < 5) {
			$width = 300;
		}
		if ($data['method'] == '1')
		{
			$list = $data['list'];
		}
		elseif ($data['method'] == '2')
		{
			$options = $data['options'];
			$options['fields'] = 'title, url, thumb';
			if ($options['weight']['range'])
			{
				$options['weight'] = $options['weight'][0].','.$options['weight'][1];
			}
			else
			{
				$options['weight'] = $options['weight'][0];
			}
			$orderby = array();
			foreach ($options['orderby'][0] as $i=>$f)
			{
				$s = $options['orderby'][1][$i];
				$orderby = "$f $s";
			}
			$options['orderby'] = implode(',', $orderby);
			$options['size'] = $rows;
			$list = tag_content($options);
			foreach ($list as &$l)
			{
				$l['thumb'] = $l['thumb'] ? thumb($l['thumb']) : '';
			}
		}
		else
		{
			$list = array();
			$placeid = $placeid = $widget['widgetid'];
			$list = loader::model('admin/place_data', 'special')
				->select(
					"placeid=$placeid AND status=1",
					'title, url, thumb',
					'sort asc, time desc', $rows
				);
		}
		return $this->_genHtml($data['template'], array(
			'data'=>$list,
			'width'=>$width,
			'height'=>$height,
			'rows'=>$rows,
			'length'=>$length
		));
	}
	public function _genData($post, $widget = null)
	{
		$list = array();
		if (is_array($post['list']))
		{
			foreach ((array) $post['list']['title'] as $i=>$v)
			{
				$list[] = array(
					'title'=>$v,
					'url'=>$post['list']['url'][$i],
					'thumb'=>$post['list']['thumb'][$i]
				);
			}
		}
		if (empty($post['template']) && $widget)
		{
			$data = decodeData($widget['data']);
			$post['template'] = $data['template'];
		}
		return encodeData(array(
			'method'=>$post['method'],
			'list'=>$list,
			'options'=>$post['options'],
			'width'=>$post['width'],
			'height'=>$post['height'],
			'rows'=>$post['rows'],
			'length'=>$post['length'],
			'template'=>$post['template']
		));
	}
	
	public function _afterPost($widgetid, $post)
	{
        $widgetid = intval($widgetid);
        $place = loader::model('admin/place', 'special');

		if ($post['place'])
		{
			if ($place->get($widgetid))
			{
				$place->update($post['place'], $widgetid);
			}
			else
			{
				$post['place']['placeid'] = $widgetid;
				$post['place']['pageid'] = $_REQUEST['pageid'];
				$place->insert($post['place']);
			}
		}
        else
        {
            $place->delete($widgetid, 1);
        }
	}
	public function _afterCopy($widgetid, $widget)
	{
		$data = decodeData($widget['data']);
		if ($data['method'] == '0')
		{
			$p = loader::model('admin/place', 'special');
			$pd = loader::model('admin/place_data', 'special');
			$place = $p->get($widget['widgetid']);
			$place['placeid'] = $widgetid;
			$place['pageid'] = $_REQUEST['pageid'];
			try {
				$p->insert($place);
			} catch (Exception $e) {}
			foreach ($pd->select("placeid={$widget[widgetid]}") as $d)
			{
				$d['placeid'] = $widgetid;
				try {
					$pd->insert($d);
				} catch (Exception $e) {}
			}
		}
	}
	public function _addView()
	{
		$sortset = array(
        	'published'=>'发布时间',
        	'contentid'=>'ID',
        	'pv'=>'浏览量',
        	'comments'=>'评论数'
        );
        $this->view->assign('sortset', $sortset);
		$this->view->display('widgets/slider/add');
	}
	public function _editView($widget)
	{
		$data = decodeData($widget['data']);
		if ($data['method'] == '0')
		{
			$data['place'] = loader::model('admin/place', 'special')->get($widget['widgetid']);
		}
		$this->view->assign($data);
		$sortset = array(
        	'published'=>'发布时间',
        	'contentid'=>'ID',
        	'pv'=>'浏览量',
        	'comments'=>'评论数'
        );
        $this->view->assign('sortset', $sortset);
		$this->view->display('widgets/slider/edit');
	}
}