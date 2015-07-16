<?php
/**
 * CDN设置
 *
 * @aca CDN设置
 */
class controller_admin_setting extends cdn_controller_abstract
{
	public	$cdn, $cdn_rules, $cdn_type;

	function __construct(&$app)
	{
		parent::__construct($app);
		$this->cdn		= loader::model('admin/cdn');
		$this->cdn_type	= loader::model('admin/cdn_type');
	}

    /**
     * CDN设置
     *
     * @aca 设置管理
     */
	function index()
	{
		$this->view->assign('head', array('title'=>'CDN设置'));
		$this->view->display('setting/index');
	}

    /**
     * 设置列表
     *
     * @aca 设置管理
     */
	function page()
	{
		$where = null;
		$total = $this->cdn_type->count($where);
		$data = $this->cdn_type->page($where);
        foreach ($data as $key => $item)
        {
			$array		= json_decode($item['parameter']);
			$parameter	= '';
			foreach ($array as $pk => $pv)
			{
				$parameter	.= "$pk:$pv<br/>";
			}
			$data[$key]['parameter'] = $parameter ? $parameter : '无';
        }
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
			if ($this->cdn_type->add($_POST))
			{
				exit('{"state":"true"}');
			}
			else
			{
				exit($this->json->encode(array('state'=>false, 'error'=>'添加失败')));
			}
		}
		$type	= $this->cdn_type->get_cdn_file();
		$this->view->assign('type', $type);
		$this->view->display('setting/add');
	}

    /**
     * 编辑
     *
     * @aca 编辑
     */
	function edit()
	{
		$tid	= intval($_GET['tid']);
		$tid > 0 || $this->showmessage('ID不存在');
		if ($this->is_post())
		{
			if ($this->cdn_type->edit($_POST, $tid))
			{
				exit('{"state":"true"}');
			}
			else
			{
				exit($this->json->encode(array('state'=>false, 'error'=>'修改失败')));
			}
		}
		$type	= $this->cdn_type->get_cdn_file();
		$data	= $this->cdn_type->get($tid);
		$this->view->assign('type', $type);
		$this->view->assign('data', $data);
		$this->view->display('setting/edit');
	}

    /**
     * 删除
     *
     * @aca 删除
     */
	function delete()
	{
		$tid	= intval($_GET['tid']);
		$tid > 0 || $this->showmessage('ID不存在');
		if ($this->cdn->count("tid=$tid"))
		{
			 $this->showmessage('有设置规则的CDN接口无法删除');
		}
		if ($this->cdn_type->delete("tid=$tid"))
		{
			exit('{"state":"true"}');
		}
		else
		{
			exit($this->json->encode(array('state'=>false, 'error'=>'添加失败')));
		}
	}
}