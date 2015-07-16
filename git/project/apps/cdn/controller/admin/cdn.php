<?php
/**
 * CDN管理
 *
 * @aca CDN管理
 */
class controller_admin_cdn extends cdn_controller_abstract
{
	public	$cdn, $cdn_rules, $cdn_type;

	function __construct(&$app)
	{
		parent::__construct($app);
		$this->cdn		= loader::model('admin/cdn');
		$this->cdn_type	= loader::model('admin/cdn_type');
		$this->cdn_rules	= loader::model('admin/cdn_rules');
		$this->cdn_parameter= loader::model('admin/cdn_parameter');
	}

    /**
     * CDN管理
     *
     * @aca CDN管理
     */
	function index()
	{
		$this->view->assign('head', array('title'=>'CDN接口'));
		$this->view->display('cdn/index');
	}

    /**
     * 接口列表
     *
     * @aca CDN管理
     */
	function page()
	{
		$where = null;
		$total = $this->cdn->count($where);
		$data = $this->cdn->page($where);
		$result = array('total'=>$total, 'data'=>$data);
		echo $this->json->encode($result);
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
			if ($cdnid = $this->cdn->add($_POST))
			{
				if ($this->cdn_parameter->add($cdnid, $_POST['par']))
				{
					exit('{"state":"true"}');
				}
				else
				{
					$this->cdn->delete("cdnid=$cdnid");
					exit($this->json->encode(array('state'=>false, 'error'=>'添加失败')));
				}
			}
			else
			{
				exit($this->json->encode(array('state'=>false, 'error'=>'添加失败')));
			}
		}
		else
		{
			$type	= $this->cdn_type->ls();
			$this->view->assign('type', $type);
			$this->view->display('cdn/add');
		}
	}

    /**
     * 编辑
     *
     * @aca 编辑
     */
	function edit()
	{
		$cdnid	= intval($_GET['cdnid']);
		$cdnid > 0 || $this->showmessage('CDN不存在');
		if ($this->is_post())
		{
			if ($this->cdn->edit($_POST, $cdnid))
			{
				if ($this->cdn_parameter->edit($cdnid, $_POST['par']))
				{
					exit('{"state":"true"}');
				}
				else
				{
					exit($this->json->encode(array('state'=>false, 'error'=>'添加失败')));
				}
			}
			else
			{
				exit($this->json->encode(array('state'=>false, 'error'=>'添加失败')));
			}
		}
		else
		{
			$cdn	= $this->cdn->get($cdnid);
			$type	= $this->cdn_type->ls();
			$cdn_par= $this->cdn_parameter->select("cdnid=$cdnid");
			$this->view->assign('cdn', $cdn);
			$this->view->assign('type', $type);
			$this->view->assign('par', $cdn_par);
			$this->view->display('cdn/edit');
		}
	}

    /**
     * 获取发布点
     *
     * @aca 获取发布点
     */
	function getpsn()
	{
		$psn = loader::model('admin/psn', 'system');
		$this->view->assign('psn', $psn->ls());
		$this->view->display('cdn/psn');
	}

    /**
     * 删除
     *
     * @aca 删除
     */
	function del()
	{
		$cdnid	= intval($_GET['cdnid']);
		$cdnid > 0 || $this->showmessage('CDN不存在');
		if ($this->cdn->delete("cdnid=$cdnid") && $this->cdn_rules->delete("cdnid=$cdnid"))
		{
			exit('{"state":"true"}');
		}
		else
		{
			exit($this->json->encode(array('state'=>false, 'error'=>'添加失败')));
		}
	}

    /**
     * 定时同步
     *
     * @aca 定时同步
     * @return array
     */
	function interval()
	{
		if ($path = $_SERVER['path'])
		{
			$path	= preg_replace('#'.PUBLIC_PATH.'(.*)#', '$1', $path);
			if (!$path)
			{
				$result	= array('state'=>0, 'error'=>'error path');
			}
			else
			{
				$rules	= $this->cdn_rules->rules($path);
				$excute	= $this->cdn->get_type($rules);
				$result	= $this->cdn_type->excute(array_unique($excute));
			}
		}
		else
		{
			$result	= array('state'=>0, 'error'=>'no path');
		}
		return $result;
	}
}