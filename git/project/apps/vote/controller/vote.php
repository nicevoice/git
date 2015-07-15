<?php

class controller_vote extends vote_controller_abstract
{
	private $vote;
	
	function __construct(& $app)
	{
		parent::__construct($app);
		$this->vote = loader::model('vote');
	}
	
	function vote()
	{
		if($this->vote->vote($_REQUEST['contentid'], $_REQUEST['optionid']))
		{
			$this->showmessage('投票成功', APP_URL.'?app=vote&controller=vote&action=result&contentid='.$_REQUEST['contentid'], 2000,true);
		}
		else
		{
			$this->showmessage($this->vote->error());
		}
	}

	function ajaxvote()
	{
		$contentid = intval($_GET['contentid']);
		if($this->vote->vote($contentid, $_REQUEST['optionid']))
		{	
			$r = $this->vote->get($contentid);
			if (!$r || $r['status'] != 6) $data = array();
			$result = json_encode(array(
				'state'=>true,
				'data'=>$r['option']
			));
		}
		else
		{
			$result = "{\"state\":false,\"error\":\"" .$this->vote->error() ."\"}";
		}
		exit($_GET['jsoncallback']."($result);");
	}
	
	function total()
	{
		$total = $this->vote->get_field('total', $_REQUEST['contentid']);
		exit($_GET['jsoncallback']."($total);");
	}
	
	function result()
	{
		$contentid = $_GET['contentid'];
		$r = $this->vote->get($contentid);
		if (!$r || $r['status'] != 6) $this->showmessage('查看的投票不存在或已删除');

		$template = 'vote/result.html';
        
		$this->template->assign($r);
		$this->template->assign('pos', $this->vote->category->pos($r['catid']));
		$this->template->display($template);
	}
}