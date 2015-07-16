<?php
class widgetEngine_share extends widgetEngine
{
	public function _render($widget)
	{
		$data = decodeData($widget['data']);
		$data['imgpath'] = IMG_URL.'apps/special/widget/share/images/'.$data['number'].'.gif';
		return $this->_genHtml(null, $data);
	}
	public function _addView()
	{
		$this->view->assign('path', IMG_URL.'apps/special/widget/share/images/');
		$this->view->display('widgets/share/form');
	}
	public function _editView($widget)
	{
		$data = decodeData($widget['data']);
		$this->view->assign('data', $data);
		$this->view->assign('path', IMG_URL.'apps/special/widget/share/images/');
		$this->view->display('widgets/share/form');
	}
}