<?php
/**
 * 视频生成
 *
 * @aca whole 视频生成
 */
class controller_admin_html extends video_controller_abstract
{
	private $video;

	function __construct(& $app)
	{
		parent::__construct($app);
		$this->video = loader::model('admin/video');
	}
	
	function show()
	{
		$contentid = $_REQUEST['contentid'];
		if ($this->video->html_write($contentid))
		{
			$result = array('state'=>true);
		}
		else 
		{
			$result = array('state'=>false, 'error'=>$this->video->error());
		}
		echo $this->json->encode($result);
	}
	
	function show_batch()
	{
		$where = $_REQUEST['where'];
		$where .= " AND `modelid`=4 ";
		
		$limit = $_REQUEST['limit'];
		$offset = isset($_REQUEST['offset']) ? $_REQUEST['offset'] : 0;
		$count = isset($_REQUEST['count']) && strlen($_REQUEST['count']) > 0 ? $_REQUEST['count'] : $this->video->content->count($where);
		
		if ($offset < $count)
		{
			$data = $this->video->content->select($where, 'contentid', '`contentid` DESC', $limit, $offset);
			foreach ($data as $r)
			{
				$this->video->html_write($r['contentid']);
			}
		}
		$offset += count($data);
		$finished = $offset >= $count ? true : false;
		
		$result = array('state'=>true, 'count'=>$count, 'offset'=>$offset, 'finished'=>$finished);
		echo $this->json->encode($result);
	}
}