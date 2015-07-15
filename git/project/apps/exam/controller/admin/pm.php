<?php
/**
 * 问卷管理
 *
 * @aca 问卷管理
 */
final class controller_admin_pm extends exam_controller_abstract
{
	private $exam, $pagesize = 15, $message;

	function __construct(& $app)
	{
		parent::__construct($app);
        $this->message = loader::model('exam_wechat_messages', 'exam');

	}
    public function index()
    {

        $this->view->assign('head', array('title' => '消息列表'));
        $this->view->display('pm/index');
    }

    public function page()
    {

        $page = isset($_GET['page']) ? intval($_GET['page']) : 0;
        $pagesize = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : $this->pagesize;
        $data = $this->message->page('', '*', 'created DESC', $page, $pagesize);
        foreach($data as $k=>$v) 
		{
            $data[$k]['created'] = time_format($v['created']);
			if($v['umengcode'])
			{
				$send_re=$this->json->decode(base64_decode($v['umengcode']));
			}
			$send_be=$send_re?$send_re['a']['ret']:"";
            $data[$k]['umeng'] = "<a href='javascript:send_umeng(".$v['messageid'].",\"".$send_be."\")'>推送</a>";
        }
        $result = array('total'=>$this->exam->total, 'data'=>$data);
        echo $this->json->encode($result);
    }

    /**
     * 编辑
     *
     * @aca 添加、编辑
     */
    function edit()
    {
        if ($this->is_post() && $_POST['messageid'])
        {
            $_POST['created'] =   $_POST['created']  ? strtotime($_POST['created']) : time();
            if ($this->message->update($_POST, $_POST['messageid']))
            {
                $result = array('state'=>true,'info'=> '修改成功');
            }
            else
            {
                $result = array('state'=>false, 'error'=>$this->message->error());
            }

            echo $this->json->encode($result);
        }
        else
        {
            $r =$this->message->get($_GET['id']);
            $r['created'] = date('Y-m-d H:i:s', $r['created']);
            $this->view->assign($r);
            $this->view->display('pm/edit');
        }
    }
    /**
     * 编辑
     *
     * @aca 添加、编辑
     */
    function add()
    {
        if ($this->is_post())
        {
            $_POST['created'] =   $_POST['created']  ? strtotime($_POST['created']) : time();
            if ($this->message->insert($_POST))
            {
                $result = array('state'=>true, 'info'=>'添加成功');
            }
            else
            {
                $result = array('state'=>false, 'error'=>$this->message->error());
            }

            echo $this->json->encode($result);
        }
        else
        {

            $this->view->display('pm/add');
        }
    }
    /**
     * 删除
     *
     * @aca 删除
     */
    function delete()
    {
        $result = $this->message->delete($_GET['id']) ? array('state'=>true) : array('state'=>false, 'error'=>$this->message->error());
        echo $this->json->encode($result);
    }

	function sendumeng()
	{
		$id = intval(value($_GET, 'id', 0));
		$result=array(
		          'state'=>false,
				  'error'=>'推送失败',
				  );
		if($id)
		{
			$re=$this->message->get($id);
			//require_once ROOT_PATH . 'extension/umeng/config.php';
			loader::import('umeng.umengController', ROOT_PATH . 'extension/');
			$umeng = new UmengSend();
			$re_a = $this->json->decode($umeng->sendAndroidBroadcast($re['subject'],$re['description'],time()+90));
			//$re_i = $this->json->decode($umeng->sendIOSBroadcast($re['subject'],$re['description'],time()+259200));
			$a_mess="失败";
			//$i_mess="失败";
			if($re_a['ret']=='SUCCESS')
			{
				$a_mess="成功";
			}
			/*
			if($re_i['ret']=='SUCCESS')
			{
				$i_mess="成功";
			}
			*/
			$this->message->update(array('umengcode'=>base64_encode(json_encode(array('a'=>$re_a,'i'=>$re_i)))), array('messageid'=>$id));
			$result = array('state'=>true, 'info'=>"推送执行成功[Android:$a_mess]");
		}
		echo $this->json->encode($result);
	}
}