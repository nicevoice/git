<?php
class widgetEngine_weather extends widgetEngine
{
	public function _render($widget)
	{
		$data = decodeData($widget['data']);
		return $data['code'];
	}
	public function _addView()
	{
		$this->view->display('widgets/weather/form');
	}
	public function _editView($widget)
	{
		$data = decodeData($widget['data']);
		$this->view->assign('data', $data);
		$this->view->display('widgets/weather/form');
	}
	public function city()
	{
		$content =  file_get_contents($_GET['url']);
		$content = explode(',',$content);
		$arr = array();
		foreach($content as $k => $v)
		{
			$v = explode('|',$v);
			$value = array(
				'id' => $v[0],
				'name' => $v[1]
			);
			$arr[] = $value;
		}
		echo $this->json->encode($arr);
	}
}