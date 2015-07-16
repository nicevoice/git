<?php
/**
 * 问卷管理
 *
 * @aca 问卷管理
 */
final class controller_admin_exam extends exam_controller_abstract
{
	private $exam, $modelid, $pagesize = 15, $weight = null;

	function __construct(& $app)
	{
		parent::__construct($app);
		if (!license('exam')) cmstop::licenseFailure();
		$this->exam = loader::model('admin/exam');
		$this->weight = loader::model('admin/admin_weight', 'system');
		$this->modelid = $this->exam->modelid;
		
		if (isset($_REQUEST['catid'])) $this->priv_category($_REQUEST['catid']);
	}

	public function __call($method, $args)
	{
		if(!priv::aca('exam', 'exam', $method)) return true;
		if(in_array($method, array('delete', 'clear', 'remove', 'restore', 'restores', 'approve', 'pass', 'reject', 'islock', 'lock', 'unlock', 'publish', 'unpublish'), true)) 
		{
			$var = in_array($method, array('clear', 'restores')) ? 'catid' : 'contentid';
			$result = $this->exam->$method($_REQUEST[$var]) ? array('state'=>true) : array('state'=>false, 'error'=>$this->exam->error());
			echo $this->json->encode($result);
		}
	}

    /**
     * 浏览
     *
     * @aca 浏览
     */
	function index()
	{
		$status = isset($_GET['status']) ? intval($_GET['status']) : 6;
		$this->view->assign('status', $status);
		
		$statuss = table('status');
		arsort($statuss);
		$this->view->assign('statuss', $statuss);
		
		$head['title'] = table('category', $_GET['catid'], 'name').'_'.table('status', $status, 'name').table('model', $this->exam->modelid, 'name');
		$this->view->assign('head', $head);
		$this->view->display('index');
	}

    /**
     * 列表
     *
     * @aca 浏览
     */
	function page()
	{
		$catid = intval($_GET['catid']);
		$status = isset($_GET['status']) ? intval($_GET['status']) : 6;
		$page = isset($_GET['page']) ? intval($_GET['page']) : 0;
		$pagesize = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : $this->pagesize;
		$where = $_GET;
		$fields = '*';
		$order = isset($_GET['orderby']) ? str_replace('|', ' ', $_GET['orderby']) : ($status >= 5 ? '`published` DESC' : '`contentid` DESC');

		$data = $this->exam->ls($where, $fields, $order, $page, $pagesize, true);
		
		$result = array('total'=>$this->exam->total, 'data'=>$data);
		echo $this->json->encode($result);
	}

    /**
     * 搜索
     *
     * @aca 浏览
     */
	function search()
	{
		$catid = isset($_GET['catid']) ? intval($_GET['catid']) : 0;
		$modelid = isset($_GET['modelid']) ? intval($_GET['modelid']) : 0;
		$status = isset($_GET['status']) ? intval($_GET['status']) : 0;

		$this->view->assign('catname', $this->exam->category[$catid]['name']);
		$this->view->assign('modelname', table('model', $modelid, 'name'));
		$this->view->assign('statusname', table('status', $status, 'name'));
		$this->view->display('search');
	}

    /**
     * 添加
     *
     * @aca 添加
     */
	function add()
	{
		if ($this->is_post())
		{
			if ($contentid = $this->exam->add($_POST))
			{
				$result = array('state'=>true, 'contentid'=>$contentid);
				$article = $this->exam->get($contentid, 'url, status, title'); 
                $this->exam->mySystemMessage($_POST['catid'], $article['title'], $article['url']);
				$article['status'] == 6 && $result['url'] = $article['url'];
			}
			else
			{
				$result = array('state'=>false, 'error'=>$this->exam->error());
			}
			echo $this->json->encode($result);
		}
		else
		{
			$catid = $_GET['catid'];
			$myweight = array_shift($this->weight->get_by('userid', $this->_userid, 'weight'));
			$weight = $this->setting['weight'];
			$data = array('minhours'=>24,
			              'status'=>1,
						  'weight' => $myweight ? (($myweight-$weight)>=0 ? $weight : $myweight) : 0,
						  'allowcomment'=>1,
						 );

			$this->view->assign($data);
			$this->view->assign('catname', $this->exam->category[$catid]['name']);
			$this->view->assign('head', array('title'=>'发布题库'));
			$this->view->assign('repeatcheck', value(setting::get('system'), 'repeatcheck', 0));
			$this->view->display('add');
		}
	}
    function edel()
    {
        $t = $_GET['t'];
        $id = $_GET['id'];
        if (!$id)return '';
        if ($t == 'qtype') {
            loader::model('admin/exam_qtype', 'exam')->delete($id);
        } else if($t == 'question') {
            loader::model('admin/exam_qtype_question', 'exam')->delete($id);
        }
    }
    /**
     * 编辑
     *
     * @aca 编辑
     */
	function edit()
	{
		if ($this->is_post())
		{

			if ($contentid = $this->exam->edit($_POST['contentid'], $_POST))
			{
				$result = array('state'=>true, 'contentid'=>$_POST['contentid']);
			}
			else
			{
				$result = array('state'=>false, 'error'=>$this->exam->error());
			}
			echo $this->json->encode($result);
		}
		else
		{
			$myweight = array_shift($this->weight->get_by('userid', $this->_userid, 'weight'));
			$contentid = $_GET['contentid'];
			$data = $this->exam->get($contentid, '*', 'get');
           // printR($data);
			if (!$data) $this->showmessage($this->exam->error());
			
			$this->priv_category($data['catid']);
			
			$this->exam->lock($contentid);
			
			$this->view->assign($data);
			$this->view->assign('myweight', $myweight);
			$this->view->assign('head', array('title'=>'编辑题库：'.$data['title']));
			$this->view->display('edit');
		}
	}

    /**
     * 查看
     *
     * @aca 浏览
     */
	function view()
	{
		$r = $this->exam->get($_GET['contentid'], '*', 'view');
		if (!$r) $this->showmessage($this->exam->error());
		
		$this->priv_category($r['catid']);
		
		$this->view->assign($r);        
		$this->view->assign('head', array('title'=>$r['title']));
		$this->view->display('view');
	}
	
	/**
	 * 生成调用代码HTML
     *
     * @aca 获取调用代码
	 */
	function code()
	{
		$contentid = $_GET['contentid'];
		$r = $this->exam->get($contentid);
		if (!$r)
		{
			$this->showmessage($this->exam->error());
		}
		if ($r['status'] != 6) $this->showmessage('数据状态错误，请先发布！');
		$questions = $r['question'];
		$template = 'exam/code.html';
		$this->template->assign($r);
		$this->template->assign('questions', $questions);
		$code = $this->template->fetch($template);
		$this->view->assign('code', $code);
		$this->view->display('code');
	}

	/**
     * 清空题库记录
     *
     * @aca 清空题库记录
     */
	function data_clear()
	{		
		$this->priv_category(table('content', $_REQUEST['contentid'], 'catid'));

		$result = $this->exam->data_clear($_REQUEST['contentid']) ? array('state'=>true) : array('state'=>false, 'error'=>$this->exam->error());
		echo $this->json->encode($result);
	}

    /**
     * 相关
     *
     * @aca 相关
     */
	function related()
	{
		$keywords = $_GET['keywords'];
		$catid = intval($_GET['catid']);
		$modelid = intval($_GET['modelid']);
		$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
		$pagesize = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : 20;

		$data = $this->exam->content->related($catid, $modelid, $keywords, $page, $pagesize);
		$result = $page == 1 ? array('state'=>true, 'data'=>$data, 'total'=>$this->exam->content->related_total($catid, $modelid, $keywords)) : array('state'=>true, 'data'=>$data);
		echo $this->json->encode($result);
	}

    /**
     * 引用
     *
     * @aca 引用
     */
	function reference()
	{
		if ($this->is_post())
		{
			$contentid = $_REQUEST['contentid'];
			$catid = $_REQUEST['catid'];
			if (is_array($catid))
			{
				foreach ($catid as $cid)
				{
					$result = $this->exam->reference($contentid, $cid);
					if (!$result) break;
				}
			}
			else
			{
				$result = $this->exam->reference($contentid, $catid);
			}
			$result = $result ? array('state'=>true) : array('state'=>false, 'error'=>$this->exam->error());
			echo $this->json->encode($result);
		}
		else 
		{
			$category = table('category');
			foreach ($category as $k=>$c)
			{
				$category[$k]['checkbox'] = '';
				if ($c['childids'])
				{
					if (!priv::category($k, true)) unset($category[$k]);
				}
				elseif (!priv::category($k))
				{
					unset($category[$k]);
				}
				else 
				{
					$category[$k]['checkbox'] = '<input type="checkbox" name="catid[]" value="'.$c['catid'].'" class="radio_style" />';
				}
			}
			import('helper.treeview');
			$treeview = new treeview($category);
			$data = $treeview->get(null, 'category_tree', '<li><span id="{$catid}">{$checkbox}{$name}</span>{$child}</li>');
			$this->view->assign('data', $data);
			$this->view->display('content/reference', 'system');
		}
	}

    /**
     * 移动
     *
     * @aca 移动
     */
	function move()
	{
		if ($this->is_post())
		{
			$contentid = $_REQUEST['contentid'];
			$catid = $_REQUEST['catid'];
			$result = $this->exam->move($contentid, $catid) ? array('state'=>true, 'contentid'=>$contentid) : array('state'=>false, 'error'=>$this->exam->error());
			echo $this->json->encode($result);
		}
		else 
		{
			$category = table('category');
			foreach ($category as $k=>$c)
			{
				$category[$k]['radio'] = '';
				if ($c['childids'])
				{
					if (!priv::category($k, true)) unset($category[$k]);
				}
				elseif (!priv::category($k))
				{
					unset($category[$k]);
				}
				else 
				{
					$category[$k]['radio'] = '<input type="radio" name="catid" value="'.$c['catid'].'" class="radio_style" />';
				}
			}
			import('helper.treeview');
			$treeview = new treeview($category);
			$data = $treeview->get(null, 'category_tree', '<li><span id="{$catid}">{$radio}{$name}</span>{$child}</li>');
			$this->view->assign('data', $data);
			$this->view->display('content/move', 'system');
		}
	}

    /**
     * 定时上下线
     *
     * @aca cron 定时上下线
     */
	function cron()
	{
		@set_time_limit(600);
		
		$publishid = $this->exam->content->cron_publish($this->modelid);
		if ($publishid) array_map(array($this->exam, 'publish'),  $publishid);
		
		$unpublishid = $this->exam->content->cron_unpublish($this->modelid);
		if ($unpublishid) array_map(array($this->exam, 'unpublish'),  $unpublishid);
		
		exit ('{"state":true}');
	}

    /**
     * 添加属性
     */
    function property()
    {

        if ($this->is_post()) {
            if ($contentid = $this->exam->add_property($_POST))
            {
                $result = array('state'=>true, 'contentid' => $contentid);
            }
            else
            {
                $result = array('state'=>false, 'error'=>$this->activity->error());
            }
            echo $this->json->encode($result);


            exit;
        }
        $this->view->display('property/add', 'exam');
    }
	
	/**
	*	手机APP版本号
	*/
	function release()
	{
		if ($this->is_post())
		{
		   $setting = new setting();
		   $result = $setting->set_array($this->app->app, $_POST['setting']) ? array('state'=>true,'message'=>'保存成功') : array('state'=>false,'error'=>'保存失败');
		   echo $this->json->encode($result);
		}else{
			$this->view->assign('setting', $this->setting);
			$this->view->display('release', 'exam');
		}
	}
}