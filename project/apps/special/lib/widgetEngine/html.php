<?php
class widgetEngine_html extends widgetEngine
{
	public function _render($widget)
	{
		$data = decodeData($widget['data']);
		return $data['code'];
	}
	public function _addView()
	{
		$this->view->display('widgets/html/form');
	}
	public function _editView($widget)
	{
		$data = decodeData($widget['data']);
		$this->view->assign('data', $data);
		$this->view->display('widgets/html/form');
	}
}