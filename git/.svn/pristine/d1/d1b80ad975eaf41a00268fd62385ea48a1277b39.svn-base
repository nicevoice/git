<?php
/**
 * 方案设置
 *
 * @aca 方案设置
 */
class controller_admin_mood extends mood_controller_abstract
{
	private $mood, $pagesize = 15;

	function __construct(&$app)
	{
		parent::__construct($app);
		$this->mood = loader::model('admin/mood');
		import('helper.folder');
	}

    /**
     * 管理方案
     *
     * @aca 浏览
     */
	function index()
	{
		$this->view->assign('head', array('title' => '管理方案'));
		$this->view->display("mood/index");
	}

    /**
     * 方案列表
     *
     * @aca 浏览
     */
	function page()
	{
		$where = null;
		$fields = '*';
		$order = '`sort` ASC';
		$page = max((isset($_GET['page']) ? intval($_GET['page']) : 1), 1);
		$size = max((isset($_GET['size']) ? intval($_GET['size']) : $this->pagesize), 1);
		$data = $this->mood->page($where, $fields, $order, $page, $size);
		$total = $this->mood->count($where);
		echo $this->json->encode(array('data' => $data, 'total' => $total));
	}

    /**
     * 添加方案
     *
     * @aca 添加
     */
	function add()
	{
		if ($this->is_post())
		{
			if ($moodid = $this->mood->add($_POST))
			{
				$result = array('state'=>true, 'data'=>$this->mood->get($moodid));
			}
			else
			{
				$result = array('state'=>false, 'error'=>$this->mood->error());
			}
			echo $this->json->encode($result);
		}
		else
		{
			$this->view->display('mood/add');
		}
	}

    /**
     * 编辑方案
     *
     * @aca 编辑
     */
	function edit()
	{
		$moodid = intval($_GET['moodid']);
		if ($this->is_post())
		{
			if ($this->mood->edit($moodid, $_POST) !== false)
			{
				$result = array('state'=>true, 'data'=>$this->mood->get($moodid));
			}
			else
			{
				$result = array('state'=>false, 'error'=>$this->mood->error());
			}
			echo $this->json->encode($result);
		}
		else
		{
			$rank = $this->mood->get($moodid);
			$this->view->assign($rank);
			$this->view->display('mood/edit');
		}
	}

    /**
     * 删除
     *
     * @aca 删除
     */
	function delete()
	{
		$moodid = $_POST['moodid'];

		$result = $this->mood->delete($moodid) ? array('state'=>true) : array('state'=>false, 'error'=>$this->mood->error());
		echo $this->json->encode($result);
	}

    /**
     * 排序
     *
     * @aca 浏览
     */
	function sort()
	{
		$moodid = intval($_POST['moodid']);
		$sort = $_POST['sort'];
		$result = $this->mood->sort_change($moodid, $sort) ? array('state'=>true) : array('state'=>false, 'error'=>'当前项已在末端,不可排序');
		echo $this->json->encode($result);
	}

    /**
     * 发布
     *
     * @aca 发布
     */
	function publish()
	{
		$infos =  $this->mood->by_sort();
		$channel = channel();
		foreach ($channel as $key => $value)
		{
			$ismake = $this->_publish($value['alias'], "list", $infos, $channel, $value['name'], $value['childids']);
			if(!$ismake) break;
			$return['info'] .= $value['name'].",";
		}
		
		$ismake = $this->_publish("index", "index", $infos, $channel);

		if ($ismake)
		{
			$return['state'] = true;
			$return['info'] .= '排行已生成';
		}
		else
		{
			$return['state'] = false;
			$return['error'] = '错误';
		}
		echo $this->json->encode($return);
	}
	
	function _publish($bulid_path, $tpl_path, $infos, $channel = array(), $name='', $childids='')
	{
		try {
			$page_file = WWW_PATH."mood/$bulid_path".SHTML;
			$this->template->assign('alias', $bulid_path);
			$this->template->assign('infos', $infos);
			$this->template->assign('channel', $channel);
			$this->template->assign('name', $name);
			$this->template->assign('childids', $childids);
			
			$html = $this->template->fetch("mood/$tpl_path.html");
			folder::create(dirname($page_file));
			write_file($page_file, $html);
		}
		catch (Exception $e) {
			$this->error =  "Exception = ".$e->getMessage();
			return false;
		}
		return true;
	}

    /**
     * 查看
     *
     * @aca 浏览
     */
	function view()
	{
		$modelid = $_GET['modelid'];
		$contentid = $_GET['contentid'];
		$alias = table('model', $modelid, 'alias');
		$url = ADMIN_URL.'?app='.$alias.'&controller='.$alias.'&action=view&contentid='.$contentid;
		$this->redirect($url);
	}
}