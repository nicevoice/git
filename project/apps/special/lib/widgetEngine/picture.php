<?php
class widgetEngine_picture extends widgetEngine
{
	public function _render($widget)
	{
		$data = decodeData($widget['data']);
		$src = empty($data['thumb'])?'':'src="'.thumb($data['thumb']).'"';
		$width = empty($data['width'])?'':'width="'.$data['width'].'"';
		$height = empty($data['height'])?'':'height="'.$data['height'].'"';
		$alt = empty($data['description'])?'':'alt="'.$data['description'].'"';
		$title = empty($data['description'])?'':'title="'.$data['description'].'"';
		$html = "<img {$src} {$width} {$height} {$alt} {$title} align=\"center\"/>";
		if(!empty($data['url'])) {
			$html = '<a href="'.$data['url'].'" '.(empty($data['blank'])?'':'target="_blank"').'>'.$html.'</a>';
		}
		return $html;
	}
	public function _addView()
	{
		$this->view->display('widgets/picture/form');
	}
	public function _genData($post)
	{
		$post['data']['blank'] = isset($post['data']['blank']) ? 1 : 0;
		return encodeData($post['data']);
	}
	public function _editView($widget)
	{
		$this->view->assign('data', decodeData($widget['data']));
		$this->view->display('widgets/picture/form');
	}
}