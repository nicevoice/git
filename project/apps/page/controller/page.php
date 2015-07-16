<?php

class controller_page extends page_controller_abstract
{
	function __construct(& $app)
	{
		parent::__construct($app);
	}

	function stat()
	{
		$pageid = intval($_GET['pageid']);
		if ($pageid && $page = table('page', $pageid))
		{
			$pv = $page['pv'] + loader::model('pv')->set($pageid);
			$data = $this->json->encode(array('pv'=>$pv));
			echo $_GET['jsoncallback']."($data);";
		}
	}
}