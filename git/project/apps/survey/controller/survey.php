<?php

class controller_survey extends survey_controller_abstract
{
	private $survey;
	
	function __construct(& $app)
	{
		parent::__construct($app);
		$this->survey = loader::model('survey');
	}

	function answer()
	{
        $contentid = intval($_REQUEST['contentid']);

        if (!$contentid)
        {
            $this->_response(array('error' => '您访问的内容不存在！', 'url' => 'back'));
        }

        if (!$this->_userid && $this->survey->get_field('checklogined', $contentid))
        {
            $this->_response(array('error' => '您尚未登录，请在登录后参与调查', 'url' => url('member/index/login')));
        }

        if($this->survey->answer($contentid, $_REQUEST['data']))
        {
            $r = $this->survey->get($_REQUEST['contentid']);
            if ($r['mailto']) {
                $data = array();
                $this->question = loader::model('admin/question');
                foreach ($_REQUEST['data'] as $aid=>$answer)
                {
                    $question = $this->question->get($aid);
                    $option = $tmp = array();
                    foreach ($question['option'] as $k=>$v)
                    {
                        $option[$v['optionid']] = $v['name'] ;
                    }
                    $tmp['question'] = $question['subject'];

                    if(is_array($answer))
                    {
                        if(is_array($answer['optionid']))
                        {
                            foreach ($answer['optionid'] as $k => $oid)
                            {
                                $tmp['answer'][] = $option[$oid];
                            }
                        }
                        else
                        {
                            $tmp['answer'][] = $option[$answer['optionid']];
                        }

                        !empty($answer['content']) && $tmp['answer'][] = $answer['content'];
                    }
                    else
                    {
                        $tmp['answer'][] = $answer;
                    }
                    $tmp['answer'] = implode(',', $tmp['answer']);
                    $data[] = $tmp;
                }

                $set = setting('system');
                $this->template->assign('title', $r['title']);
                $this->template->assign('description', $r['description']);
                $this->template->assign('data',$data);
                $message = $this->template->fetch('survey/mail.html');
                if(! send_email($r['mailto'], $r['title'].'_cmstop报名提醒', $message, $set['mail']['from']))
                {
                    $this->_response('通知邮件发送失败！');
                }
            }
            $this->_response(array('error' => '感谢您的参与！', 'url' => 'back'));
        }
        else
        {
            $this->_response(array('error' => $this->survey->error(), 'url' => 'back'));
        }
	}

    protected function _response($data, $state = FALSE)
    {
        $key = $state ? 'message' : 'error';

        if (!is_array($data))
        {
            $data = array($key => $data);
        }

        if ($this->is_post())
        {
            $this->showmessage($data[$key], $data['url'], $data['delay'], $state);
        }
        elseif ($this->is_ajax() || isset($_REQUEST['jsoncallback']))
        {
            $result = json_encode($data);
            echo isset($_REQUEST['jsoncallback']) ? $_REQUEST['jsoncallback'] . "($result);" : $result;
            exit;
        }

        return FALSE;
    }
}