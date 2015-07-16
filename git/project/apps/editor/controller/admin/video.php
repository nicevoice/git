<?php
/**
 * 视频
 *
 * @aca public 视频
 */
class controller_admin_video extends editor_controller_abstract
{
	private $video;
	
	function __construct(& $app)
	{
		parent::__construct($app);
		$this->video = loader::model('admin/video','video');
	}
	function video()
	{
		$this->view->display('video');
	}

	function flash()
	{
		$this->view->display('flash');
	}
	
	function insertvideo()
	{
		$this->view->display('videotable');
	}
	
	function page()
	{
		$catid = intval($_GET['catid']);
		$page = isset($_GET['page']) ? intval($_GET['page']) : 0;
		$pagesize = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : $this->pagesize;
		$where = $_GET;
		$fields = '*';
		$order = isset($_GET['orderby']) ? str_replace('|', ' ', $_GET['orderby']) : ($status >= 5 ? '`published` DESC' : 'c.`contentid` DESC');

		$data = $this->video->ls($where, $fields, $order, $page, $pagesize,true);
	    $result = array('total'=>$this->video->total, 'data'=>$data);
		echo $this->json->encode($result);
	}
	
	function getVideocode()
	{
		!isset($_GET['contentid']) && exit;
	    $r = $this->video->get($_GET['contentid'], '*', null,true,true);
	  	
	    $r['autostart'] = 'true';
		$videoext = array(
			'rm' => 'rmrmvb',
			'rmvb' => 'rmrmvb',
			'swf' => 'flash',
			'flv' => 'flv',
			'wmv' => 'wmv',
			'avi' => 'wmv'
		);
	    $fileext = fileext($r['video']);
		if(preg_match('/^(\[cc\])([^\[]+)(\[\/cc\])$/i', $r['video'], $matches)) 
		{
			$r['video'] = $matches[2];
			$r['player'] = 'cc';
		}
		elseif(preg_match('/^(\[ctvideo\])([^\[]+)(\[\/ctvideo\])$/i', $r['video'], $matches)) 
		{
			$ctv_setting = setting::get('video');
			$r['video'] = $matches[2];
			$r['player'] = 'ctvideo';
			$r['playerurl'] = $ctv_setting['player'];
		}
		elseif(array_key_exists($fileext, $videoext))
		{
			$r['player'] = $videoext[$fileext];	
		}
		else 
		{
			$r['player'] = 'flash';
		}
		$r['autostart'] = 'true';
		$this->template->assign($r);
		echo '<p style="text-align:center">'.$this->template->fetch('video/player/'.$r['player'].'.html', 'video').'</p>';
	}
	
	function upload()
	{
		$attachment = loader::model('admin/attachment', 'system');
		$upload_max_filesize = (intval(substr(ini_get('upload_max_filesize'),0,-1)))*1024*1024;
		$file = $attachment->upload('ctvideo',true, null,'rm|rmvb|wmv|swf|flv',$upload_max_filesize,array());
		
		echo UPLOAD_URL.$file;
	}
	
}