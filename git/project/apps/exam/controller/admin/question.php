<?php
/**
 * 表单管理
 *
 * @aca 表单管理
 */
final class controller_admin_question extends exam_controller_abstract
{
	private $exam, $question;
	
	function __construct(& $app)
	{
		parent::__construct($app);
		if (!license('exam')) cmstop::licenseFailure();
		$this->exam = loader::model('admin/exam');
		$this->question = loader::model('admin/question');

	}

    /**
     * 设计表单
     *
     * @aca 浏览
     */
	function index()
	{
        $page = $_GET['page'] ? intval($_GET['page']) : 1;
        $where = '';
        $pagesize = 10;
        if (isset($_GET['keywords']) && $_GET['keywords']) $where .=  where_keywords('subject', $_GET['keywords']);
        if (is_numeric($_GET['subjectid']) && $_GET['subjectid'] > 0)
        {
            $subjectid = intval($_GET['subjectid']);
            $where .= ' AND ' .'`subjectid`='.$subjectid;
        }
        if (is_numeric($_GET['knowledgeid']) && $_GET['knowledgeid'] > 0)
        {
            $knowledgeid = intval($_GET['knowledgeid']);
            $where .= ' AND ' .'`knowledgeid`='.$knowledgeid;
        }
        if (is_numeric($_GET['qtypeid']) && $_GET['qtypeid'] > 0)
        {
            $qtypeid = intval($_GET['qtypeid']);
            $where .= ' AND ' .'`qtypeid`='.$qtypeid;
        }

        $ls  = $this->question->ls($where, '*', 'questionid DESC', $page, $pagesize);
        $count = $this->question->count($where);
        $pages = pages3536($count, $page, $pagesize);
        $this->view->assign('questions', $ls);
        $this->view->assign('pages', $pages);
        $this->view->assign('count', $count);
		$this->view->assign('head', array('title'=>'题库'));
        $this->view->display('question');
	}
    function index2()
    {
        $this->view->assign('head', array('title'=>'题库'));
        $this->view->display('question_list');
    }
    public  function write()
    {
        $this->question->write(61);
    }
    /**
     * 列表
     *
     * @aca 浏览
     */
    public function pages()
    {

        $where = null;
        $fields = '*';
        $order = isset($_GET['orderby']) ? str_replace('|', ' ', $_GET['orderby']) : '`questionid` DESC';
        $propertys = common_data('property_0', 'brand');
        if (isset($_GET['keywords']) && $_GET['keywords']) {
            if(is_numeric($_GET['keywords'])) {
                $where[] = " (1 AND `questionid`='".$_GET['keywords']."' OR ".where_keywords('subject', $_GET['keywords']).") ";

            }else{
                $where[] = where_keywords('subject', $_GET['keywords']);
            }
        }
        if (is_numeric($_GET['qtypeid']) && $_GET['qtypeid'] > 0)
        {
            $qtypeid = intval($_GET['qtypeid']);
            $where[] = '`qtypeid`='.$qtypeid;
        }
        if (is_numeric($_GET['subjectid']) && $_GET['subjectid'] > 0)
        {
            $subjectid = intval($_GET['subjectid']);
            $subject = $propertys[$subjectid];

            $subjectid = $subject['childids'] ? $subject['childids'] : $subjectid;
            $where[] = "`subjectid` in({$subjectid})";

        }

        if ($_GET['exam'] && empty($_GET['knowledgeid'])) {
            $knowledge =  config::get('exam' , 'knowledge');
            $knowledgeid = $knowledge[$subjectid];
            $know = $propertys[$knowledgeid];
            $knowledgeid = $know['childids'] ?  $know['childids'] : $knowledgeid;
            $where[] = "`knowledgeid` in({$knowledgeid})";
        } else {
            if (is_numeric($_GET['knowledgeid']) && $_GET['knowledgeid'] > 0)
            {

                $knowledgeid = intval($_GET['knowledgeid']);
                $know = $propertys[$knowledgeid];
                $knowledgeid = $know['childids'] ?  $know['childids'] : $knowledgeid;
                $where[] = "`knowledgeid` in({$knowledgeid})";
            }
        }
        if ($where) $where = implode(' AND ', $where);
        $page = max((isset($_GET['page']) ? intval($_GET['page']) : 1), 1);
        $size = max((isset($_GET['pagesize']) ? intval($_GET['pagesize']) : $this->pagesize), 1);
        $data = $this->question->page($where, $fields, $order, $page, $size);

        $total = $this->question->count($where);

        foreach ($data as $k=>$val) {
            $data[$k]['_subject'] = $propertys[$val['subjectid']]['name'];
            $data[$k]['qtype'] = $propertys[$val['qtypeid']]['name'];
            $data[$k]['knowledge'] = $propertys[$val['knowledgeid']]['name'];
        }

        $result = array('data'=>$data, 'total'=>$total);
        echo $this->json->encode($result);
    }
    function band()
    {
        if ($this->is_post()) {
            if (!$_POST['questionid'])exit($this->json->encode(array('state'=>true, 'question'=>null)));
            $questionids = implode(',', $_POST['questionid']);
            $questions = $this->question->select($questionids);
            $result = array('state'=>true, 'question'=>$questions);
            exit($this->json->encode($result));
        } else {
            $type = $_GET['type'];
            $subjectid = intval($_GET['subjectid']);
            $keyword = trim($_GET['keyword']);
            if ($keyword)$_k = " AND `subject` like '%{$keyword}%'";
            $questions = $this->question->page("`type`='{$type}' AND bandid =0  AND subjectid={$subjectid}{$_k}");
            if ($_GET['getJSON']) {
                die($this->json->encode($questions));
            }
            $this->view->assign('questions', $questions);
            $this->view->assign('catname', $this->propertys[$subjectid]['name']);
            $this->view->display('question/read/band');
        }

    }
    function serach()
    {
        if ($this->is_post()) {
            if (!$_POST['question'])exit($this->json->encode(array('state'=>true, 'question'=>null)));
            $questionids = implode(',', $_POST['question']);
            $questions = $this->question->select($questionids);

            $result = array('state'=>true, 'question'=>$questions);
            exit($this->json->encode($result));
        } else {
            $subjectid = intval($_GET['subjectid']);
            $knowledge =  config::get('exam' , 'knowledge');
            $knowledgeid = $knowledge[$subjectid];
            $this->pro_ids['knowledgeid'] = $knowledgeid;
            $this->view->assign('pro_ids',  $this->pro_ids);
            $this->view->display('question_search');
        }

    }
    function delband()
    {
        $questionid = $_GET['questionid'];
        if ($questionid)$this->question->update(array('bandid'=>0), $questionid);
    }
    /**
     * 添加问题
     *
     * @aca 添加
     */
	function add()
	{
		if ($this->is_post())
		{
			if ($questionid = $this->question->add($_POST))
			{
				//$this->exam->set_inc('questions', $_POST['contentid']);
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
	
    public function check_url()
    {

        if ($this->is_post() || $_GET['run']) {
            $where  = '';
            if (is_numeric($_REQUEST['subjectid']) && $_REQUEST['subjectid'] > 0){
                $subjectid = $this->propertys[$_REQUEST['subjectid']]['childids'] ? $this->propertys[$_REQUEST['subjectid']]['childids'] : $_REQUEST['subjectid'];
                $where .= " subjectid in({$subjectid}) ";

            }
            $page = $_GET['page'] ? intval($_GET['page']) : 1;
            $pagesize = 50;
            $question = $this->question->page($where,'questionid,bandid,md5id', null, $page, $pagesize);
            foreach($question as $q){
                $md5id = $q['bandid'] ? md5($q['bandid'].'exam') : md5($q['questionid'].'exam');
                if ($md5id != $q['md5id'])$this->question->update(array('md5id'=>$md5id), $q['questionid']);
            }
            $count = $this->question->count($where);
            if ($count > $page*$pagesize) {
                ++$page;
                $msg = $_REQUEST['subjectid']   ? $this->propertys[$_REQUEST['subjectid']]['name'] . '栏目下的题目URL 检查中...' : '检查中...';
                $this->view->assign('success', true);
                $this->view->assign('ms', 1000);
                $this->view->assign('message', $msg);
                $this->view->assign('url', "?app=exam&controller=question&action=check_url&page={$page}&subjectid={$_REQUEST['subjectid']}&run=1" );
                $this->view->display('showmessage', 'system');
                exit;
            } else {
                $msg = $_REQUEST['subjectid'] ? $this->propertys[$_REQUEST['subjectid']]['name'] . '栏目下的题目URL 完成' : '完成';
                $this->view->assign('success', true);
                $this->view->assign('message', $msg);
                $this->view->display('showmessage', 'system');
                exit;
            }
        }else {
            $this->view->display('check_url');
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
			$this->view->assign($r);

			$head['title'] = '编辑问卷：'.$r['title'];
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
		//$this->priv_category(table('content', $question['contentid'], 'catid'));
		//$this->exam->set_dec('questions', $question['contentid']);
		$result = $this->question->del($questionid) ? array('state'=>true) : array('state'=>false, 'error'=>$this->question->error());
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