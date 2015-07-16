<?php
/**
 * 问卷生成
 *
 * @aca whole 问卷生成
 */
final class controller_admin_html extends survey_controller_abstract
{
	private $survey;

	function __construct(& $app)
	{
		parent::__construct($app);
		if (!license('survey')) cmstop::licenseFailure();
		$this->survey = loader::model('admin/survey');
	}
	
	function show()
	{
		$contentid = $_REQUEST['contentid'];
		if ($this->survey->html_write($contentid))
		{
			$result = array('state'=>true);
		}
		else 
		{
			$result = array('state'=>false, 'error'=>$this->survey->error());
		}
		echo $this->json->encode($result);
	}
	
	function show_batch()
	{
		$where = $_REQUEST['where'];
		$where .= " AND `modelid`=9 ";
		
		$limit = $_REQUEST['limit'];
		$offset = isset($_REQUEST['offset']) ? $_REQUEST['offset'] : 0;
		$count = isset($_REQUEST['count']) && strlen($_REQUEST['count']) > 0 ? $_REQUEST['count'] : $this->survey->content->count($where);
		
		if ($offset < $count)
		{
			$data = $this->survey->content->select($where, 'contentid', '`contentid` DESC', $limit, $offset);
			foreach ($data as $r)
			{
				$this->survey->html_write($r['contentid']);
			}
		}
		$offset += count($data);
		$finished = $offset >= $count ? true : false;
		
		$result = array('state'=>true, 'count'=>$count, 'offset'=>$offset, 'finished'=>$finished);
		echo $this->json->encode($result);
	}
}