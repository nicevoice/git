<?php
abstract class interview_controller_abstract extends controller
{
	protected $app, $json, $template, $view, $config, $setting, $system, $_userid, $_username, $_groupid, $_roleid;

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
		
		$this->json = & factory::json();
		
		$array = array('_userid'=>$this->_userid, '_username'=>$this->_username, '_groupid'=>$this->_groupid, '_roleid'=>$this->_roleid);
		
		if ($app->client === 'admin')
		{
			$this->view = & factory::view($app->app);
		    $this->view->assign('CONFIG',  $this->config);
			$this->view->assign('SETTING',  $this->setting);
			$this->view->assign('SYSTEM',  $this->system);
			$this->view->assign($array);
		}

		$this->template = & factory::template($app->app);
		$this->template->assign('CONFIG',  $this->config);
		$this->template->assign('SETTING',  $this->setting);
		$this->template->assign('SYSTEM',  $this->system);
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
}