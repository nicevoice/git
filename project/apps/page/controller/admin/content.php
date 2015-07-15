<?php
/**
 * 区块内容
 *
 * @aca 区块内容
 */
class controller_admin_content extends page_controller_abstract
{
    /**
     * 搜索
     *
     * @aca 搜索
     */
	function search()
	{
		$page = intval($_GET['page']);
		if ($page < 1) $page = 1;
		$pagesize = intval($_GET['pagesize']);
		if ($pagesize < 10)
		{
			$pagesize = 20;
		}
		$recommend = loader::model('admin/section_recommend', 'page');
		$data = $recommend->search($_POST, $page, $pagesize);
		$json = array('total'=>$recommend->total, 'data'=>$data);
		exit ($this->json->encode($json));
	}

    /**
     * 推荐
     *
     * @aca 推荐
     */
	function recommend()
	{
		$page = intval($_GET['page']);
		if ($page < 1) $page = 1;
		$pagesize = intval($_GET['pagesize']);
		if ($pagesize < 10)
		{
			$pagesize = 20;
		}
		$sectionid = intval($_POST['sectionid']);
		$recommend = loader::model('admin/section_recommend', 'page');
		$data = $recommend->get_by_section($sectionid, $page, $pagesize);
		$json = array('total'=>$recommend->total, 'data'=>$data);
		exit ($this->json->encode($json));
	}

    /**
     * 删除推荐
     *
     * @aca 删除推荐
     */
	function delrecommend()
	{
		$recommendid = intval($_POST['recommendid']);
		if (!$recommendid) {
			exit ('{"state":false,"error":"无此对象"}');
		}
		$recommend = loader::model('admin/section_recommend', 'page');
		if ($recommend->delete($recommendid))
		{
			exit ('{"state":true}');
		}
		else
		{
			exit ('{"state":false,"error":"删除失败"}');
		}
	}
}