<?php
/**
 * 区块管理
 *
 * @aca 区块管理
 */
class controller_admin_section extends page_controller_abstract
{
	private $section_url = null;
	function __construct(& $app)
	{
		parent::__construct($app);
		if (isset($_REQUEST['sectionid']) && ($sectionid = intval($_REQUEST['sectionid'])) > 0)
		{
			$section = $this->section->get($sectionid);
			if (!$section) $this->_error_out("区块不存在！");
			if (!priv::section($sectionid, $section['pageid']))
			{
				$this->_error_out("您没有<span style='color:red'>".$section['name']."($sectionid)</span>管理权限！");
			}
		}

		$this->section_url = loader::model('admin/section_url', 'page');
	}

	/**
     * 编辑
     *
     * @aca 编辑
     */
	function edit()
	{
	    $sectionid = intval($_REQUEST['sectionid']);

		$section = $this->section->get($sectionid);

		if ($this->is_post())
		{
			// 判断是否被自己锁定
			if ($section['locked'] < TIME)
			{
				exit('{"state":false,"error":"失去锁定，无法保存"}');
			}
			if ($section['lockedby'] != $this->_userid)
			{
				exit('{"state":false,"error":"锁定被'.username($section['lockedby']).'抢占，无法保存"}');
			}
			exit($this->json->encode($this->{'_save'.ucfirst($section['type'])}($section)));
		}
		else
		{
			$logs =  $this->history->get_section($sectionid);
			$this->view->assign('logs', $logs);

			// 判断是否有别人在修改
			$locked = $section['locked'] && $section['locked'] > TIME;
			if ($locked && $section['lockedby'] != $this->_userid)
			{
			    $return = array('state'=>false, 'error'=>'当前区块正在由'.username($section['lockedby']).'编辑中');
			}
			else
			{
			    $this->section->lock($sectionid, 360);// lock 6 min
				$section['data'] = $this->_section_edit_html($section);
				$this->view->assign('section', $section);
				$return = array('state'=>true,'type'=>$section['type'], 'html'=>$this->view->fetch('section/edit/'.$section['type']));
			}

			exit($this->json->encode($return));
		}
	}

    /**
     * 属性
     *
     * @aca 属性
     */
	function property()
	{
	    $sectionid = intval($_REQUEST['sectionid']);
		$section = $this->section->get($sectionid);
		if ($this->is_post())
		{
			if (!$section)
			{
			    exit ($this->json->encode(array('state'=>false,'error'=>'要编辑的碎片不存在')));
			}
			$type = strtolower($section['type']);
			$data = $this->{'_property'.ucfirst($type).'Data'}();
			$template = empty($data['template']) ? $data['data'] : $data['template'];
			if ($type != 'html' && !empty($template))
			{
				if (false !== ($error = $this->_testTemplate($template)))
				{
					return exit ($this->json->encode(array('state'=>false,'error'=>$error)));
				}
			}
			if ($this->section->edit($sectionid, $data))
			{
				$return = array (
				    'state'=>true,
				    'info'=>'碎片属性设置成功'
				);
				if ($data['name'])
				{
					$return['name'] = $data['name'];
				}
			}
			else
			{
				$return = array (
				    'state'=>false,
				    'error'=>$this->section->error()
				);
			}
			exit($this->json->encode($return));
		}
		else
		{
			if (in_array($section['type'],array('hand','feed','rpc','json')))
			{
				$section['data'] = $section['template'];
			}
			else
			{
				$section['data'] = $section['origdata'];
			}
			$this->view->assign('section', $section);
			$this->view->display('section/setting/'.$section['type']);
		}
	}

    /**
     * 可视化区块
     *
     * @aca 可视化区块
     */
	function visual()
	{
	    $sectionid = intval($_REQUEST['sectionid']);
		$section = $this->section->get($sectionid);

		if ($this->is_post())
		{
			if ($_POST['do']=='preview')
			{
				$html = $this->_section_visual_html($section);
				$return = array(
					'state'=>true,
					'html'=>$html
				);
				exit($this->json->encode($return));
			}

			// 判断是否被自己锁定
			if ($section['locked'] < TIME)
			{
				exit('{"state":false,"error":"失去锁定，无法保存"}');
			}
			if ($section['lockedby'] != $this->_userid)
			{
				exit('{"state":false,"error":"锁定被'.username($section['lockedby']).'抢占，无法保存"}');
			}

			$return = $this->{'_save'.ucfirst($section['type'])}($section);
			if ($return['state'])
			{
			    $newsection = $this->section->get($sectionid);
			    $return['html'] = $this->_section_view_html($newsection);
			}
			exit($this->json->encode($return));
		}

		// not post
		$locked = $section['locked'] && $section['locked'] > TIME;
		if ($locked && $section['lockedby'] != $this->_userid)
		{
		    $return = array('state'=>false, 'error'=>'当前区块正在由'.username($section['lockedby']).'编辑中');
		    exit($this->json->encode($return));
		}
	    $this->section->lock($sectionid, 360);// lock 6 min
		$section['data'] = $this->_section_edit_html($section);
		$this->view->assign('section', $section);
		$return = array('state'=>true,'type'=>$section['type'], 'html'=>$this->view->fetch('section/visual/'.$section['type']));
		exit($this->json->encode($return));
	}

    /**
     * 预览
     *
     * @aca 预览
     */
	function preview()
	{
		$pageid = intval($_GET['pageid']);
		$sectionid = intval($_GET['sectionid']);
		if ($this->is_post())
		{
			if (!$pageid || !$sectionid)
			{
			    exit('{"state":false}');
			}
			$state = true;
			if (!empty($_POST['data']))
			{
				$tmp_template = 'section_'.$this->_userid.'_'.$sectionid.'.tpl';
				$tpl = $this->_tmptplfile($tmp_template);
				$state = false !== write_file($tpl, $_POST['data']);
			}
			exit('{"state":'.($state ? 'true' : 'false').'}');
		}
		else
		{
			if (!$pageid || !$sectionid)
			{
			    exit('not exists!!!');
			}
			$page = $this->page->get($pageid);

			$html = $this->template->fetch($page['template']);

			$html = preg_replace('|<!--#include virtual=([\'"])[/]?section/(\d+)\.html\1-->|Uie','$this->_section_preview_wrapper(\2, $sectionid)', $html);
			$html = str_replace('</head>', $this->_section_preview_helper(), $html);
			echo $html;
		}
	}

    /**
     * 锁定
     *
     * @aca 锁定
     */
	function lock()
	{
		$sectionid = intval($_POST['sectionid']);
		if (!$sectionid || !($section = $this->section->get($sectionid)))
		{
			exit('{"state":false,"error":"当前处于无效编辑"}');
		}
		if (!$section['lockedby'])
		{
			exit('{"state":false,"error":"当前处于无效编辑"}');
		}
		if ($section['lockedby'] != $this->_userid)
		{
			exit('{"state":false,"error":"锁定被'.username($section['lockedby']).'抢占"}');
		}
		$this->section->lock($sectionid, 360);
	}

    /**
     * 强行解锁
     *
     * @aca 强行解锁
     */
	function unlock()
	{
		// 强行解锁
		$sectionid = intval($_POST['sectionid']);
		$return = array('state'=>false);
		if ($sectionid && $this->section->unlock($sectionid))
		{
			$return = array('state'=>true);
		}
		exit ($this->json->encode($return));
	}

    /**
     * 解自己的锁
     *
     * @aca 解自己的锁
     */
	function unsave()
	{
		// 解自己的锁
		$sectionid = intval($_POST['sectionid']);
		$section = $this->section->get($sectionid);
		// 判断是否有别人在修改
		if ($section['lockedby'] == $this->_userid)
		{
			// 尝试解锁
			$this->section->unlock($sectionid);
		}
		if ($section['type']=='hand')
		{
            cache_delete($this->_handSessionFile($sectionid));
		}
	}

    /**
     * 区块状态
     *
     * @aca 区块状态
     * @return mixed
     */
	function section_state()
	{
		$pageid = intval($_GET['pageid']);

		if (!$pageid)
		{
		    return;
		}
		$this->section->unlock_sections($pageid);

		$sections = $this->section->get_section($pageid);
		if ($sections)
		{
			$arr = array();
			foreach ($sections as $value)
			{
				$title = $value['locked'] ? username($value['lockedby']).' 编辑中' : '可编辑';
				$arr[] = array('sectionid'=>$value['sectionid'], 'locked'=>$value['locked'] ? true : false, 'title'=>$title);
			}
			$return = array('state'=>true, 'sections'=>$arr);
		}
		else
		{
			$return = array('state'=>false);
		}
		exit($this->json->encode($return));
	}

    /**
     * 删除行
     *
     * @aca 删除行
     */
	function delrow()
	{
		$sectionid = intval($_POST['sectionid']);
        $cachefile = $this->_handSessionFile($sectionid);
        if(!is_array($data = cache_read($cachefile)))
		{
			exit('{"state":false,"error":"读取缓存失败，请关闭标签页重新打开。"}');
		}

		$delrow = $_POST['row'];
		$return = array();
		if ($delrow < count($data)) {
			array_splice($data, $delrow, 1);
			if (cache_write($cachefile, $data))
			{
				$return['state'] = true;
			} else {
				$return['state'] = false;
			}
		} else {
			$return['state'] = false;
		}
		exit($this->json->encode($return));
	}

    /**
     * 上移行
     *
     * @aca 顺序调整
     */
	function uprow()
	{
		$row = intval($_POST['row']);
		$num = intval($_POST['num']);	// 新增一个多步上移的参数, 拖动排序中调用
		$sectionid = intval($_POST['sectionid']);
		$num < 1 && ($num = 1);
        $cachefile = $this->_handSessionFile($sectionid);
        if (!is_array($data = cache_read($cachefile)))
		{
			exit('{"state":false,"error":"读取缓存失败，请关闭标签页重新打开。"}');
		}

		$return = array();
		if ($row != 0) {
			for ($i = 0; $i < $num; $i++)
			{
				$tmp	= $data[$row - $i];
				$data[$row - $i] = $data[$row - $i - 1];
				$data[$row - $i - 1] = $tmp;
			}
			if (cache_write($cachefile, $data))
			{
				$return['state'] = true;
			} else {
				$return['state'] = false;
			}
		} else {
			$return['state'] = false;
		}
		exit($this->json->encode($return));
	}

    /**
     * 下移行
     *
     * @aca 顺序调整
     */
	function downrow()
	{
		$row = intval($_POST['row']);
		$num = intval($_POST['num']);	// 新增一个多步下移的参数, 拖动排序中调用
		$sectionid = intval($_POST['sectionid']);
		$num < 1 && ($num = 1);
        $cachefile = $this->_handSessionFile($sectionid);
        if (!is_array($data = cache_read($cachefile)))
		{
			exit('{"state":false,"error":"读取缓存失败，请关闭标签页重新打开。"}');
		}
		$return = array();
		if ($row < count($data) - 1) {
			for ($i = 0; $i < $num; $i++)
			{
				$tmp	= $data[$row + $i];
				$data[$row + $i] = $data[$row + $i + 1];
				$data[$row + $i + 1] = $tmp;
			}
			if (cache_write($cachefile, $data))
			{
				$return['state'] = true;
			} else {
				$return['state'] = false;
			}
		} else {
			$return['state'] = false;
		}
		exit($this->json->encode($return));
	}

    /**
     * 添加行
     *
     * @aca 添加行
     */
	function addrow()
	{
		$sectionid = intval($_POST['sectionid']);
		$cachefile = $this->_handSessionFile($sectionid);
		if (!is_array($data = cache_read($cachefile)))
		{
			exit('{"state":false,"error":"读取缓存失败，请关闭标签页重新打开。"}');
		}
		array_splice($data, intval($_POST['pos']), 0, array(array()));
		$return = array();
		if (cache_write($cachefile, $data))
		{
			$return['state'] = true;
		} else {
			$return['state'] = false;
		}
		exit($this->json->encode($return));
	}

    /**
     * 添加项
     *
     * @aca 添加项
     */
	function additem()
	{
		$sectionid = intval($_REQUEST['sectionid']);
		$row = intval($_REQUEST['row']);
		$cachefile = $this->_handSessionFile($sectionid);
		if ($this->is_post()) {
			if (!is_array($data = cache_read($cachefile)))
			{
				exit('{"state":false,"error":"读取缓存失败，请关闭标签页重新打开。"}');
			}
            if (empty($_POST['title']))
            {
                exit('{"state":false,"error":"标题不能为空"}');
            }
			$newitem = array(
				'contentid' => $_POST['contentid'],
				'title' => $_POST['title'],
				'url' => $_POST['url'],
				'color' => $_POST['color'],
				'subtitle' => $_POST['subtitle'],
				'suburl' => $_POST['suburl'],
				'thumb' => $_POST['thumb'],
				'description' => $_POST['description'],
				'time' => strtotime($_POST['time'])
			);

			// 如果contentid为空则从url中分析contentid
			if (empty($newitem['contentid']))
			{
				$newitem['contentid'] = loader::model('admin/search', 'system')->url2contentid($newitem['url']);
			}
			$col = count($data[$row]);
			$data[$row][$col] = $newitem;
			if (cache_write($cachefile, $data))
			{
				$newitem['tips'] = $this->_buildHandItemTips($newitem);
				$newitem['col'] = $col;
			    $return = array(
			        'state' => true,
			        'data' => $newitem
			    );
			} else {
			    $return = array('state'=>false);
			}
			exit ($this->json->encode($return));
		} else {
			$this->view->assign('sectionid', $sectionid);
			$this->view->assign('row', $row);
			$this->view->display('section/edit/hand_item');
		}
	}

    /**
     * 删除项
     *
     * @aca 删除项
     */
	function delitem()
	{
		$sectionid = intval($_POST['sectionid']);
		$row = intval($_POST['row']);
		$col = intval($_POST['col']);
		$cachefile = $this->_handSessionFile($sectionid);
		if (!is_array($data = cache_read($cachefile)))
		{
			exit('{"state":false,"error":"读取缓存失败，请关闭标签页重新打开。"}');
		}
		$return = array();
		if ($data[$row][$col] != null)
		{
		    array_splice($data[$row], $col, 1);
			if (cache_write($cachefile, $data))
			{
				$return['state'] = true;
			} else {
				$return['state'] = false;
			}
		} else {
			$return['state'] = false;
		}
		exit ($this->json->encode($return));
	}

    /**
     * 编辑项
     *
     * @aca 编辑项
     */
	function edititem()
	{
		$sectionid = intval($_REQUEST['sectionid']);
		$row = intval($_REQUEST['row']);
		$col = intval($_REQUEST['col']);
		$cachefile = $this->_handSessionFile($sectionid);
		if (!is_array($data = cache_read($cachefile)))
		{
			exit('{"state":false,"error":"读取缓存失败，请关闭标签页重新打开。"}');
		}
		if (!$this->is_post())
		{
			$this->view->assign('item', $data[$row][$col]);
			$this->view->assign('action', 'edititem');
			$this->view->display('section/edit/hand_item_e');
		} else {
		    $return = array();
			if ($data[$row][$col]) {
				$item = array(
					'contentid' => $_POST['contentid'],
				    'title' => $_POST['title'],
				    'color' => $_POST['color'],
				    'url' => $_POST['url'],
				    'subtitle' => $_POST['subtitle'],
				    'suburl' => $_POST['suburl'],
				    'thumb' => $_POST['thumb'],
				    'description' => $_POST['description'],
				    'time' => strtotime($_POST['time'])
				);
				// 如果contentid为空则从url中分析contentid
				if (empty($item['contentid']))
				{
					$item['contentid'] = loader::model('admin/search', 'system')->url2contentid($item['url']);
				}
				$data[$row][$col] = $item;
				if (cache_write($cachefile, $data))
				{
					$item['tips'] = $this->_buildHandItemTips($item);
				    exit ($this->json->encode(array(
						'data'=>$item,
						'state'=>true
					)));
				} else {
					exit('{"state":false}');
				}
			}

			exit('{"state":false}');
		}
	}

    /**
     * 替换项
     *
     * @aca 替换项
     */
	function replaceitem()
	{
	    $sectionid = intval($_REQUEST['sectionid']);
		$row = intval($_REQUEST['row']);
		$col = intval($_REQUEST['col']);
		$cachefile = $this->_handSessionFile($sectionid);
	    if (!is_array($data = cache_read($cachefile)))
		{
			exit('{"state":false,"error":"读取缓存失败，请关闭标签页重新打开。"}');
		}
		if ($this->is_post())
	    {
	        $return = null;
	        if ($data[$row][$col]) {
				$item = array(
					'contentid' => $_POST['contentid'],
				    'title' => $_POST['title'],
				    'url' => $_POST['url'],
				    'color' => $_POST['color'],
				    'subtitle' => $_POST['subtitle'],
				    'suburl' => $_POST['suburl'],
				    'thumb' => $_POST['thumb'],
				    'description' => $_POST['description'],
				    'time' => strtotime($_POST['time'])
				);
				$data[$row][$col] = $item;
				if (cache_write($cachefile, $data))
				{
					$item['tips'] = $this->_buildHandItemTips($item);
				    $return = array(
				        'data' => $item,
				        'state' => true
				    );
				} else {
					$return = array('state'=>false);
				}
			} else {
				$return = array('state'=>false);
			}
			exit ($this->json->encode($return));
	    }
	    else
	    {
	        $this->view->assign('sectionid', $sectionid);
			$this->view->assign('row', $row);
			$this->view->assign('col', $col);
			$this->view->assign('item',$data[$row][$col]);
			$this->view->display('section/edit/hand_item_r');
	    }
	}

    /**
     * 左移项
     *
     * @aca 左右移动标题
     */
	function leftitem()
	{
		$sectionid = intval($_POST['sectionid']);
		$row = intval($_POST['row']);
		$col = intval($_POST['col']);
		$cachefile = $this->_handSessionFile($sectionid);
		if (!is_array($data = cache_read($cachefile)))
		{
			exit('{"state":false,"error":"读取缓存失败，请关闭标签页重新打开。"}');
		}

		$return = array();
		if ($col <= 0) {
			$return['state'] = false;
		} else {
			$tmp = $data[$row][$col-1];
			$data[$row][$col-1] = $data[$row][$col];
			$data[$row][$col] = $tmp;
			if (cache_write($cachefile, $data))
			{
				$return['state'] = true;
			} else {
				$return['state'] = false;
			}
		}
		exit ($this->json->encode($return));
	}

    /**
     * 右移项
     *
     * @aca 左右移动标题
     */
	function rightitem()
	{
		$sectionid = intval($_POST['sectionid']);
		$row = intval($_POST['row']);
		$col = intval($_POST['col']);
		$cachefile = $this->_handSessionFile($sectionid);
		if (!is_array($data = cache_read($cachefile)))
		{
			exit('{"state":false,"error":"读取缓存失败，请关闭标签页重新打开。"}');
		}
		$return = array();
		if ($col >= count($data[$row][$col])-1)
		{
			$return['state'] = false;
		} else {
			$tmp = $data[$row][$col+1];
			$data[$row][$col+1] = $data[$row][$col];
			$data[$row][$col] = $tmp;
			if (cache_write($cachefile, $data))
			{
				$return['state'] = true;
			} else {
				$return['state'] = false;
			}
		}
		exit ($this->json->encode($return));
	}

    /**
     * 历史记录
     *
     * @aca 历史记录
     */
	function logpack()
	{
		$where = null;
		if ($_GET['d'] && preg_match('/(\d{4})\-(\d{1,2})\-(\d{1,2})/',$_GET['d'],$match))
		{
			list(,$year,$month,$day) = $match;

			// 重构时间 防止不合法时间输入 比如 2009-13-33
			$date = date('Y,w,n,j', mktime(0,0,0,$month,$day,$year));
			// 重新获得一些变量值
			list($year,$week,$month,$day) = explode(',', $date);
			$from = mktime(0,0,0,$month, $day, $year);
			$to = mktime(0,0,0,$month, $day+1, $year);
			$where = "(created>=$from AND created<$to)";
		}
		
		$sectionid = intval($_GET['sectionid']);
		$logs =  $this->history->get_section($sectionid, $where);
		$this->view->assign('section', $this->section->get($sectionid));
		$this->view->assign('logs', $logs);
		$this->view->display('section/log');
	}

    /**
     * 预览历史记录
     *
     * @aca 预览历史记录
     */
	function viewlog(){
		$logid = intval($_GET['logid']);
		$log_info = $this->history->get($logid);
		$this->view->assign('loginfo', $log_info);
		$section = $this->section->get($log_info['sectionid']);
		$section['data'] = $log_info['data'];
		$html = $this->_section_view_html($section);

		$this->view->assign('html', $html);

		$this->view->display('section/viewlog');
	}

    /**
     * 恢复到原始数据
     *
     * @aca 恢复到原始数据
     */
	function restorelog()
	{
		if ($_POST['logid']=='orig')
		{
			$sectionid = intval($_POST['sectionid']);
			$section = $this->section->get($sectionid);
			if ($section && $section['origdata'])
			{
				$this->history->add(array('sectionid'=>$sectionid, 'data'=>$section['data']));
				if ($this->section->edit($sectionid, array('data'=>$section['origdata'])))
				{
					$this->log->add(array('action'=>'edit', 'sectionid'=>$sectionid));
					$return = array('state'=>true, 'info'=>"数据已恢复到原始版本");
				}
				else
				{
					$return = array('state'=>false, 'error'=>'数据恢复失败'.$this->section->error());
				}
			}
			else
			{
				$return = array('state'=>false, 'error'=>'原始数据为空');
			}
		}
		else
		{
			$logid = intval($_POST['logid']);
			$log = $this->history->get($logid);
			if ($log)
			{
				if ($log['data'])
				{
					$sectionid = $log['sectionid'];
					$section = $this->section->get($sectionid);
					$this->history->add(array('sectionid'=>$sectionid, 'data'=>$section['data']));
					if ($this->section->edit($sectionid, array('data'=>$log['data'])))
					{
						$this->log->add(array('action'=>'edit', 'sectionid'=>$sectionid));
						$return = array('state'=>true, 'info'=>"数据已恢复到".date('Y-m-d H:i:s', $log['created']). "的版本");
					}
					else
					{
						$return = array('state'=>false, 'error'=>'数据恢复失败'.$this->section->error());
					}
				}
				else
				{
					$return = array('state'=>false, 'error'=>'要恢复的历史记录为空');
				}
			}
			else
			{
				$return = array('state'=>false, 'error'=>'历史记录读取失败');
			}
		}
		exit($this->json->encode($return));
	}

    /**
     * 清空历史记录
     *
     * @aca 清空历史记录
     */
	function clearlog()
	{
		if ($sectionid = intval($_GET['sectionid']))
		{
			$this->history->delete("sectionid=$sectionid");
		}
		exit ('{"state":true}');
	}

    /**
     * 历史记录
     *
     * @aca 历史记录
     */
	function getlog()
	{
		$logid = intval($_GET['logid']);
		$return = array('state'=>false);
		if ($log = $this->history->get($logid))
		{
			$section = $this->section->get($log['sectionid']);
			if ($section['type'] == 'html' || $section['type'] == 'auto')
			{
				$section['data'] = $log['data'];
				$data = $this->_section_edit_html($section);
			    $return = array('state'=>true, 'data'=>$data);
			}
		}
		exit($this->json->encode($return)) ;
	}
	
	/**
     * 添加
     *
     * @aca 添加
     */
	function add()
	{
		$pageid = intval($_REQUEST['pageid']);
		if (!$pageid || !($page = $this->page->get($pageid)))
		{
			$this->_error_out("页面不存在！");
		}
		if (!priv::page($pageid))
		{
			$this->_error_out("您没有<span style='color:red'>".$page['name']."($pageid)</span>管理权限！");
		}
		$type = empty($_REQUEST['type']) ? 'html' : strtolower($_REQUEST['type']);
		if ($this->is_post())
		{
		    $data = $this->{'_add'.ucfirst($type).'Data'}($_POST);
		    if (is_array($data))
		    {
		    	if ($id = $this->section->add($data))
				{
					$data = $this->section->get($id);
					try {
						$this->{'_publish'.ucfirst($type)}($data);
					} catch (Exception $e) {}
					$this->log->add(array('action'=>'add', 'sectionid'=>$data['sectionid']));
					$data['locked'] = false;
					$return = array(
					    'state'=>true,
					    'data'=>$data
					);
				}
				else
				{
				    $return = array(
					    'state'=>false,
					    'error'=>$this->section->error()
					);
				}
			}
			else
			{
				$return = array(
				    'state'=>false,
				    'error'=>$data
				);
			}
			exit($this->json->encode($return));
		}
		else
		{
		    $this->view->assign('pageid', $pageid);
		    $this->view->assign('data', htmlspecialchars($this->_getSampleCode($type)));
		    $this->view->display('section/add/'.$type);
		}
	}
	
	/**
     * 编辑发布、添加发布
     *
     * @aca 发布
     */
	function grap(){
		$this->publish();
	}

    /**
     * 发布
     *
     * @aca 发布
     */
	function publish()
	{
	    $sectionid = intval($_POST['sectionid']);
	    $section = $this->section->get($sectionid);
	    $type = strtolower($section['type']);
	    exit($this->json->encode($this->{'_publish'.ucfirst($type)}($section)));
	}
	
	/**
     * 删除
     *
     * @aca 删除
     */
	function delete()
	{
		$sectionid = intval($_POST['sectionid']);
		if (!$sectionid || !($section = $this->section->get($sectionid)))
		{
			exit('{"state":false,"error":"未知删除"}');
		}
		$locked = $section['locked'] && $section['locked'] > TIME;
		if ($locked && $section['lockedby'] != $this->_userid)
		{
		    exit('{"state":false,"error":"当前区块正在由'.username($section['lockedby']).'编辑中"}');
		}
		if ($this->section->delete($sectionid))
		{
			$return = array(
		        'state'=>true
		    );
			$this->log->add(array('action'=>'delete', 'sectionid'=>$sectionid));
		}
		else
		{
		    $return = array(
		        'state'=>false,
		        'error'=>$this->section->error()
		    );
		}
		exit($this->json->encode($return));
	}

	/**
     * 浏览
     *
     * @aca 查看
     */
	function view()
	{
		$sectionid = intval($_REQUEST['sectionid']);
		$section = $this->section->get($sectionid);
		$this->view->assign('section', $section);

		if (isset($_GET['foreditor']))
		{
            $this->view->assign('html', $this->_section_view_html($section));
			$json['data']	= $this->view->fetch('section/editor_view');
		}
		else
		{
            $lockedid = 0;
            if ($section['locked'] && $section['locked'] > TIME)
            {
                $lockedid = $section['lockedby'];
            }
            $type = strtolower($section['type']);

            $this->view->assign('lockedid', $lockedid);
			$json['data']	= $this->view->fetch('section/view/'.$type);
		}
		$json['state']	= true;
		$json['pageid']	= $section['pageid'];
		echo $this->json->encode($json);
	}

    /**
     * 获取区块查看的 HTML
     *
     * @aca 查看
     */
	function loadViewHtml()
	{
		$sectionid = intval($_REQUEST['sectionid']);
		$section = $this->section->get($sectionid);
		$content = $this->_section_view_html($section);
		$content = preg_replace('/<(script|style)[^>]*>.*<\/\1>/Uis', '', $content);
		exit($content);
	}

	/**
     * 搜索区块
     *
     * @aca public 搜索区块
     */
	function search()
	{
		if ($this->is_post())
		{
			$keywords = trim($_POST['keywords']);
			$pageid = intval($_POST['pageid']);
			$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
			$pagesize = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : 20;

			$data = $this->section->search($pageid, $keywords, $page, $pagesize);
			$result = $page == 1 ? array('state'=>true, 'data'=>$data, 'total'=>$this->section->search_total($pageid, $keywords)) : array('state'=>true, 'data'=>$data);
			exit ($this->json->encode($result));
		}
		else
		{
			$keywords = isset($_GET['keywords']) ? $_GET['keywords'] : null;
			$this->view->assign('keywords', $keywords);
			$this->view->assign('pageid',intval($_REQUEST['pageid']));
			$this->view->display("section/search");
		}
	}

    /**
     * 搜索区块
     *
     * @aca public 搜索区块
     */
	function searchall()
	{
		$pageid = intval($_GET['pageid']);
		$keyword = $_REQUEST['keyword'];
		if (trim($keyword) == '') {
			$keyword = '';
		} else {
			$keyword = str_replace('_', '\_', addcslashes($keyword, '%_'));
		}
		$where = "`pageid`=$pageid";
    	if ($keyword)
    	{
    		$where .= " AND `name` LIKE '%$keyword%'";
    	}
    	$db = factory::db();
    	$sql = "SELECT `sectionid`, `name` as `text`, `pageid`, `type`
    		FROM #table_section WHERE $where
    		ORDER BY `sectionid` ASC";
    	if ($rowset = $db->select($sql))
        {
        	exit ($this->json->encode($rowset));
        }
        else
        {
        	exit ('[]');
        }
	}
	
	/**
     * 移动
     *
     * @aca 移动
     */
	function move()
	{
		$sectionid = intval($_REQUEST['sectionid']);
		if (!$sectionid || !($section = $this->section->get($sectionid)))
		{
			$this->_error_out('不存在此区块');
		}
		if ($this->is_post())
		{
			$pageid = intval($_REQUEST['pageid']);
			if (!$pageid || $pageid == $section['pageid']
			   || !$this->page->get($pageid)) 
			{
				exit ('{"state":false,"error":"无效移动"}');
			}
			if ($this->section->move($sectionid,array('pageid'=>$pageid)))
			{
				exit ('{"state":true,"info":"移动成功"}');
			}
			else
			{
				exit ('{"state":false,"error":"移动失败"}');
			}
		}
		else
		{
			$this->view->assign('section', $section);
			$this->view->display('section/move');
		}
	}

	protected function _section_edit_html($section)
	{
		switch ($section['type'])
		{
			case 'hand':
				$data = $this->_string_to_array($section['data']);
				if (!is_array($data)) {
					$data = array();
				}
			    cache_write($this->_handSessionFile($section['sectionid']), $data);
			    foreach ($data as &$r)
			    {
			    	foreach ($r as &$c)
			    	{
			    		$c['tips'] = htmlspecialchars($this->_buildhandItemTips($c));
			    	}
			    }
			    return $data;
			case 'auto': case 'html': default:
				return $section['data'];
		}
	}

	protected function _section_visual_html($section)
	{
		if ($section['type']=='html')
		{
			return $_POST['data'];
		}
		switch ($section['type'])
		{
			case 'auto':
				$tpldata = $_POST['data'];
				$data = null;
				break;
			case 'hand':
				$data = cache_read($this->_handSessionFile($section['sectionid']));
				$tpldata = $section['template'];
				break;
			case 'feed': case 'rpc': case 'json':
				$data = $this->_string_to_array($section['data']);
				$tpldata = $section['template'];
				break;
			default:
				return '';
		}
		$orig_dir = $this->template->dir;
        $this->template->set_dir(CACHE_PATH);
		$tmp_template = 'section_'.$this->_userid.'_'.$section['sectionid'].'.tpl';
		$tpl = $this->_tmptplfile($tmp_template);
		write_file($tpl, $tpldata);
		$this->template->assign('data', $data);
		$data = $this->template->fetch($tmp_template);
		@unlink($tpl);
		$this->template->set_dir($orig_dir);
		return $data;
	}
	protected function _getSampleCode($type)
	{
		switch ($type)
		{
			case 'hand': return '<ul>
<!--{loop $data $k $r}-->
  <li><!--{loop $r $i $c}-->
    <a href="{$c[url]}">{$c[title]}<img src="{$c[thumb]}" /></a>
    <span>{date("Y-m-d", $c[time])}</span>
    <!--{/loop}-->
  </li>
<!--{/loop}-->
</ul>';
			case 'auto': return '<ul>
<!--{content modelid="1" catid="1" weight="60," orderby="published desc" size="10"}-->
  <li><a href="{$r[url]}">{$r[title]}</a></li>
<!--{/content}-->
</ul>
';
			case 'feed':case 'json':case 'rpc': return '<ul>
{loop $data $k $r}
  <li><a href="{$r[link]}">{$r[title]}</a></li>
{/loop}
</ul>';
			default: return '';
		}
	}
	protected function _section_preview_wrapper($sectionid,$id)
	{
		$section =  $this->section->get($sectionid);
	    if ($sectionid != $id)
	    {
	    	return $this->_section_view_html($section);
	    }
	    return '<a name="'.$id.'"></a><div class="section preview" title="'.$section['name'].'（预览）" id="'.$sectionid.'">'.$this->_section_preview_html($section).'</div>';
	}
	protected function _section_preview_html($section)
	{
		$orig_dir = $this->template->dir;
		$tmp_template = 'section_'.$this->_userid.'_'.$section['sectionid'].'.tpl';
		$tpl = $this->_tmptplfile($tmp_template);
		$this->template->set_dir(CACHE_PATH);
		switch ($section['type'])
		{
			case 'auto':
				$data = $this->template->fetch($tmp_template);
				break;
			case 'html':
				$data = file_get_contents($tpl);
				break;
			case 'hand':
				$data = cache_read($this->_handSessionFile($section['sectionid']));
				write_file($tpl, $section['template']);
				$this->template->assign('data', $data);
				$data = $this->template->fetch($tmp_template);
				break;
			default:
				$data = '';
		}
		@unlink($tpl);
		$this->template->set_dir($orig_dir);
		return $data;
	}
	protected function _saveHtml($section)
	{
	    // data nextupdate
	    $data = array();
		$publish = false;
		if (!empty($_POST['nextupdate'])
			&& ($nextupdate = strtotime(trim($_POST['nextupdate'])))
			&& $nextupdate > TIME)
		{
			$data['nextupdate'] = $nextupdate;
		}
		else
		{
			$data['nextupdate'] = 0;
			$publish = true;
		}
	    $sectionid = $section['sectionid'];
		$data['data'] = $_POST['data'];
	    $info = array();
	    $ok = 1;
	    if ($publish)
	    {
	        $filepath = $this->_sectionPath($sectionid);
            if (false !== write_file($filepath, $data['data']))
			{
				$info[] = '区块发布成功';
			    $data['published'] = TIME;
			    $this->log->add(array('action'=>'publish', 'sectionid'=>$sectionid));
			}
			else
			{
			    $info[] = '区块发布失败';
			    $ok = 0;
			}
	    }
	    if (!$this->history->add(array('sectionid'=>$sectionid, 'data'=>$section['data'])))
		{
		    $info[] = '记录生成失败，未保存本次内容';
		    return array('state'=>false,'error'=>implode('，',$info));
		}
	    if ($this->section->edit($sectionid, $data))
	    {
	        $info[] = '保存成功';
	        $this->log->add(array('action'=>'edit', 'sectionid'=>$sectionid));
	    }
	    else
	    {
	        $info[] = '保存失败';
	        $ok = 0;
	    }
	    $info = implode('，',$info);
	    return $ok ? array('state'=>true,'info'=>$info)
	               : array('state'=>false,'error'=>$info);
	}
	protected function _saveAuto($section)
	{
	    // data nextupdate
	    $data = array();
		$publish = false;
		if (!empty($_POST['nextupdate'])
			&& ($nextupdate = strtotime(trim($_POST['nextupdate'])))
			&& $nextupdate > TIME)
		{
			$data['nextupdate'] = $nextupdate;
		}
		else
		{
			$data['nextupdate'] = 0;
			$publish = true;
		}
	    $sectionid = $section['sectionid'];
	    $data['data'] = $_POST['data'];
	    // check template syntax
		if ($data['data']) {
		    if (false !== ($error = $this->_testTemplate($data['data'])))
			{
				return array('state'=>false,'error'=>$error);
			}
		}
	    $info = array();
	    $ok = 1;
	    if ($publish)
	    {
	    	$orig_dir = $this->template->dir;
        	$this->template->set_dir(CACHE_PATH);
	        $tmp_template = 'section_'.$this->_userid.'_'.$section['sectionid'].'.tpl';
	        $tpl = $this->_tmptplfile($tmp_template);
			write_file($tpl, $data['data']);
			$html = $this->template->fetch($tmp_template);
			@unlink($tpl);
			$this->template->set_dir($orig_dir);
	        $filepath = $this->_sectionPath($sectionid);
            if (false !== write_file($filepath, $html))
			{
				$info[] = '区块发布成功';
			    $data['published'] = TIME;
			    $this->log->add(array('action'=>'publish', 'sectionid'=>$sectionid));
			}
			else
			{
			    $info[] = '区块发布失败';
			    $ok = 0;
			}
	    }
	    if (!$this->history->add(array('sectionid'=>$sectionid, 'data'=>$section['data'])))
		{
		    $info[] = '记录生成失败，未保存本次内容';
		    return array('state'=>false,'error'=>implode('，',$info));
		}
	    if ($this->section->edit($sectionid, $data))
	    {
	        $info[] = '保存成功';
	        $this->log->add(array('action'=>'edit', 'sectionid'=>$sectionid));
	    }
	    else
	    {
	        $info[] = '保存失败';
	        $ok = 0;
	    }
	    $info = implode('，',$info);
	    return $ok ? array('state'=>true,'info'=>$info)
	               : array('state'=>false,'error'=>$info);
	}
	protected function _saveHand($section)
	{
	    // data nextupdate
	    $data = array();
		$publish = false;
		if (!empty($_POST['nextupdate'])
			&& ($nextupdate = strtotime(trim($_POST['nextupdate'])))
			&& $nextupdate > TIME)
		{
			$data['nextupdate'] = $nextupdate;
		}
		else
		{
			$data['nextupdate'] = 0;
			$publish = true;
		}
	    $sectionid = $section['sectionid'];
	    $sessionfile = $this->_handSessionFile($sectionid);
	    if (!($d = cache_read($sessionfile)))
	    {
	        $d = $this->_string_to_array($section['data']);
	    }
	    $info = array();
	    $ok = 1;
	    if ($publish)
	    {
	    	$orig_dir = $this->template->dir;
			$this->template->set_dir(CACHE_PATH);
	    	$tmp_template = 'section_'.$this->_userid.'_'.$sectionid.'.tpl';
	        $tpl = $this->_tmptplfile($tmp_template);
			write_file($tpl, $section['template']);

	        foreach (explode(',', $section['output']) as $type)
	        {
	        	$type = strtolower($type);
	        	$filepath = $this->_sectionPath($sectionid,$type);
	        	if ($type == 'json')
	        	{
	        	    $output = $this->json->encode($d);
	        	}
	        	else
	        	{
	        		$this->template->assign('data', $d);
	        		$output = $this->template->fetch($tmp_template);
	        	}
                if (false !== write_file($filepath, $output))
    			{
    				$info[] = '发布'.$type.'区块成功';
    			    $this->log->add(array(
    			    	'action'=>'publish',
    			    	'sectionid'=>$sectionid
    			    ));
    			}
    			else
    			{
    			    $info[] = '发布'.$type.'区块失败';
    			    $ok = 0;
    			}
	        }
	        @unlink($tpl);
	        $this->template->set_dir($orig_dir);
	        if ($ok)
	        {
	            $data['published'] = TIME;
	        }
	    }
	    if (!$this->history->add(array('sectionid'=>$sectionid, 'data'=>$section['data'])))
		{
		    $info[] = '记录生成失败，未保存本次内容';
		    return array('state'=>false,'error'=>implode('，',$info));
		}
		$data['rows'] = count($d);
		$data['data'] = $this->_array_to_string($d);
	    if ($this->section->edit($sectionid, $data))
	    {
	        $info[] = '保存成功';
	        $this->log->add(array('action'=>'edit', 'sectionid'=>$sectionid));
			$this->section_url->save($sectionid, $this->_string_to_array($data['data']));	// 保存/修改 URL地址
	    }
	    else
	    {
	        $info[] = '保存失败';
	        $ok = 0;
	    }
	    $info = implode('，',$info);
	    return $ok ? array('state'=>true,'info'=>$info)
	               : array('state'=>false,'error'=>$info);
	}
	protected function _propertyHtmlData()
	{
		$data = array(
	       'width'=>$_POST['width'],
	       'description'=>$_POST['description']
	    );
		if (!empty($_POST['name']))
		{
			$data['name'] = $_POST['name'];
		}
		/*if (!empty($_POST['data']))
		{
			$data['origdata'] = $_POST['data'];
		}*/
	    return $data;
	}
	protected function _propertyAutoData()
	{
	    $data = array(
	       'width'=>$_POST['width'],
	       'description'=>$_POST['description']
	    );
		if (!empty($_POST['name']))
		{
			$data['name'] = $_POST['name'];
		}
		/*if (!empty($_POST['data']))
		{
			$data['origdata'] = $_POST['data'];
		}*/
		$data['frequency'] = intval($_POST['frequency']);
	    if ($data['frequency'] > 0)
		{
			$data['nextupdate'] = TIME + $data['frequency'];
		}
		return $data;
	}
	protected function _propertyHandData()
	{
	    $data = array(
	       'width'=>$_POST['width'],
	       'output'=>implode(',',$_POST['output']),
	       'description'=>$_POST['description']
	    );
		if (!empty($_POST['name']))
		{
			$data['name'] = $_POST['name'];
		}
		if (!empty($_POST['data']))
		{
			$data['template'] = $_POST['data'];
		}
		return $data;
	}
	protected function _propertyFeedData()
	{
	    $data = array(
	       'width'=>$_POST['width'],
	       'url'=>$_POST['url'],
	       'description'=>$_POST['description']
	    );
		if (!empty($_POST['name']))
		{
			$data['name'] = $_POST['name'];
		}
		if (!empty($_POST['data']))
		{
			$data['template'] = $_POST['data'];
		}
	    $data['frequency'] = intval($_POST['frequency']);
	    if ($data['frequency'] > 0)
		{
			$data['nextupdate'] = TIME + $data['frequency'];
		}
		return $data;
	}
	protected function _propertyJsonData()
	{
	    $data = array(
	       'width'=>$_POST['width'],
	       'url'=>$_POST['url'],
	       'description'=>$_POST['description']
	    );
		if (!empty($_POST['name']))
		{
			$data['name'] = $_POST['name'];
		}
		if (!empty($_POST['data']))
		{
			$data['template'] = $_POST['data'];
		}
	    $data['frequency'] = intval($_POST['frequency']);
	    if ($data['frequency'] > 0)
		{
			$data['nextupdate'] = TIME + $data['frequency'];
		}
		return $data;
	}
	protected function _propertyRpcData()
	{
	    $data = array(
	       'width'=>$_POST['width'],
	       'url'=>$_POST['url'],
	       'method'=>$_POST['method'],
	       'args'=>$_POST['args'],
	       'description'=>$_POST['description']
	    );
		if (!empty($_POST['name']))
		{
			$data['name'] = $_POST['name'];
		}
		if (!empty($_POST['data']))
		{
			$data['template'] = $_POST['data'];
		}
	    $data['frequency'] = intval($_POST['frequency']);
	    if ($data['frequency'] > 0)
		{
			$data['nextupdate'] = TIME + $data['frequency'];
		}
		return $data;
	}
	protected function _handSessionFile($sectionid)
	{
        return 'section/section_'.$this->_userid.'_'.$sectionid.'.php';
	}
	protected function _buildhandItemTips($item)
	{
		$tips = array();
		if ($item['thumb'])
		{
			$thumb  = preg_match('|^http[s]?\://|i',$item['thumb']) ? $item['thumb'] : (UPLOAD_URL.$item['thumb']);
			$tips[] = '<img src="'.$thumb.'" width="100" />';
		}
		$tips[] = '标题：<span'.($item['color'] ? ' style="color:'.$item['color'].';"':'').'>'.$item['title'].'</span>';
		$tips[] = '链接：'.$item['url'];
		if ($item['subtitle'])
		{
			$tips[] = '副题：'.$item['subtitle'];
		}
		$tips[] = '时间：'.date('Y-m-d H:i:s', $item['time']);
		return implode('<br />', $tips);
	}

	/**
	 * 计划任务
     *
     * @aca 计划任务
	 */
	function cron()
	{
		@set_time_limit(600);

		$topublish = $this->section->cron_publish();
		$info = array();
		if (!empty($topublish))
		{
			foreach ($topublish as $section)
			{
				$type = strtolower($section['type']);
				$rs = $this->{'_publish'.ucfirst($type)}($section);
				if (!$rs['state'])
				{
					$info[] = $rs['error'];
				}
			}
		}
		$json = array(
			'state'=>true
		);
		if ($info)
		{
			$json['info'] = implode('<br/>', $info);
		}
		exit ($this->json->encode($json));
	}

    /**
     * 查看区块信息
     *
     * @aca 查看
     */
	public function get_section_info()
	{
		$url = $_GET['url'];
		$url && exit ($this->json->encode(array('section' => $this->section_url->get_section_url($url))));
	}

	/**
	 * 清除早期的日志
	 *
     * @aca cron 清除早期的日志
	 */
	public function  clear_early_log()
	{
		$day	= max(30, (isset($_GET['day'])) ? intval($_GET['day']) : 30);
		$where	= "created < ".(TIME - 86400 * $day);
		$rst	= true;
		$rst	&= ($this->history->delete($where) !== false);
		$rst	&= ($this->log->delete($where) !== false);
		exit ($this->json->encode(array('state'=>(bool)$rst)));
	}
}