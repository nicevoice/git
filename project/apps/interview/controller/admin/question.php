<?php
/**
 * 网友互动
 *
 * @aca 网友互动
 */
final class controller_admin_question extends interview_controller_abstract
{
	private $question, $pagesize = 10;
	
	function __construct(& $app)
	{
		parent::__construct($app);
		if (!license('interview')) cmstop::licenseFailure();
		$this->question = loader::model('admin/question');
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
        
		$state = isset($_GET['state']) ? intval($_GET['state']) : 1;
        $this->view->assign('state', $state);
        $this->view->assign('head', array('title'=>'网友提问：'.$r['title']));
        $this->view->display('question');
	}

    /**
     * 列表
     *
     * @aca 浏览
     */
	function page()
	{
		$contentid = intval($_GET['contentid']);
		$state = isset($_GET['state']) ? intval($_GET['state']) : 1;
		
		$where = "`contentid`=$contentid AND `state`=$state ";
		$fields = '*';
		$order = '`questionid` DESC';
		$page = isset($_GET['page']) ? intval($_GET['page']) : 0;
		$pagesize = empty($_GET['pagesize']) ? $this->pagesize : $_GET['pagesize'];
		
		$data = $this->question->page($where, $fields, $order, $page, $pagesize);
		$result = array('total'=>$this->question->count($where), 'data'=>$data);
        echo $this->json->encode($result);
	}

    /**
     * 编辑提问
     *
     * @aca 编辑
     */
    function edit()
    {
        $questionid = intval(value($_REQUEST, 'questionid'));

        if (!$questionid || !($question = $this->question->get($questionid)))
        {
            $this->showmessage('提问不存在');
        }

        if ($this->is_post())
        {
            if ($this->question->edit($questionid ,$_POST))
            {
                $result = array('state' => true);
            }
            else
            {
                $result = array('state' => false, 'error' => $this->question->error());
            }

            echo json_encode($result);
        }
        else
        {
            $this->view->assign($question);
            $this->view->display('question/edit');
        }
    }

    /**
     * 删除提问
     *
     * @aca 删除
     */
	function delete()
	{
		$questionid = $_GET['questionid'];
		$result = $this->question->delete($questionid) ? array('state'=>true) : array('state'=>false, 'error'=>$this->question->error());
		echo $this->json->encode($result);
	}

    /**
     * 清空提问
     *
     * @aca 清空
     */
	function clear()
	{
		$contentid = $_GET['contentid'];
		$result = $this->question->clear($contentid) ? array('state'=>true) : array('state'=>false, 'error'=>$this->question->error());
		echo $this->json->encode($result);
	}

    /**
     * 彻底删除提问
     *
     * @aca 彻底删除
     */
	function remove()
	{
		$questionid = $_REQUEST['questionid'];
		$result = $this->question->state($questionid, 0) ? array('state'=>true) : array('state'=>false, 'error'=>$this->question->error());
		echo $this->json->encode($result);
	}

    /**
     * 通过审核
     *
     * @aca 通过
     */
	function pass()
	{
		$questionid = $_REQUEST['questionid'];
		$result = $this->question->state($questionid, 2) ? array('state'=>true) : array('state'=>false, 'error'=>$this->question->error());
		echo $this->json->encode($result);
	}

    /**
     * 推荐提问
     *
     * @aca 推荐
     */
	function commend()
	{
		$questionid = $_REQUEST['questionid'];
		$result = $this->question->state($questionid, 3) ? array('state'=>true) : array('state'=>false, 'error'=>$this->question->error());
		echo $this->json->encode($result);
	}

    /**
     * IP 锁定
     *
     * @aca IP 锁定
     */
	function iplock()
	{
		$questionid = $_GET['questionid'];
		$result = $this->question->iplock($questionid) ? array('state'=>true) : array('state'=>false, 'error'=>$this->question->error());
		echo $this->json->encode($result);
	}

    /**
     * IP 解锁
     *
     * @aca IP 解锁
     */
	function ipunlock()
	{
		$questionid = $_GET['questionid'];
		$result = $this->question->ipunlock($questionid) ? array('state'=>true) : array('state'=>false, 'error'=>$this->question->error());
		echo $this->json->encode($result);
	}
}