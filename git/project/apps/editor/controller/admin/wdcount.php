<?php
/**
 * 字数统计
 *
 * @aca public 字数统计
 */
class controller_admin_wdcount extends editor_controller_abstract
{
	private $wdcount;
	
	function __construct(& $app)
	{
		parent::__construct($app);
	}
	
	function index()
	{
		echo $this->con_count($_POST['content']);
	}
	
	function con_count($string, $charset = 'utf-8')
	{
		$imgs =$words=0;
		$imgs = preg_match_all('/<img[^>]*\/?>/i',$string,$matches);
		$zh_count = words_count($string);
		return "字数：$zh_count<br />图片数：$imgs";
	}
}