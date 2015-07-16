<?php
class controller_sign extends activity_controller_abstract 
{
	private $sign,$seccode = true;

	function __construct(&$app)
	{
		parent::__construct($app);
		$this->sign = loader::model('sign');
	}
	
	function add()
	{
		if($this->is_post())
		{
			$signid = $this->sign->add($_POST);
			if($signid)
			{
				$r = loader::model('admin/activity', 'activity')->get($_POST['contentid']);
				if ($r['mailto']) {
					$sign = $this->sign->get($signid);
					$set = setting('system');

					$sendmail = & factory::sendmail();
					$selectedfiled = explode(',', $r['selected']);
					foreach ($selectedfiled as $v)
					{
						if($v == 'aid')
						{
							$aidmsg = loader::model('admin/attachment', 'system')->get($sign['aid']);
							$this->template->assign('aid', UPLOAD_URL.$aidmsg['filepath'].$aidmsg['filename']);
							continue;
						}
						$this->template->assign("$v", $sign[$v]);
					}
					$message = $this->template->fetch('activity/mail.html');
					if(!$sendmail->execute($r['mailto'], $r['title'].'_cmstop报名提醒', $message, $set['mail']['from']))
					{
						$this->showmessage('通知邮件发送失败！','back',3000);
					}
				}
				$this->showmessage('恭喜你，报名成功！','back', 3000, true);
			}
            else
			{			
				$this->showmessage($this->sign->error,'back',3000);
			}
		}
	}
	
}