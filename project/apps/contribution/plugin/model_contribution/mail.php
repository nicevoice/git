<?php
//发送邮件
//action： after_add after_submit,after_reject,after_publish
class plugin_mail extends object 
{
	private $contribution;
	
	public function __construct(& $contribution)
	{
		$this->contribution = $contribution;
	}
	
	public function after_add()
	{
		$this->sendmail('submit'); //投稿发送
	}
	public function after_submit()
	{
		$this->sendmail('submit'); //提交
	}
	public function after_reject()
	{
		$this->sendmail('reject');//拒绝
	}
	public function after_publish()
	{
		$this->sendmail('publish'); //发布
	}
	
	private function sendmail($action)
	{
		$contributionid = $this->contribution->contributionid;
		if(!$contributionid)
		{
			return;
		}
		$r = $this->contribution->get($contributionid);
		if(!$r || !$r['email'])
		{
			return;
		}
		//查看内容状态调整发送邮件
		if(!preg_match('/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/', $r['email']))
		{
			return;
		}
		$sitename = setting('system','sitename');
		$to = $r['email'];
		$subject = '';
		$message = '';
		switch ($action) {
			case 'submit':
				$subject = '您在'.$sitename.'的投稿“'.$r['title'].'”已提交，正在等待审核';
				break;
			case 'reject':
				$subject = '您在'.$sitename.'的投稿“'.$r['title'].'”被退回';
				break;
			case 'publish':
				$subject = '您在'.$sitename.'的投稿“'.$r['title'].'”已通过审核';
				break;
		}
		$template = & factory::template();
		$template->assign('data',$r);
		$template->assign('action',$action);
		$message = $template->fetch('contribution/mail.html');

        send_email($to, $subject, $message);
		return;
	}
}