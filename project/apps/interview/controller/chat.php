<?php
class controller_chat extends interview_controller_abstract
{	
	function __construct(& $app)
	{
		parent::__construct($app);
	}
	
	function load()
	{
		$contentid = intval($_GET['contentid']);
		
		$chat = loader::model('chat');
		$data = $chat->ls($contentid);
		
		$data = $this->json->encode($data);
		echo $_GET['jsoncallback']."($data);";
	}
}