<?php
/**
 * 访谈生成
 *
 * @aca whole 访谈生成
 */
final class controller_admin_html extends interview_controller_abstract
{
	private $interview;

	function __construct(& $app)
	{
		parent::__construct($app);
		if (!license('interview')) cmstop::licenseFailure();
		$this->interview = loader::model('admin/interview');
	}
	
	function show()
	{
		$contentid = $_REQUEST['contentid'];
		if ($this->interview->html_write($contentid))
		{
			$result = array('state'=>true);
		}
		else 
		{
			$result = array('state'=>false, 'error'=>$this->interview->error());
		}
		echo $this->json->encode($result);
	}
	
	function show_batch()
	{
		$where = $_REQUEST['where'];
		$where .= " AND `modelid`=5 ";
		
		$limit = $_REQUEST['limit'];
		$offset = isset($_REQUEST['offset']) ? $_REQUEST['offset'] : 0;
		$count = isset($_REQUEST['count']) && strlen($_REQUEST['count']) > 0 ? $_REQUEST['count'] : $this->interview->content->count($where);
		
		if ($offset < $count)
		{
			$data = $this->interview->content->select($where, 'contentid', '`contentid` DESC', $limit, $offset);
			foreach ($data as $r)
			{
				$this->interview->html_write($r['contentid']);
			}
		}
		$offset += count($data);
		$finished = $offset >= $count ? true : false;
		
		$result = array('state'=>true, 'count'=>$count, 'offset'=>$offset, 'finished'=>$finished);
		echo $this->json->encode($result);
	}
	
	function guest()
	{
		$letters = array('a','b','c','d','e','f','g','h','i','g','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
    	$this->template->assign('letters', $letters);
    	$data = $this->template->fetch('interview/guest.html');
		$filename = ROOT_PATH.'public/www/talk/guests'.SHTML;
		folder::create(dirname($filename));
		write_file($filename, $data);

		$result = array('state'=>true);
		echo $this->json->encode($result);
	}
}