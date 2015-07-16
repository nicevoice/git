<?php
/**
 * 文件上传
 *
 * @aca public 文件上传
 */
class controller_admin_filesup extends editor_controller_abstract
{	
	private $attachment;
	function __construct(& $app)
	{
		parent::__construct($app);
		$this->attachment = loader::model('admin/attachment', 'system');
	}

    /**
     * 上传
     *
     * @aca 上传
     */
	function upload()
	{
		$filename = $_FILES['multiUp']['name'];
		$upload_max_filesize = (intval(substr(ini_get('upload_max_filesize'),0,-1)))*1024*1024;

		$file	= $this->attachment->upload('multiUp',true,null, UPLOAD_FILE_EXTS,$upload_max_filesize);
		$aid	= $this->attachment->aid[0];
		$filext = strtolower(substr($filename,strrpos($filename,'.')+1));
		$isimage = in_array($filext,array('jpg','jpeg','gif','png'));
		$isvideo = in_array($filext,array('wmv','flv','swf','rm','rmvb'));
		$exts = array('chm','do','doc','docx','exe','hlp','htm','html','js','mid','midi','mp3','pdf','psd','rar','shtml','tif','txt','xls','xlsx','xml','zip','ppt','pptx');
				
		if ($isimage)
		{
			// 缩略图
			$setting = setting('editor');
			if ($setting['thumb_width'] || $setting['thumb_height'])
			{
				$image = & factory::image();
				if ($setting['thumb_width'] || $setting['thumb_height']) $image->set_thumb($setting['thumb_width'], $setting['thumb_height']);
				$sfile = UPLOAD_PATH.$file;
				$tmp_arr = explode('/', $file);
				array_push($tmp_arr, 'orig_'.array_pop($tmp_arr));
				$ofile = UPLOAD_PATH.implode('/', $tmp_arr);
				@copy($sfile, $ofile);
				if ($setting['thumb_width'] || $setting['thumb_height']) $image->thumb($sfile);
			}
			
			$code = '<img src="'.UPLOAD_URL.$file.'" alt="'.array_shift(explode('.', $filename)).'" />';
		}
		elseif ($isvideo)
		{
			$this->template->assign('src', UPLOAD_URL.$file);
			$this->template->assign('autostart', true);
			if ($filext == 'swf')
			{
				$code = $this->template->fetch('video/player/flash.html', 'video');
			}
			elseif ($filext == 'flv')
			{
				$code = $this->template->fetch('video/player/flv.html', 'video');
			}
			elseif ($filext == 'rm' || $filext == 'rmvb')
			{
				$code = $this->template->fetch('video/player/rmrmvb.html', 'video');
			}
			else
			{
				$code = $this->template->fetch('video/player/other.html', 'video');
			}
		}
		else
		{
			$code = '<a href="'.UPLOAD_URL.$file.'"><img class="icon" src="'.IMG_URL.'images/ext/'.(in_array($filext,$exts)?$filext:'other').'.gif" />'.$filename.'</a>';
		}

		$result = array(
			'state' => !!$file,
			'code'	=> $code,
			'msg'	=> !!$file ? '上传成功' : $this->attachment->error(),
			'aid'	=> $aid
		);
		exit($this->json->encode($result));
	}

    /**
     * 删除
     *
     * @aca 删除
     */
	function delete()
	{
		$aid	= intval($_GET['id']);
		if ($this->attachment->delete($aid))
		{
			$result = array('state'=>true, 'data'=>'删除成功');
		}
		else
		{
			$result	= array('state'=>false, 'error'=>$this->attachment->error());
		}
		exit($this->json->encode($result));
	}
}