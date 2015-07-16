<?php
/**
 * 期号管理
 *
 * @aca 期号管理
 */
final class controller_admin_edition extends magazine_controller_abstract
{
	private $edition,$magazine,$page,$pagesize = 15;
	private $disabled = array('未发布', '已发布', '休眠');
	
	function __construct(& $app)
	{
		parent::__construct($app);
		if (!license('magazine')) cmstop::licenseFailure();
		$this->magazine = loader::model('admin/magazine');
		$this->edition = loader::model('admin/edition');
		$cookie = factory::cookie();
		$size = $cookie->get('editionsPageSize');
		intval($size) && $this->pagesize = $size;
	}

    /**
     * 浏览
     *
     * @aca 浏览
     */
	function index()
	{
		$magazines = $this->magazine->select(null, 'mid, name');
		$magazine = table('magazine', intval($_GET['id']));
		$this->view->assign('magazines', $magazines);
		$this->view->assign('edits', $this->edition->edits());
		$this->view->assign('size', $this->pagesize);
		$this->view->assign('head', array('title'=>$magazine['name']));
		$this->view->assign('disabledMap', $this->disabled);
		$this->view->display("edition/index");
	}

    /**
     * 列表
     *
     * @aca 浏览
     */
	function page()
	{
		if($_GET['orderby'] && !preg_match('/^[\w\|\.]+$/i', $_GET['orderby'])) exit;
		$order = $_GET['orderby'] ? str_replace('|', ' ', $_GET['orderby']) : 'total_number DESC';
		
		$page = max((isset($_GET['page']) ? intval($_GET['page']) : 1), 1);
		$size = max((isset($_GET['pagesize']) ? intval($_GET['pagesize']) : $this->pagesize), 1);
		
		$where = $this->edition->createWhere($_REQUEST);
		$mid = intval($_REQUEST['mid']);
		$total = $this->edition->count("mid = $mid");
		$m = table('magazine', $mid);
		
		$data = $this->edition->page($where, $order, $page, $size);
		foreach ($data as & $r)
		{
			$r['url'] = $this->www_root."/{$m['alias']}/{$r['year']}/{$r['eid']}/";
		}
		$rs = array('total' => $total, 'data' => $data);
		echo $this->json->encode($rs);
	}

    /**
     * 新建期号
     *
     * @aca 新建期号
     */
	function save()
	{
    	if ($this->is_post())
        {
            if ($id = $this->edition->save($_POST))
            {
                $result = array('state'=>true, 'data'=>$this->edition->getEdition($id));
            }
            else
            {
                $result = array('state'=>false, 'error'=>$this->edition->error());
            }
            echo $this->json->encode($result);
        }
        else
        {
        	$magazines = $this->magazine->select(null, 'mid, name');
			$this->view->assign('magazines', $magazines);
            $edition = $this->edition->getEdition(intval($_GET['eid']));
            
            $mid = $_GET['mid'] ? $_GET['mid'] : $edition['mid'];
           	$this->view->assign('mid', $mid);
           	
            $last = $this->edition->lastE($mid);
            $this->view->assign($last);
        	
        	if(!$edition['eid']) 
            {
            	$mana = $this->magazine->get($mid);
            	$edition['publish'] = $mana['default_year'].'-01-01';
            	$edition['number'] = is_numeric($last['lastN']) ? $last['lastN'] + 1 : $last['lastN'];
        		$edition['total_number'] = is_numeric($last['lastTN']) ? $last['lastTN'] + 1 : $last['lastTN'];
            }
            $this->view->assign($edition);
            $this->view->display('edition/form');
        }
    }

    /**
     * 删除期号
     *
     * @aca 删除期号
     * @return mixed
     */
	function delete()
	{
		$id = $_GET['id'];
		if(!$id) return ;
		$result = $this->edition->delete($id);
		$result = $result ? array('state'=>true) : array('state'=>false, 'error'=>'删除失败');
		exit($this->json->encode($result));
	}
	
	/**
     * 批量修改状态
     *
     * @aca 批量修改状态
     * @return mixed
     */
	function disabled()
	{
		$id = $_GET['id'];
		if(!$id || !isset($_GET['value'])) return ;
		$v = intval($_GET['value']);
		if($v == 2)
		{
			$html = loader::model('admin/html');
			$ids = explode(',', $id);
			foreach ($ids as $eid)
			{
				if(!intval($eid)) continue;
				$html->delEdition($eid);
			}
		}
		exit($this->json->encode(array('state'=>true)));
	}
}
?>