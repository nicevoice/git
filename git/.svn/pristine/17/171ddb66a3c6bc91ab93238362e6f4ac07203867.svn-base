<?php
/**
 * 投稿管理
 *
 * @aca 投稿管理
 */
class controller_admin_index extends contribution_controller_abstract
{
	public $pagesize = 15;
	function __construct(& $app)
	{
		parent::__construct($app);
		$this->contribution = loader::model('contribution');
	}

    /**
     * 浏览
     *
     * @aca 浏览
     */
	function index()
	{
		$status = isset($_GET['status']) ? intval($_GET['status']) : 3;
		$catid = isset($_GET['catid']) ? intval($_GET['catid']) : 0;
		$date = isset($_GET['date']) ? $_GET['date'] : 'none';
		$keywords = isset($_GET['keywords']) ? $_GET['keywords'] : '';
		
		$this->view->assign('status', $status);
		$this->view->assign('catid', $catid);
		$this->view->assign('date', $date);
		$this->view->assign('keywords', $keywords);
		$head['title'] = '稿件管理';
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
		$_GET['catid'] = intval($_GET['catid']);
		$_GET['status'] = isset($_GET['status']) ? intval($_GET['status']) : 3;
		$page = isset($_GET['page']) ? intval($_GET['page']) : 0;
		$pagesize = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : $this->pagesize;
		
		$fields = '*';
		$order = isset($_GET['orderby']) ? str_replace('|', ' ', $_GET['orderby']) : '`created` DESC';

		if($_GET['status'] == 6)
		{
			$data = $this->contribution->get_publish($_GET, $fields, $order, $page, $pagesize);
		}
		else
		{
			$data = $this->contribution->ls($_GET, $fields, $order, $page, $pagesize);
		}
		$total = $this->contribution->get_total();
		$count = $this->contribution->get_count();
		$result = array('total' => $total, 'data' => $data, 'count' => $count);
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
			$this->article = loader::model('admin/article','article');
			if ($contentid = $this->article->add($_POST))
			{
				$result = array('state'=>true, 'contentid' => $contentid);
				$article = $this->article->get($contentid, 'url, status');
				if($article['status'] == 6)
				{
					$result['url'] = $article['url'];
				}
				$this->contribution->publish($_POST['contributionid'],$contentid,$article['status']);
			}
			else
			{
				$result = array('state'=>false, 'error'=>$this->article->error());
				if($this->article->filterword)
				{
					$result['filterword'] = $this->article->filterword;
				}
			}
			echo $this->json->encode($result);
		}
		else
		{
			$contributionid = $_GET['contributionid'];
			$data = $this->contribution->get($contributionid);
			$data['source'] = !empty($data['source']) ? $data['source'] : $data['sourcename'];
			$data['status'] = 6;
			$data['weight'] = setting('article','weight');
			$data['source'] = !empty($data['source'])?$data['source']:setting('article','source');
			$data['editor'] = $this->_username;
			$data['allowcomment'] = 1;
			$data['saveremoteimage'] = 1;
			$data['modelid'] = 1;
			
			$this->view->assign($data);
			$head['title'] = '发布稿件';
			$this->view->assign('head', $head);
			$this->view->display('form');
		}
	}

    /**
     * 退稿
     *
     * @aca 退稿
     */
	function reject()
	{
		$ids = $_POST['contributionid'];
		if(!empty($ids))
		{
			$ids = explode(',',$ids);
			foreach($ids as $id)
			{
				$this->contribution->reject($id);
			}
			$result = array('state' => true,'message' => '退稿成功');
		}
		else
		{
			$result = array('state' => false,'message' => '稿件ID不能为空');
		}
		echo $this->json->encode($result);
	}

    /**
     * 彻底删除
     *
     * @aca 彻底删除
     */
	function remove()
	{
		$ids = $_POST['contributionid'];
		if(!empty($ids))
		{
			$ids = explode(',',$ids);
			foreach($ids as $id)
			{
				$this->contribution->remove($id);
			}
			$result = array('state' => true,'message' => '退稿成功');
		}
		else
		{
			$result = array('state' => false,'message' => '稿件ID不能为空');
		}
		echo $this->json->encode($result);
	}

    /**
     * 删除
     *
     * @aca 删除
     */
	function delete()
	{
		$ids = $_POST['contributionid'];
		if(!empty($ids))
		{
			$ids = explode(',',$ids);
			foreach($ids as $id)
			{
				$this->contribution->del($id);
			}
			$result = array('state' => true,'message' => '退稿成功');
		}
		else
		{
			$result = array('state' => false,'message' => '稿件ID不能为空');
		}
		echo $this->json->encode($result);
	}

    /**
     * 清空回收站
     *
     * @aca 清空回收站
     */
	function clear()
	{
		$this->contribution->delete("`status`='0'");
		$result = array('state' => true,'message' => '清空回收站成功');
		echo $this->json->encode($result);
	}

    /**
     * 查看
     *
     * @aca 浏览
     */
	function view()
	{
		$r = $this->contribution->get($_GET['contributionid'], '*');
		if (!$r) $this->showmessage($this->contribution->error());
		
		$this->priv_category($r['catid']);
		
		$this->view->assign($r);
		$this->view->assign('head', array('title'=>$r['title'].'_稿件管理'));
		$this->view->display('view');
	}
}