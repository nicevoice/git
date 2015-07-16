<?php
class widgetEngine_live extends widgetEngine
{
	public function _render($widget)
	{
		$data = decodeData($widget['data']);
		$width = empty($data['width'])?'':' width="'.$data['width'].'"';
		$height = empty($data['height'])?'':' height="'.$data['height'].'"';
		$src = isset($data['src']) && $data['src'] ? $data['src'] : '';
		
		$html = '<object' . $width . $height;
		$html .= ' codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0"';
		$html .= ' classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000">';
		$html .= '<param name="movie" value="' . IMG_URL . 'js/player/StrobeMediaPlayback.swf" />';
		$html .= '<param name="flashvars" value="src=' . $src;
		$html .= '&amp;controlBarMode=docked&amp;controlBarAutoHide=true&amp;autoPlay=true"></param>';
		$html .= '<param name="wmode" value="vid=opaque"></param>';
		$html .= '<param name="allowFullScreen" value="true"></param>';
		$html .= '<param name="allowscriptaccess" value="always"></param>';
		$html .= '<embed src="' . IMG_URL . 'js/player/StrobeMediaPlayback.swf" type="application/x-shockwave-flash"';
		$html .= ' pluginspage="http://www.macromedia.com/go/getflashplayer" quality="high" wmode="opaque"';
		$html .= ' allowscriptaccess="always" allowfullscreen="true"' . $width . $height;
		$html .= ' flashvars="src=' . $src. '&amp;controlBarMode=docked&amp;controlBarAutoHide=true&amp;autoPlay=true"></embed>';
		$html .= '</object>';
		return $html;
	}
	
	public function _addView()
	{
		$this->view->display('widgets/live/form');
	}
	
	public function _editView($widget)
	{
		$this->view->assign('data', decodeData($widget['data']));
		$this->view->display('widgets/live/form');
	}
}