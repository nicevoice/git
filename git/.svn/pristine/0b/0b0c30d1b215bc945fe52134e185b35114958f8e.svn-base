<?php
/**
 * 问卷管理
 *
 * @aca 问卷管理
 */
final class controller_admin_count extends exam_controller_abstract
{
	private $exam, $modelid, $pagesize = 15, $answer = null;

	function __construct(& $app)
	{
		parent::__construct($app);
		$this->exam = loader::model('admin/exam', 'exam');
		$this->_answer = loader::model('admin/answer', 'exam');
		$this->answer = loader::model('answer', 'exam');
		$this->member = loader::model('member', 'exam');
		$this->modelid = $this->exam->modelid;

	}
    public function index()
    {

        $this->view->assign('head', array('title' => '真题列表'));
        $this->view->display('count/index');
    }

    public function exam_count()
    {
        $contentid = isset($_GET['contentid']) ? intval($_GET['contentid']) : 0;
        if ($contentid == 0) showmessage('参数错误！');
        $this->view->assign('head', array('title' => '数据统计：'));
        $this->view->display('count/exam_count');
    }


    public function answer_report()
    {
        $answerid = isset($_GET['answerid']) ? intval($_GET['answerid']) : 0;
        $answer = $this->_answer->get_my_answer($answerid);
        $exams = $this->exam->get($answer['contentid']);
        $this->view->assign('answer', $answer);
        $this->view->assign('exams', $exams);
        //printR($answer);
        $this->view->display('count/answer_report');
    }
    public function exam_page()
    {

        $propertys = common_data('property_0', 'brand');
        $status = isset($_GET['status']) ? intval($_GET['status']) : 6;
        $page = isset($_GET['page']) ? intval($_GET['page']) : 0;
        $pagesize = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : $this->pagesize;
        $where = " status=6 AND modelid=12 AND isday=0";
        $fields = '*';
        $order = isset($_GET['orderby']) ? str_replace('|', ' ', $_GET['orderby']) : ($status >= 5 ? '`published` DESC' : '`contentid` DESC');
        if (is_numeric($_GET['subjectid']) && $_GET['subjectid'] > 0)
        {

            $subjectid = intval($_GET['subjectid']);
            $pro = $propertys[$subjectid];
            $subjectid = $pro['childids'] ?  $pro['childids'] : $subjectid;
            $where .= " AND `typeid` in({$subjectid})";
        }

        $data = $this->exam->ls($where, $fields, $order, $page, $pagesize, true);

        foreach($data as $k=>$v) {
            $data[$k]['subject'] = $propertys[$v['typeid']]['name'];
            $data[$k]['created'] = time_format($v['created']);
        }
        $result = array('total'=>$this->exam->total, 'data'=>$data);
        echo $this->json->encode($result);
    }
    public function answer_page()
    {
        $contentid = isset($_GET['contentid']) ? intval($_GET['contentid']) : 0;
        $page = isset($_GET['page']) ? intval($_GET['page']) : 0;
        $pagesize = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : $this->pagesize;
        $where = " isfinish=0 AND contentid={$contentid}";
        $fields = '*';
        $order = '`created` DESC';
        $exam = &factory::db()->get("SELECT qcount,examtime FROM #table_exam WHERE contentid={$contentid} LIMIT 1");
        //$member = loader::model('member_front', 'member');
        $data = $this->_answer->page($where, $fields, $order, $page, $pagesize);
        foreach ($data as $k=>$v) {
            $data[$k]['correct'] = floor($v['right']/$exam['qcount']*100);
            $data[$k]['exam_time'] = $exam['examtime'];
            $data[$k]['examtime'] = floor($v['examtime']/60);
            //$_m = $member->get($v['createdby'], 'username');
            //$data[$k]['createdbyname'] = $_m['name'];
            $data[$k]['createdbyname'] = table('member', $v['createdby'], 'username');
        }
        $result = array('total'=>$this->_answer->count($where), 'data'=>$data);
        echo $this->json->encode($result);
    }
}