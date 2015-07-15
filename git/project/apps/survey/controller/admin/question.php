<?php
/**
 * 表单管理
 *
 * @aca 表单管理
 */
final class controller_admin_question extends survey_controller_abstract
{
	private $survey, $question;
	
	function __construct(& $app)
	{
		parent::__construct($app);
		if (!license('survey')) cmstop::licenseFailure();
		$this->survey = loader::model('admin/survey');
		$this->question = loader::model('admin/question');
	}

    /**
     * 设计表单
     *
     * @aca 浏览
     */
	function index()
	{
		$contentid = $_GET['contentid'];
		$survey = $this->survey->get($contentid);
		if (!$survey) $this->showmessage('调查不存在');
		
        $this->priv_category($survey['catid']);
        
        $this->view->assign($survey);
		$this->view->assign('head', array('title'=>'设计表单：'.$survey['title']));
        $this->view->display('question');
	}

    /**
     * 添加问题
     *
     * @aca 添加
     */
	function add()
	{
		$this->priv_category(table('content', $_REQUEST['contentid'], 'catid'));
		
		if ($this->is_post())
		{
			if ($questionid = $this->question->add($_POST))
			{
				$this->survey->set_inc('questions', $_POST['contentid']);
                $result = array('state'=>true, 'questionid'=>$questionid);
			}
			else 
			{
				$result = array('state'=>false, 'error'=>$this->question->error());
			}
			echo $this->json->encode($result);
		}
		else 
		{
			$head['title'] = '设计问卷';
		    $this->view->assign('head', $head);
			$this->view->display('question/'.$_GET['type'].'/add');
		}
	}

    /**
     * 编辑问题
     *
     * @aca 编辑
     */
	function edit()
	{		
		if ($this->is_post())
		{
			$this->priv_category(table('content', $_REQUEST['contentid'], 'catid'));
			
			$questionid = $_POST['questionid'];
			if ($this->question->edit($questionid, $_POST))
			{
				$result = array('state'=>true);
			}
			else 
			{
				$result = array('state'=>false, 'error'=>$this->question->error());
			}
			echo $this->json->encode($result);
		}
		else 
		{
			$questionid = $_GET['questionid'];
			$r = $this->question->get($questionid);
			if (!$r) $this->showmessage($this->question->error());
			
            $this->priv_category(table('content', $r['contentid'], 'catid'));
            
			$this->view->assign($r);

			$head['title'] = '编辑问卷：'.$t['title'];
		    $this->view->assign('head', $head);
			$this->view->display('question/'.$r['type'].'/edit');
		}
	}

    /**
     * 查看问题
     *
     * @aca 浏览
     */
	function view()
	{
		$questionid = $_GET['questionid'];
		$order = $_GET['order'];
		$r = $this->question->get($questionid);
		if (!$r) $this->showmessage($this->question->error());
		
		$this->priv_category(table('content', $r['contentid'], 'catid'));
		
		$this->view->assign('n',$order);
		$this->view->assign($r);
		$this->view->display('question/'.$r['type'].'/form');
	}

    /**
     * 选项排序
     *
     * @aca 选项排序
     */
	function sort()
	{
		foreach ($_GET['sort'] as $questionid=>$sort)
		{
			$result = $this->question->sort($questionid, $sort);
		}
		if ($result)
		{
			$result = array('state'=>true);
		}
		else
		{
			$result = array('state'=>true, 'error'=>$this->question->error());
		}
		echo $this->json->encode($result);
	}

    /**
     * 删除问题
     *
     * @aca 删除
     */
	function delete()
	{
		$questionid = $_GET['questionid'];
		$question = $this->question->get($questionid);
		
		$this->priv_category(table('content', $question['contentid'], 'catid'));
		
		$this->survey->set_dec('questions', $question['contentid']);
		$result = $this->question->delete($questionid) ? array('state'=>true) : array('state'=>false, 'error'=>$this->question->error());
		echo $this->json->encode($result);
	}

    /**
     * 上传图片
     *
     * @aca 上传
     */
	function upload()
	{
		$attachment = loader::model('admin/attachment', 'system');
		$file = $attachment->upload('Filedata', true, null, 'jpg|jpeg|gif|png|bmp', 2048);
		echo $file ? $attachment->aid[0].'|'.$file : '0';
	}
}