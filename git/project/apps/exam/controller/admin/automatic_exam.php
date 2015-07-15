<?php
/**
 * 问卷管理
 *
 * @aca 问卷管理
 */
final class controller_admin_automatic_exam extends exam_controller_abstract
{
    private $automatic;
	function __construct(& $app)
	{
		parent::__construct($app);
        $this->automatic = loader::model('admin/automatic');
	}

    public function index()
    {
        $this->view->display('automatic_index');
    }

    /**
     * 列表
     *
     * @aca 浏览
     */
    public function page()
    {
        $where = null;
        $fields = '*';
        $order = isset($_GET['orderby']) ? str_replace('|', ' ', $_GET['orderby']) : '`automaticid` DESC';

        if (isset($_GET['keywords']) && $_GET['keywords']) $where[] = where_keywords('title', $_GET['keywords']);
        if (is_numeric($_GET['catid']))
        {
            $catid = intval($_GET['catid']);
            $where[] = '`catid`='.$catid;
        }
        if ($where) $where = implode(' AND ', $where);
        $page = max((isset($_GET['page']) ? intval($_GET['page']) : 1), 1);
        $size = max((isset($_GET['pagesize']) ? intval($_GET['pagesize']) : $this->pagesize), 1);
        $data = $this->automatic->page($where, $fields, $order, $page, $size);
        $content = loader::model('admin/content', 'system');
        $category = $content->category;
        foreach($data as $k=>$v) {
            $data[$k]['created'] = time_format($v['created']);
            $data[$k]['catname'] = $category[$v['catid']]['name'];
        }
        $total = $this->automatic->count($where);

        $result = array('data'=>$data, 'total'=>$total);
        echo $this->json->encode($result);
    }


    public function del()
    {
        $id = $_REQUEST['mid'];
        $result = $this->automatic->delete($id) ? array('state'=>true,'info'=>'删除成功') : array('state'=>false,'error'=>$this->automatic->error);
        echo $this->json->encode($result);
    }


    public function cexam()
    {
        $id = $_REQUEST['id'] ? intval($_REQUEST['id']) : 0;
        if (!$id)die($this->json->encode(array('state'=>false,'error'=>'缺少ID')));
        $automatic = $this->automatic->get($id);
        $result = $this->automatic->cexam($automatic) ? array('state'=>true,'info'=>'生成成功') : array('state'=>false,'error'=>$this->automatic->error);
        echo $this->json->encode($result);
    }
    /**
     * 设置
     *
     * @aca 配置
     */
    public function setting()
    {
        if ($this->is_post())
        {
            foreach ($_POST['qtype'] as $k=>$qtype) {
                if (!$qtype['id'])unset($_POST['qtype'][$k]);
            }
            $_POST['subject'] = $_POST['subject'];
            $_POST['knowledge'] = array_unique(array_filter($_POST['knowledge']));
            $result = $this->automatic->add($_POST) ? array('state'=>true,'info'=>'保存成功') : array('state'=>false,'error'=>$this->automatic->error);
            echo $this->json->encode($result);
        } else {
            $head = array('title'=>'设置');
            $this->view->assign('head', $head);
            $this->view->assign('setting', $this->setting);
            $this->view->display('setting');
        }
    }
}