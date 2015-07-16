<?php
/**
 * 文章生成
 *
 * @aca whole 页面生成
 */
class controller_admin_html extends article_controller_abstract 
{
	private $article;

	function __construct(& $app)
	{
		parent::__construct($app);
		$this->article = loader::model('admin/article');
	}
	
	function show()
	{
		$contentid = $_REQUEST['contentid'];
		if ($this->article->html_write($contentid))
		{
			$result = array('state'=>true);
		}
		else 
		{
			$result = array('state'=>false, 'error'=>$this->article->error());
		}
		echo $this->json->encode($result);
	}
	
	function show_batch()
	{
		$where = $_REQUEST['where'];
		$where .= " AND `modelid`=1 ";
		
		$limit = $_REQUEST['limit'];
		$offset = isset($_REQUEST['offset']) ? $_REQUEST['offset'] : 0;
		$count = isset($_REQUEST['count']) && strlen($_REQUEST['count']) > 0 ? $_REQUEST['count'] : $this->article->content->count($where);
		
		if ($offset < $count)
		{
			$data = $this->article->content->select($where, 'contentid', '`contentid` DESC', $limit, $offset);
			foreach ($data as $r)
			{
				$this->article->html_write($r['contentid']);
			}
		}
		$offset += count($data);
		$finished = $offset >= $count ? true : false;
		
		$result = array('state'=>true, 'count'=>$count, 'offset'=>$offset, 'finished'=>$finished);
		echo $this->json->encode($result);
	}
}