<?php
class widgetEngine_menu extends widgetEngine
{
	public function _render($widget)
	{
		$data = decodeData($widget['data']);
		return $this->_genHtml($data['template'], array('data'=>$data['list']));
	}
	public function _addView()
	{
		if ($contentid = intval($_REQUEST['contentid'])) {
			$list = $this->specialPage->select("contentid='$contentid'", 'name as title, url');
			$this->view->assign(array('list'=>$list));
		}
		$this->view->display('widgets/menu/form');
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
					'blank'=>$post['list']['blank'][$i]
				);
			}
		}
		if (empty($post['template']) && $widget)
		{
			$data = decodeData($widget['data']);
			$post['template'] = $data['template'];
		}
		return encodeData(array(
			'list'=>$list,
			'template'=>$post['template']
		));
	}
	public function _editView($widget)
	{
		$data = decodeData($widget['data']);
		$this->view->assign($data);
		$this->view->display('widgets/menu/form');
	}
}