<?php
/**
 * 留言本
 *
 * @aca 留言本
 */
class controller_admin_guestbook extends guestbook_controller_abstract
{
	private $guestbook, $pagesize = 10;

	function __construct(&$app)
	{
		parent::__construct($app);
		$this->guestbook = loader::model('admin/guestbook');
	}

    /**
     * 管理留言
     *
     * @aca 浏览
     */
	function index()
	{
		$this->view->assign('head', array('title'=>'管理留言'));
		$this->view->display("index");
	}

    /**
     * 留言列表
     *
     * @aca 浏览
     */
	function page()
	{
		$order = isset($_GET['orderby']) ? str_replace('|', ' ', $_GET['orderby']) : '`gid` DESC';
		$page = max((isset($_GET['page']) ? intval($_GET['page']) : 1), 1);
		$size = max((isset($_GET['pagesize']) ? intval($_GET['pagesize']) : $this->pagesize), 1);
		$data = $this->guestbook->ls($_GET, '*', $order, $page, $size);
		$total = $this->guestbook->total();
		echo $this->json->encode(array('data' =>$data, 'total' => $total));
	}

    /**
     * 回复留言
     *
     * @aca 回复
     */
	function reply()
	{
		$gid = intval($_GET['gid']);
		if($gid)
		{
			import('helper.iplocation');
			$this->iplocation = new iplocation();
			
			$data = $this->guestbook->get($gid);
			if(!$data['isview'])
			{
				$where = "gid = $gid";
				$isview = array('isview'=>1);
				$this->guestbook->update($isview,$where);
			}
			foreach ($data as $value)
			{
				$data['gender'] = ($value['gender'] == 1) ? '男' : '女';
				$data['location'] = $this->iplocation->get($data['ip']);
			}
			$data['addtime'] = date('Y/m/d',$data['addtime']);
			$data['content'] = $this->guestbook->replace_keyword($data['content']);
			$this->view->assign('data',$data);
			$this->view->display("reply");
		}
	}

    /**
     * 已回复
     *
     * @aca 已回复
     */
	function replyed()
	{
		$gid = intval($_GET['gid']);
		$where = "`gid` = $gid";
		$reply = $_POST['reply'];
		$data = array('replyer' => $this->_username, 'replytime' => TIME, 'reply' => $reply);
		if($this->guestbook->update($data, $where))
		{
			$result = array('state' =>true, 'message' => '回复成功');
			if($this->setting['option']['isemail'] && $this->setting['emailnotice']) $this->send_email($gid);
		}
		else
		{
			$result = array('state' =>false, 'error' => '发生错误');
		}
		echo $this->json->encode($result);
	}

    /**
     * 发送邮件
     *
     * @aca 发送邮件
     * @param $gid 留言ID
     * @return mixed
     */
	function send_email($gid)
	{
		$data = $this->guestbook->get($gid);
		$data['guestbookname'] = $this->setting['guestbookname'];
		$data['datetime'] = date('Y-m-d H:i:s',TIME);
		$data['manager'] = $this->setting['showmanage'];
		$to = $data['email'];
		$subject = $this->setting['emailtitle'];
		$message = $this->setting['emailcontent'];
		//留言者名  留言内容  回复内容 留言本名 回复时间 管理员名字
		foreach($data as $k =>$v)
		{
			$message = str_replace('{'.$k.'}',$v,$message);
		}
		
		//$message = str_replace(array('{content}','{reply}'),array($data['content'],$data['reply']),$message);
		$from = setting('system','mail');
		
		return send_email($to, $subject, $message, $from);
	}

    /**
     * 编辑内容
     *
     * @aca 编辑内容
     * @return bool
     */
	function edit_content()
	{
		if(empty($_POST['gid'])) return false;
		$gid = intval($_POST['gid']);
		$result = $this->guestbook->update(array('content' => $_POST['content']), "`gid`=$gid") 
				? array('state'=>true,'conentet'=>$this->guestbook->replace_keyword($_POST['content'])) 
				: array('state'=>false,'error'=>$this->guestbook->error());
		echo $this->json->encode($result);
	}

    /**
     * 删除
     *
     * @aca 删除
     */
	function delete()
	{
		$typeid = $_GET['gid'];
		$result = $this->guestbook->delete($typeid) ? array('state'=>true) : array('state'=>false,'error'=>$this->guestbook->error());
		echo $this->json->encode($result);
	}
}