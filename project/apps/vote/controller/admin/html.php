<?php
/**
 * 投票生成
 *
 * @aca whole 投票生成
 */
class controller_admin_html extends vote_controller_abstract
{
	private $vote;

	function __construct(& $app)
	{
		parent::__construct($app);
		$this->vote = loader::model('admin/vote');
	}
	
	function show()
	{
		$contentid = $_REQUEST['contentid'];
		if ($this->vote->html_write($contentid))
		{
			$result = array('state'=>true);
		}
		else 
		{
			$result = array('state'=>false, 'error'=>$this->vote->error());
		}
		echo $this->json->encode($result);
	}
	
	function show_batch()
	{
		$where = $_REQUEST['where'];
		$where .= " AND `modelid`=8 ";
		
		$limit = $_REQUEST['limit'];
		$offset = isset($_REQUEST['offset']) ? $_REQUEST['offset'] : 0;
		$count = isset($_REQUEST['count']) && strlen($_REQUEST['count']) > 0 ? $_REQUEST['count'] : $this->vote->content->count($where);
		
		if ($offset < $count)
		{
			$data = $this->vote->content->select($where, 'contentid', '`contentid` DESC', $limit, $offset);
			foreach ($data as $r)
			{
				$this->vote->html_write($r['contentid']);
			}
		}
		$offset += count($data);
		$finished = $offset >= $count ? true : false;
		
		$result = array('state'=>true, 'count'=>$count, 'offset'=>$offset, 'finished'=>$finished);
		echo $this->json->encode($result);
	}
}