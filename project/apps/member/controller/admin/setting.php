<?php
/**
 * 设置
 *
 * @aca 设置
 */
class controller_admin_setting extends member_controller_abstract
{
	private $member, $ucenter;
	
	function __construct(& $app)
	{
		parent::__construct($app);
		$this->member = loader::model('member');
        $this->ucenter = loader::model('ucenter');
	}

    /**
     * 基本设置
     *
     * @aca 基本设置
     */
	public function index()
	{
		if ($this->is_post())
		{
			$_POST['setting']['default_group'] = $_POST['setting']['groupid'] = empty($_POST['need_audit'])?6:4;
			$setting = new setting();
			$setting->set_array('member',$_POST['setting']);
			$return = array('state'=>true, 'message'=>'修改成功');
			echo $this->json->encode($return);
		}
		else
		{
			$st = setting('member');
			$head = array('title'=>'注册设置');
			
			$this->view->assign('head', $head);
			$this->view->assign('setting', $st);
			$this->view->display('setting/index');
		}
	}

    /**
     * UCenter 设置
     *
     * @aca 整合设置
     */
	public function ucenter()
	{
		if ($this->is_post())
		{
			$do_merge = 0;
			if($_POST['uc'] == 'ucenter' && $_POST['uc_connect'] == 'mysql')
			{
				$uc = array(
					'dbhost' => $_POST['uc_dbhost'],
					'dbport' => $_POST['uc_dbport'],
					'dbname' => $_POST['uc_dbname'],
					'dbpw' => $_POST['uc_dbpw'],
					'dbtablepre' => $_POST['uc_dbtablepre'],
					'dbuser' => $_POST['uc_dbuser']
				);
				$result = $this->ucenter->uctest($uc);
				if(!$result['state'])
				{
					echo $this->json->encode($result);exit;
				}
				$do_merge = 1;
			}
			$setting = new setting();
			$setting->set_array('member',$_POST);
			echo $this->json->encode(array('state'=>true,'message'=>'更新成功','do_merge' => $do_merge));
		}
		else
		{
			$set = setting('member');
			$set['uc_connect'] || $set['uc_connect'] = 'mysql';
			$set['uc_dbhost'] || $set['uc_dbhost'] = 'localhost';
			$set['uc_dbuser'] || $set['uc_dbuser'] = 'root';
			$set['uc_dbtablepre'] || $set['uc_dbtablepre'] = 'uc_';
		
			$head = array('title'=>'Ucenter设置');
			$this->view->assign($set);
			$this->view->assign('head', $head);
			$this->view->display('setting/ucenter');
		}
	}

    /**
     * UCenter 同步
     *
     * @aca 用户合并
     */
	public function ucenter_sync()
	{
		$set = setting('member');
		$type = $_GET['type'];
		if($type == 'uc') {
			$options = array(
				'dbhost'	=>	$set['uc_dbhost'],
				'dbport'    =>  $set['uc_dbport'],
				'dbuser'	=>	$set['uc_dbuser'],
				'dbpw'		=>	$set['uc_dbpw'],
				'dbname'	=>	$set['uc_dbname'],
				'dbtablepre'=>	$set['uc_dbtablepre'],
			);
		} else if( $type == 'pw') {
			$options = array(
				'dbhost'	=>	$set['pw_dbhost'],
				'dbport'    =>  $set['pw_dbport'],
				'dbuser'	=>	$set['pw_dbuser'],
				'dbpw'		=>	$set['pw_dbpw'],
				'dbname'	=>	$set['pw_dbname'],
				'dbtablepre'=>	$set['pw_dbtablepre'],
			);
		}
		$result = $this->ucenter->uctest($options);
		if($result['state'])
		{
			$ctdb = & factory::db();
			$ctuser = $ctdb->get("SELECT COUNT(*) AS num FROM #table_member");
			$ucdb = factory::db(array(
				'driver' => $set['uc_connect'],
				'host' => $set['uc_dbhost'],
				'port' => $set['uc_dbport'],
				'username' => $set['uc_dbuser'],
				'password' => $set['uc_dbpw'],
				'dbname' => $set['uc_dbname'],
				'prefix' => $set['uc_dbtablepre'],
				'pconnect' => $set['uc_dbconnect'],
				'charset' => $set['uc_dbcharset'],
			));
			$ucuser = $ucdb->get("SELECT COUNT(*) AS num FROM #table_members");
			$this->view->assign('uc_num', $ucuser['num']);
			$this->view->assign('ct_num', $ctuser['num']);
		}
		
		$head = array('title'=>'整合用户');
		$this->view->assign('head', $head);
		$this->view->assign('dbtest', $result);
		$this->view->display('setting/ucenter_sync');
	}

    /**
     * 用户合并
     *
     * @aca 用户合并
     */
	public function merge()
	{
		set_time_limit(0);
		$start = intval($_REQUEST['start']);		//开始 $_REQUEST
		$perpage = max(1, intval($_REQUEST['perpage']));	//每页
		$total =  intval($_REQUEST['total']);		//总数
		$type = $_GET['type'];
		
		$ucmodel = loader::model('ucenter');
		
		if($type == 'uc') {
			$success = $ucmodel->uc_merge($start, $perpage, $total);
		} elseif($type == 'pw') {
			$success = $ucmodel->pw_merge($start, $perpage, $total);
		} else {
			echo $this->json->encode(array('state' =>false,'error' =>'不存在的连接方式'));exit;
		}
		$offset = $start+$perpage;
		$finished = 0;
		if($offset > $total)
		{
			$finished = 1;
		}
		$result = array(
			'state' => true,
			'success' => $success,
			'start' => $offset,
			'perpage' => $perpage,
			'total' => $total,
			'finished' => $finished
		);
		echo $this->json->encode($result);
	}

    /**
     * 连接测试
     *
     * @aca 整合设置
     */
	public function uctest()
	{
		$result = $this->ucenter->uctest($_POST);
		echo $this->json->encode($result);
	}

     /**
     * Phpwind 整合
     *
     * @aca 整合设置
     */
	public function phpwind()
	{
		if ($this->is_post())
		{
			$do_merge = 0;
			if($_POST['uc'] == 'phpwind' && $_POST['pw_connect'] == 'mysql')
			{
				$uc = array(
					'dbhost' => $_POST['pw_dbhost'],
					'dbport' => $_POST['pw_dbport'],
					'dbname' => $_POST['pw_dbname'],
					'dbpw' => $_POST['pw_dbpw'],
					'dbtablepre' => $_POST['pw_dbtablepre'],
					'dbuser' => $_POST['pw_dbuser']
				);
				$result = $this->ucenter->uctest($uc);
				if(!$result['state'])
				{
					echo $this->json->encode($result);exit;
				}
				$do_merge = 1;
			}
			$setting = new setting();
			$setting->set_array('member',$_POST);
			echo $this->json->encode(array('state'=>true,'message'=>'更新成功,请重新登录','do_merge' => $do_merge));
		}
		else
		{
			$set = setting('member');
			$head = array('title'=>'Phpwind整合');
			$this->view->assign($set);
			$this->view->assign('head', $head);
			$this->view->display('setting/phpwind');
		}
	}
}