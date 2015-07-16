<?php
class widgetEngine_title extends widgetEngine
{
	public function _render($widget)
	{
		$data = decodeData($widget['data']);
		$rows = intval($data['rows']);
		$length = intval($data['length']);
		if ($data['method'] == '1')
		{
			$list = $data['list'];
		}
		elseif ($data['method'] == '2')
		{
			$options = $data['options'];
			$options['fields'] = 'title, url, published as `time`';
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
				$l = array($l);
			}
		}
		else
		{
			$list = array();
			$placeid = $widget['widgetid'];
			$list = loader::model('admin/place_data', 'special')
				->select(
					"placeid=$placeid AND status=1",
					'title, url, time',
					'sort asc, time desc', $rows
				);
			foreach ($list as &$l)
			{
				$l = array($l);
			}
		}
		return $this->_genHtml($data['template'], array(
			'data'=>$list,
			'rows'=>$rows,
			'length'=>$length
		));
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
		$this->view->display('widgets/title/add');
	}
	public function _genData($post, $widget = null)
	{
		$list = array();
		if (is_array($post['list']))
		{
			$splits = array_values(array_filter(array_map('intval', (array) $post['list']['count'])));
			$titles = (array) $post['list']['title'];
			$urls = (array) $post['list']['url'];
			$total = count($titles);
			$r = 0;
			$l = $splits ? $splits[$r] : $total;
			$i = 0;
			if ($total) {
				$list[$r] = array();
			}
			while ($i<$total)
			{
				if ($i < $l) {
					$list[$r][] = array(
						'title'=>$titles[$i],
						'url'=>$urls[$i]
					);
					$i++;
				} else {
					$list[++$r] = array();
					$l = isset($splits[$r]) ? ($l + $splits[$r]) : $total;
				}
			}
		}if (empty($post['template']) && $widget)
		{
			$data = decodeData($widget['data']);
			$post['template'] = $data['template'];
		}
		return encodeData(array(
			'method'=>$post['method'],
			'list'=>$list,
			'options'=>$post['options'],
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
		$this->view->display('widgets/title/edit');
	}
}