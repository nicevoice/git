<?php
class widgetEngine_weibo extends widgetEngine
{
	public function _render($widget)
	{
		$data = decodeData($widget['data']);
		return $data['code'];
	}
	public function _addView()
	{
		$this->view->display('widgets/weibo/form');
	}
	public function _editView($widget)
	{
		$data = decodeData($widget['data']);
		$this->view->assign('data', $data);
		$this->view->display('widgets/weibo/form');
	}
	public function gettags()
	{
		$tags = urlencode(str_charset('UTF-8','GBK',$_GET['tags']));
		echo $this->json->encode(array('state' => true,'tags' => $tags));
	}
	public function _genData($post)
	{
		$post['data']['sina'] = $post['sina'];
		$post['data']['sohu'] = $post['sohu'];
		return encodeData($post['data']);
	}
}