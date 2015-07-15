<?php
/**
 * 期号管理
 *
 * @aca 期号管理
 */
final class controller_admin_edition extends paper_controller_abstract
{
	private $edition,$paper,$page,$pagesize = 15;
	private $disabled = array('未发布', '已发布', '休眠');
	function __construct(& $app)
	{
		parent::__construct($app);
		if (!license('paper')) cmstop::licenseFailure();
		$this->paper = loader::model('admin/paper');
		$this->edition = loader::model('admin/edition');
		$cookie = factory::cookie();
		$size = $cookie->get('editionsPageSize');
		intval($size) && $this->pagesize = $size;
	}

    /**
     * 期号管理
     *
     * @aca 浏览
     */
	function index()
	{
		$papers = $this->paper->select(null, 'paperid, name');
		$paper = table('paper', intval($_GET['id']));
		$this->view->assign('papers', $papers);
		$this->view->assign('edits', $this->edition->edits());
		$this->view->assign('size', $this->pagesize);
		$this->view->assign('head', array('title'=>$paper['name']));
		$this->view->assign('disabledMap', $this->disabled);
		$this->view->display("edition/index");
	}

    /**
     * 期号列表
     *
     * @aca 浏览
     */
	function page()
	{
		if($_GET['orderby'] && !preg_match('/^[\w\|\.]+$/i', $_GET['orderby'])) exit;
		$order = $_GET['orderby'] ? str_replace('|', ' ', $_GET['orderby']) : 'editionid DESC';
		
		$page = max((isset($_GET['page']) ? intval($_GET['page']) : 1), 1);
		$size = max((isset($_GET['pagesize']) ? intval($_GET['pagesize']) : $this->pagesize), 1);
		
		$where = $this->edition->createWhere($_REQUEST);
		
		$data = $this->edition->page($where, $order, $page, $size);
		echo $this->json->encode($data);
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
            	$id || $id = intval($_POST['editionid']);
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
        	$papers = $this->paper->select(null, 'paperid, name');
			$this->view->assign('papers', $papers);
            $edition = $this->edition->get(intval($_GET['editionid']));
            
            $paperid = $_GET['paperid'] ? $_GET['paperid'] : $edition['paperid'];
            $last = $this->edition->lastE($paperid);
            $this->view->assign($last);
        	$this->view->assign('pid', $paperid);
        	if(!$edition)
        	{
        		$edition['number'] = is_numeric($last['lastN']) ? $last['lastN'] + 1 : $last['lastN'];
        		$edition['total_number'] = is_numeric($last['lastTN']) ? $last['lastTN'] + 1 : $last['lastTN'];
        		$edition['date'] = TIME;
        	}
            $this->view->assign($edition);
            $this->view->display('edition/form');
        }
    }

    /**
     * 预览时返回头版头条ID
     *
     * @aca 预览
     */
	function prevView()
	{
		$eid = intval($_GET['eid']);
		echo $this->edition->getFirst($eid);
	}

    /**
     * 删除
     *
     * @aca 删除
     * @return mixed
     */
	function delete()
	{
		$id = $_GET['id'];
		if(!$id) return ;
		if($this->edition->delete($id))
		{
			$this->encode(true);
		}
		$this->encode(false, '删除失败!');
	}
	
	/**
     * 批量修改状态
     *
     * @aca 批量修改状态
     * @return mixed
     */
	function disabled()
	{
		$eid = $_GET['id'];
		if(!$eid || !isset($_GET['value'])) return ;
		$v = intval($_GET['value']);
		if($v == 2)
		{
			$html = loader::model('admin/html');
			$ids = explode(',', $eid);
			foreach ($ids as $eid)
			{
				if(!intval($eid)) continue;
				$html->delEdition($eid);
			}
		}
		$this->encode(true);
	}
}