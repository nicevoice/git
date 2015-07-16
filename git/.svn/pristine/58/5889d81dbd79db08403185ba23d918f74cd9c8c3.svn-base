<?php

abstract class exam_controller_abstract extends controller
{
	protected $app, $json, $template, $view, $config, $setting, $system, $_userid, $_username, $_groupid, $_roleid,$pro_ids,$exam_catid,$alphabet,$category,$propertys,$project_t,$_subject;

    function __construct(& $app)
    {
        parent::__construct();

		$this->app = $app;
		$this->_userid = & $app->userid;
		$this->_username = & $app->username;
		$this->_groupid = & $app->groupid;
		$this->_roleid = & $app->roleid;
        /**
         * 会计网
         */
        /* $user =  login_member();
        $this->_userid =  $user['user_id'];
        $this->_username = $user['name']; */
		$this->config = config::get('config');
		$this->setting = setting::get($app->app);
		$this->system = setting::get('system');
		$this->json = & factory::json();
		$array = array('_userid'=>$this->_userid, '_username'=>$this->_username, '_groupid'=>$this->_groupid, '_roleid'=>$this->_roleid);
        $this->pro_ids  = array('subjectid'=>7, 'knowledgeid'=>110000, 'qtypeid'=>101000);
        $this->propertys = common_data('property_0', 'brand');
		if ($app->client === 'admin')
		{
			$this->view = & factory::view($app->app);
            $this->view->assign('CONFIG',  $this->config);
			$this->view->assign('SETTING',  $this->setting);
			$this->view->assign('SYSTEM',  $this->system);
			$this->view->assign($array);
            $this->view->assign('propertys', $this->propertys);
            $this->view->assign('pro_ids', $this->pro_ids);
		}
        $this->exam_catid = array(11100,11200,11300,11500);
        $this->bbs_fid = array(11100=>110,11200=>120,11300=>130,11500=>150);
        $this->alphabet = array('A','B','C','D','E','F','G');

        $content = loader::model('content', 'system');
        $this->category = & $content->category;
        foreach($this->exam_catid as $catid) {
            $_types = get_property_child($this->category[$catid]['typeid']);
            $this->project_t[$this->category[$catid]['typeid']] = $this->propertys[$this->category[$catid]['typeid']];
            $this->project_t[$this->category[$catid]['typeid']]['child'] = $_types;
            foreach ($_types as $types){
                $this->_subject[$types['proid']] = $types;
            }
        }
		$this->template = & factory::template($app->app);
        //$this->template->ext = '';
		$this->template->assign('CONFIG',  $this->config);
		$this->template->assign('SETTING',  $this->setting);
		$this->template->assign('SYSTEM',  $this->system);
		$this->template->assign($array);
		$this->template->assign('alphabet', $this->alphabet);
		$this->template->assign('category', $this->category);
        $this->template->assign('_subject',  $this->_subject);
        $this->template->assign('propertys', $this->propertys);
        $this->template->assign('project_t', $this->project_t);
        $this->template->assign('bbs_fid', $this->bbs_fid);
        $this->template->assign('exam_catid', $this->exam_catid);
        $this->template->assign('pro_ids', $this->pro_ids);



    }
    
    public function execute()
    {
    	if ($this->action_exists($this->app->action))
    	{
    		$response = call_user_func_array(array($this, $this->app->action), $this->app->args);
    	}
    	else 
    	{
    		$this->_action_not_defined($this->app->action);
    	}
        return $response;
    }
    
    protected function _action_not_defined($action)
    {
        show404();
    	//$this->showmessage("<font color='red'>$action</font> 动作不存在");
    }
    
    protected function priv_category($catid)
    {
		if (is_numeric($catid))
		{
			if (!priv::category($catid)) $this->showmessage("您没有<span style='color:red'>".table('category', $catid, 'name')."($catid)</span>栏目权限！");
		}
		else
		{
			if (strpos($catid, ',')) $catid = explode(',', $catid);
			if (is_array($catid))
			{
				foreach ($catid as $id)
				{
					if (!priv::category($id)) $this->showmessage("您没有<span style='color:red'>".table('category', $id, 'name')."($id)</span>栏目权限！");
				}
			}
		}
    }
    /**
     * 提示信息，并halt
     *
     * @param string $message
     * @param string $url
     * @param int $ms 延迟
     * @param boolean $success 成功与否
     */
    function showmessage($message, $url = null, $ms = 2000, $success = false)
    {
        if(!$ms) $ms = 2000;

        $accept = value($_SERVER, 'HTTP_ACCEPT', '');
        if (stripos($accept, 'application/json') !== false || stripos($accept, 'text/javascript') !== false)
        {
            $result = array('state' => $success);
            $result[$success ? 'message' : 'error'] = $message;
            if ($url) $result['url'] = $url;
            $result = $this->json->encode($result);
            echo isset($_GET['jsoncallback']) ? $_GET['jsoncallback'] . "($result);" : $result;
            exit;
        }

        if ($this->app->client === 'admin')
        {
            $handler = $this->view;
            $template = 'showmessage';
        }
        else
        {
            $handler = $this->template;
            $template = 'exam/showmessage.html';
        }
        if (is_array($message)) $message = implode('<br />', $message);
        $handler->assign('message', $message);
        $handler->assign('url', $url);
        $handler->assign('ms', $ms);
        $handler->assign('success', $success);
        $handler->display($template, 'exam');
        exit;
    }

}