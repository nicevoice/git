<?php
class controller_question extends interview_controller_abstract
{
	private $question, $interview;
	
	function __construct(& $app)
	{
		parent::__construct($app);
		$this->question = loader::model('question');
		$this->interview = loader::model('interview');
	}
	
	function load()
	{
		$contentid = intval($_GET['contentid']);
		
		$data = $this->question->ls($contentid);
		$data = $this->json->encode($data);
		echo $_GET['jsoncallback']."($data);";
	}
	
	function add()
	{
		$r = $this->interview->get(intval($_GET['contentid']));
		if (!$r || $r['status'] != 6)
		{
			$result = array('state'=>false, 'error'=>'访谈记录不存在'.$_GET['contentid']);
			$data = $this->json->encode($result);
			echo $_GET['jsoncallback']."($data);";
			exit;
		}

		if(!$this->_userid && !$r['visitorchat'])
		{
			$result = array('state'=>false, 'error'=>'禁止游客发言，请登录后重试');
			$data = $this->json->encode($result);
			echo $_GET['jsoncallback']."($data);";
			exit;
		}

		if (!$r['allowchat'])
		{
			$result = array('state'=>false, 'error'=>'禁止网友提问');
			$data = $this->json->encode($result);
			echo $_GET['jsoncallback']."($data);";
			exit;
		}

		if ($r['startchat'] && $r['startchat'] > TIME)
		{
			$result = array('state'=>false, 'error'=>'网友提问未开始，请于'.date('Y年n月j日H点i分').'以后开始提问。');
			$data = $this->json->encode($result);
			echo $_GET['jsoncallback']."($data);";
			exit;
		}
		
		if ($r['endchat'] && $r['endchat'] < TIME)
		{
			$result = array('state'=>false, 'error'=>'网友提问已结束');
			$data = $this->json->encode($result);
			echo $_GET['jsoncallback']."($data);";
			exit;
		}
		
		$ipbanned = loader::model('ipbanned', 'system');
		if (!$ipbanned->is_expired())
		{
			$result = array('state'=>false, 'error'=>'提问失败，您的IP已被管理员锁定。');
			$data = $this->json->encode($result);
			echo $_GET['jsoncallback']."($data);";
			exit;
		}
		
        $data = $_REQUEST;
        $data['state'] = $r['ischeck'] ? 1 : 2;
        
		if ($questionid = $this->question->add($data))
		{
            $result = array('state'=>true, 'ischeck' =>(bool)$r['ischeck']);
		}
		else 
		{
			$result = array('state'=>false, 'error'=>$this->question->error());
		}
		$data = $this->json->encode($result);
		echo $_GET['jsoncallback']."($data);";
	}
}