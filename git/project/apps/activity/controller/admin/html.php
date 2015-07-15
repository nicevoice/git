<?php
/**
 * 活动生成
 *
 * @aca whole 活动生成
 */
class controller_admin_html extends activity_controller_abstract
{
	private $activity;

	function __construct(& $app)
	{
		parent::__construct($app);

		$this->activity = loader::model('admin/activity');
		
		if (isset($_REQUEST['catid']) && $_REQUEST['catid'] > 0)
		{
			$catid = intval($_REQUEST['catid']);
			if (!priv::category($catid))
			{
				$this->showmessage("您没有<span style='color:red'>".table('category', $catid, 'name')."($catid)</span>栏目权限！");
			}
		}
	}
	
	function show()
	{
		$contentid = $_REQUEST['contentid'];
		if ($this->activity->html_write($contentid))
		{
			$result = array('state'=>true);
		}
		else 
		{
			$result = array('state'=>false, 'error'=>$this->activity->error());
		}
		echo $this->json->encode($result);
	}
	
	function show_batch()
	{
		$where = $_REQUEST['where'];
		$where .= " AND `modelid`=7 ";
		
		$limit = $_REQUEST['limit'];
		$offset = isset($_REQUEST['offset']) ? $_REQUEST['offset'] : 0;
		$count = isset($_REQUEST['count']) && strlen($_REQUEST['count']) > 0 ? $_REQUEST['count'] : $this->activity->content->count($where);
		
		if ($offset < $count)
		{
			$data = $this->activity->content->select($where, 'contentid', '`contentid` DESC', $limit, $offset);
			foreach ($data as $r)
			{
				$this->activity->html_write($r['contentid']);
			}
		}
		$offset += count($data);
		$finished = $offset >= $count ? true : false;
		
		$result = array('state'=>true, 'count'=>$count, 'offset'=>$offset, 'finished'=>$finished);
		echo $this->json->encode($result);
	}
}