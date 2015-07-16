<?php
/**
 * 问卷生成
 *
 * @aca whole 问卷生成
 */
final class controller_admin_html extends exam_controller_abstract
{
	private $exam;

	function __construct(& $app)
	{
		parent::__construct($app);
		if (!license('exam')) cmstop::licenseFailure();
		$this->exam = loader::model('admin/exam');
	}
	
	function show()
	{
		$contentid = $_REQUEST['contentid'];
		if ($this->exam->html_write($contentid))
		{
			$result = array('state'=>true);
		}
		else 
		{
			$result = array('state'=>false, 'error'=>$this->exam->error());
		}

		echo $this->json->encode($result);
	}
	
	function show_batch()
	{
		$where = $_REQUEST['where'];
		$where .= " AND `modelid`=9 ";
		
		$limit = $_REQUEST['limit'];
		$offset = isset($_REQUEST['offset']) ? $_REQUEST['offset'] : 0;
		$count = isset($_REQUEST['count']) && strlen($_REQUEST['count']) > 0 ? $_REQUEST['count'] : $this->exam->content->count($where);
		
		if ($offset < $count)
		{
			$data = $this->exam->content->select($where, 'contentid', '`contentid` DESC', $limit, $offset);
			foreach ($data as $r)
			{
				$this->exam->html_write($r['contentid']);
			}
		}
		$offset += count($data);
		$finished = $offset >= $count ? true : false;
		
		$result = array('state'=>true, 'count'=>$count, 'offset'=>$offset, 'finished'=>$finished);
		echo $this->json->encode($result);
	}
}