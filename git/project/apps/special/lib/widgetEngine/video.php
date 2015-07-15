<?php
class widgetEngine_video extends widgetEngine
{
	public function _render($widget)
	{
		$data = decodeData($widget['data']);
		$videoext = array(
			'rm' => 'rmrmvb',
			'rmvb' => 'rmrmvb',
			'swf' => 'flash',
			'flv' => 'flv',
			'wmv' => 'wmv',
			'avi' => 'wmv'
		);
		$fileext = fileext($data['video']);
		if(preg_match('/^(\[cc\])([^\[]+)(\[\/cc\])$/i', $data['video'], $matches)) 
		{
			$data['video'] = $matches[2];
			$type = 'cc';
		}
        elseif(preg_match('/^(\[ctvideo\])([^\[]+)(\[\/ctvideo\])$/i', $data['video'], $matches))
		{
			$ctv_setting = setting::get('video');
			$data['video'] = $matches[2];
            $data['width'] = isset($data['width']) && $data['width'] ? $data['width'] : 560;
            $data['height'] = isset($data['height']) && $data['height'] ? $data['height'] : 420;
			$type = 'ctvideo';
			$data['playerurl'] = $ctv_setting['player'];
		}
		elseif(array_key_exists($fileext, $videoext))
		{
			$type = $videoext[$fileext];
		}
		else 
		{
			$type = 'flash';
		}
		$data['autostart'] = empty($data['autostart'])?'false':'true';
		$html = $this->html($type, $data);
		return $html;
	}
	public function _addView()
	{
		$this->view->display('widgets/video/form');
	}
	public function _genData($post)
	{
		$post['data']['autostart'] = isset($post['data']['autostart']) ? 1 : 0;
		return encodeData($post['data']);
	}
	public function _editView($widget)
	{
		$this->view->assign('data', decodeData($widget['data']));
		$this->view->display('widgets/video/form');
	}
	public function getvideo()
	{
		$contentid = intval($_GET['contentid']);
		$this->video = loader::model('admin/video','video');
		$video = $this->video->get_field('video',$contentid);
		
		$result = $video ? array('state' => true,'data' => $video):array('state' => false,'error' => '不存在的视频');
		echo $this->json->encode($result);
	}
	private function html($type,$data)
	{
		extract($data);

        $width = isset($width) && $width ? $width : '100%';
        $height = isset($height) && $height ? $height : '100%';

		$tpl['cc'] = <<<EOT
<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" 
width="{$width}" height="{$height}" id="bokeccplayer"> 
    <param name="movie" value="http://union.bokecc.com/{$video}" /> 
    <param name="allowFullScreen" value="true" />   
    <param name="allowScriptAccess" value="always" /> 
    <param name="wmode" value="opaque" /> 
    <embed src="http://union.bokecc.com/{$video}" quality="high" 
pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x -shockwave-flash" 
width="{$width}" height="{$height}" name="bokeccplayer" allowFullscreen="true" allowScriptAccess="always" 
wmode="opaque"></embed> 
  </object>
EOT;
		$tpl['flash'] = <<<EOT
<embed src="{$video}" allowFullScreen="true" quality="high" width="{$width}" height="{$height}" autostart="{$autostart}" align="middle" allowScriptAccess="always" type="application/x-shockwave-flash"></embed>
EOT;
		$IMG_URL = IMG_URL;
		$tpl['flv'] = <<<EOT
<p id="ctvideo"></p>
<script type="text/javascript" src="{$IMG_URL}apps/video/js/flvplayer.js"></script>
<script type="text/javascript">
var s1 = new SWFObject("{$IMG_URL}apps/video/js/flvplayer.swf","single","{$width}","{$height}","7");
s1.addParam("allowfullscreen","true");
s1.addVariable("file","{$video}");
s1.addVariable("image","preview.gif");
s1.addVariable("autostart","{$autostart}");
s1.addVariable("width","{$width}");
s1.addVariable("height","{$height}");
s1.write("ctvideo");
</script>
EOT;
		$tpl['other'] = <<<EOT
<object classid="CLSID:22d6f312-b0f6-11d0-94ab-0080c74c7e95" class="OBJECT" width="{$width}" height="{$height}">
<param name="ShowStatusBar" value="true">
<param name="transparentatstar" value="true">
<param name="DisplaySize" value="100%"> 
<param name="animationatstart" value="true">
<param name="volume" value="100">
<param name="showstatusbar" value="true">
<param name="showaudiocontrols" value="true">
<param name="showpositioncontrols" value="true">
<param name="Filename" value="{$video}">
<param name="autostart" value="{$autostart}">
<embed width="{$width}" height="{$height}" transparentatstart="true" animationatstart="false" DisplaySize="100%" autostart="{$autostart}" volume="100" type="application/x-mplayer2" showstatusbar="true" showaudiocontrols="true"  showpositioncontrols="true" src="{$video}">
</embed>
</object>
EOT;
		$tpl['rmrmvb'] = <<<EOT
<table border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td><object id="RVOCX" classid="CLSID:CFCDAA03-8BE4-11cf-B84B-0020AFBBCCFA" width="{$width}" height="{$height}" >
 <param name="SRC" value="{$video}">
 <param name="CONTROLS" value="ImageWindow">
 <param name="CONSOLE" value="cons">
 <param name="autostart" value="{$autostart}">
 <embed autostart="{$autostart}" src="{$video}" type="audio/x-pn-realaudio-plugin" width="{$width}" height="{$height}" controls="ImageWindow" console="cons"> </embed>
</object></td>
  </tr>
  <tr>
    <td><object id="RVOCX2" classid="CLSID:CFCDAA03-8BE4-11CF-B84B-0020AFBBCCFA" width="{$width}" height="30" >
   <param name="SRC" value="{$video}">
   <param name="CONTROLS" value="ControlPanel">
   <param name="CONSOLE" value="cons">
   <embed src="{$video}" type="audio/x-pn-realaudio-plugin" width="{$width}" height="30" controls="ControlPanel" console="cons" > </embed>
 </object></td>
  </tr>
</table>
EOT;
		$tpl['wmv'] = <<<EOT
<object classid="CLSID:22d6f312-b0f6-11d0-94ab-0080c74c7e95" class="OBJECT" width="{$width}" height="{$height}">
<param name="ShowStatusBar" value="true">
<param name="transparentatstar" value="true">
<param name="DisplaySize" value="100%"> 
<param name="animationatstart" value="true">
<param name="volume" value="100">
<param name="showstatusbar" value="true">
<param name="showaudiocontrols" value="true">
<param name="showpositioncontrols" value="true">
<param name="Filename" value="{$video}">
<param name="autostart" value="{$autostart}">
<embed width="{$width}" height="{$height}" transparentatstart="true" animationatstart="false" DisplaySize="100%" autostart="{$autostart}" volume="100" type="application/x-mplayer2" showstatusbar="true" showaudiocontrols="true"  showpositioncontrols="true" src="{$video}">
</embed>
</object>
EOT;

        $this->template->assign($data);
        $tpl['ctvideo'] = $this->template->fetch('video/player/ctvideo.html', 'video');

        if(!isset($tpl[$type])) return 'video tpl not found!';
        return $tpl[$type];
	}
}