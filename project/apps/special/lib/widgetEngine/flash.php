<?php
class widgetEngine_flash extends widgetEngine
{
	public function _render($widget)
	{
		$data = decodeData($widget['data']);
		$width = empty($data['width'])?'':'width="'.$data['width'].'"';
		$height = empty($data['height'])?'':'height="'.$data['height'].'"';
		
		$html .= '<object '.$width.' '.$height.' codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000">';
		$html .= '<param value="'.$data['src'].'" name="movie">';
		$html .= '<param value="transparent" name="wmode">';
    	$html .= '<embed '.$width.' '.$height.' wmode="transparent" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" quality="high" src="'.$data['src'].'">
</object>';
		return $html;
	}
	
	public function _addView()
	{
		$this->view->assign('upload_max_filesize', (intval(substr(ini_get('upload_max_filesize'),0,-1)))*1024*1024);
		$this->view->display('widgets/flash/edit');
	}
	
	public function _editView($widget)
	{
		$this->view->assign('upload_max_filesize', (intval(substr(ini_get('upload_max_filesize'),0,-1)))*1024*1024);
		$this->view->assign('data', decodeData($widget['data']));
		$this->view->display('widgets/flash/edit');
	}
}