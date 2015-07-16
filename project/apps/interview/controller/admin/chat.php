<?php
/**
 * 文字实录
 *
 * @aca 文字实录
 */
final class controller_admin_chat extends interview_controller_abstract
{
	private $chat, $pagesize = 10;
	
	function __construct(& $app)
	{
		parent::__construct($app);
		if (!license('interview')) cmstop::licenseFailure();
		$this->chat = loader::model('admin/chat');
	}

    /**
     * 浏览
     *
     * @aca 浏览
     */
	function index()
	{
		$contentid = $_GET['contentid'];
		
		$interview = loader::model('admin/interview');
        $r = $interview->get($contentid);
        if (!$r) $this->showmessage($interview->error());
        
        $this->priv_category($r['catid']);
        
        $this->view->assign($r);
        
		$data = $this->chat->ls($contentid);
        $data = $this->json->encode($data);
		$this->view->assign('data', $data);
        
        $head['title'] = '文字实录：'.$r['title'];
        $this->view->assign('head', $head);		
		$this->view->display('chat');
	}
	
	/**
     * 实录列表
     *
     * @aca 浏览
     */
	function chat()
	{
		$contentid = $_GET['contentid'];
		$data = $this->chat->ls($contentid);
		echo $this->json->encode($data);
	}

    /**
     * 添加
     *
     * @aca 添加
     */
	function add()
	{
		if ($this->is_post())
		{
			if ($chatid = $this->chat->add($_POST))
			{
                $result = array('state'=>true, 'data'=>$this->chat->get($chatid));
			}
			else 
			{
				$result = array('state'=>false, 'error'=>$this->chat->error());
			}
			echo $this->json->encode($result);
		}
	}

    /**
     * 编辑
     *
     * @aca 编辑
     */
	function edit()
	{
        $chatid = $_REQUEST['chatid'];
		if ($this->is_post())
		{
			if ($this->chat->edit($chatid, $_POST) !== false)
			{
				$result = array('state'=>true, 'data'=>$this->chat->get($chatid));
			}
			else 
			{
				$result = array('state'=>false, 'error'=>$this->chat->error());
			}
			echo $this->json->encode($result);
		}
		else 
		{
			$data = $this->chat->get($chatid);
			
			$this->view->assign($data);
			$this->view->display('chat_edit');
		}
	}
	
	/**
	 * 推荐文字实录中的内容到精彩观点
	 *
     * @aca 精彩观点推荐
	 */
	function recommend()
	{
		$chatid = $_GET['chatid'];
		$result = $this->chat->recommend($chatid) ? array('state'=>true) : array('state'=>false, 'error'=>$this->chat->error());
		echo $this->json->encode($result);
	}

    /**
     * 删除
     *
     * @aca 删除
     */
	function delete()
	{
		$chatid = $_GET['chatid'];
		$result = $this->chat->delete($chatid) ? array('state'=>true) : array('state'=>false, 'error'=>$this->chat->error());
		echo $this->json->encode($result);
	}
}