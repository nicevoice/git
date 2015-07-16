<?php

abstract class magazine_controller_abstract extends controller
{
	protected $app, $json, $template, $view, $config, $setting, $system, $_userid, $_username, $_groupid, $_roleid;
    protected $html_root, $www_root, $uri;

    function __construct(& $app)
    {
        parent::__construct();

		$this->app = $app;
		$this->_userid = & $app->userid;
		$this->_username = & $app->username;
		$this->_groupid = & $app->groupid;
		$this->_roleid = & $app->roleid;

		$this->config = config::get('config');
		$this->setting = setting::get($app->app);
		$this->system = setting::get('system');

        // 特别的头部，声明APP的路径和URL
        $this->uri = loader::lib('uri','system');
        $u = $this->uri->psn($this->setting['path']);
        $this->html_root = $u['path'];
        $this->www_root  = $u['url'];
        $this->setting['html_root'] = $this->html_root;
        $this->setting['www_root'] = $this->www_root;
		
		$this->json = & factory::json();
		
		$array = array('_userid'=>$this->_userid, '_username'=>$this->_username, '_groupid'=>$this->_groupid, '_roleid'=>$this->_roleid);
		
		if ($app->client === 'admin')
		{
			$this->view = & factory::view($app->app);
		    $this->view->assign('CONFIG', $this->config);
			$this->view->assign('SETTING', $this->setting);
			$this->view->assign('SYSTEM', $this->system);
			$this->view->assign($array);
		}

		$this->template = & factory::template($app->app);
		$this->template->assign('CONFIG', $this->config);
		$this->template->assign('SETTING', $this->setting);
		$this->template->assign('SYSTEM', $this->system);
		$this->template->assign($array);
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
    	$this->showmessage("<font color='red'>$action</font> 动作不存在");
    }
}