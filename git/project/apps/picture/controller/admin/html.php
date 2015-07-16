<?php
/**
 * 组图生成
 *
 * @aca whole 组图生成
 */
class controller_admin_html extends picture_controller_abstract
{
	private $picture;

	function __construct(& $app)
	{
		parent::__construct($app);
		$this->picture = loader::model('admin/picture');
	}
	
	function show()
	{
		$contentid = $_REQUEST['contentid'];
		if ($this->picture->html_write($contentid))
		{
			$result = array('state'=>true);
		}
		else 
		{
			$result = array('state'=>false, 'error'=>$this->picture->error());
		}
		echo $this->json->encode($result);
	}
	
	function show_batch()
	{
		$where = $_REQUEST['where'];
		$where .= " AND `modelid`=2 ";
		
		$limit = $_REQUEST['limit'];
		$offset = isset($_REQUEST['offset']) ? $_REQUEST['offset'] : 0;
		$count = isset($_REQUEST['count']) && strlen($_REQUEST['count']) > 0 ? $_REQUEST['count'] : $this->picture->content->count($where);
		
		if ($offset < $count)
		{
			$data = $this->picture->content->select($where, 'contentid', '`contentid` DESC', $limit, $offset);
			foreach ($data as $r)
			{
				$this->picture->html_write($r['contentid']);
			}
		}
		$offset += count($data);
		$finished = $offset >= $count ? true : false;
		
		$result = array('state'=>true, 'count'=>$count, 'offset'=>$offset, 'finished'=>$finished);
		echo $this->json->encode($result);
	}
}