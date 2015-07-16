<?php
class widgetEngine_map extends widgetEngine
{
	public function _render($widget)
	{
		$data = decodeData($widget['data']);
		return $this->_html($data);
	}
	public function _addView()
	{
		$this->view->display('widgets/map/form');
	}
	public function _editView($widget)
	{
		$data = decodeData($widget['data']);
		$this->view->assign('data', $data);
		$this->view->display('widgets/map/form');
	}
	
	public function _html($data)
	{
		$id = 'map_container_'.TIME;
		$height = intval($data['height']);
		if ($height < 10)
		{
		    $height = 300;
		}
		$width = intval($data['width']);
		if ($width < 10)
		{
            $width = 300;
		}
		$zoom = intval($data['zoom']);
		if (!$zoom)
		{
		    $zoom = 12;
		}
		$html = <<<EOT
<div style="width:{$width}px;height:{$height}px;" id="{$id}"></div>
<script type="text/javascript">
fet('net.BMap', function(){
	var map = new BMap.Map("{$id}");
	var point = new BMap.Point({$data['x']}, {$data['y']});
	map.enableScrollWheelZoom();
	map.centerAndZoom(point, {$zoom});
	var marker = new BMap.Marker(point);
	map.addOverlay(marker);
});
</script>
EOT;
	   return $html;
	}
}