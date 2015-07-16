<?php
class model_topic extends model implements SplSubject
{
    public $data = array(), $event, $topicid, $json;

    private $observers = array();

    function __construct()
    {
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'comment_topic';
		$this->_primary = 'topicid';
		$this->_fields = array('topicid', 'title', 'url', 'description', 'thumb', 'disabled', 'created', 'createdby', 'updated', 'updatedby', 'comments', 'comments_pend', 'url_md5');
		$this->_readonly = array('topicid');
		$this->_create_autofill = array('created'=>TIME, 'createdby'=>$this->_userid);
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array(
		'title' => array('not_empty' => array('评论话题不能为空'),
		                 'max_length' => array(80, '评论话题不得超过80字节'),
		                 ),
		'url' => array('not_empty' => array('评论话题网址不能为空'),
		               'max_length' => array(255, '评论话题网址不得超过255字节'),
		               ),
		'description' => array('max_length' => array(255, '评论话题描述不得超过255字节'),
		                 ),
		'thumb' => array('max_length' => array(100, '评论话题图片地址不得超过100字节'),
		                 ),
		'disabled' => array('integer' => array('评论开关必须是0或1'),
		                    'min' => array(0, '评论开关必须是0或1'),
		                    'max' => array(1, '评论开关必须是0或1'),
		                 ),
        );
		$this->json = & factory::json();
    }

    //添加评论话题
    public function add($title, $url, $description = null, $thumb = null, $disabled = null)
    {
		$thumb = null && $thumb = '';
    	return $this->insert(array('title'=>$title, 'url'=>$url, 'description'=>$description, 'thumb'=>$thumb, 'disabled'=>$disabled, 'url_md5'=>md5($url)));
    }

    //修改评论话题
    public function edit($topicid, $title, $url, $description = null, $thumb = null, $disabled = null)
    {
    	$topicid = intval($topicid);
    	return $this->update(array('title'=>$title, 'url'=>$url, 'description'=>$description, 'thumb'=>$thumb, 'disabled'=>$disabled), $topicid);
    }

    //删除评论话题
    public function delete($topicid)
    {
        $topicid = intval($topicid);
        return parent::delete($topicid);
    }

    //获取评论话题
    public function get($topicid)
    {
    	$topicid = intval($topicid);
    	return parent::get($topicid);
    }

    //查询热门评论话题
    public function ls_hot($page = 1, $pagesize = 20)
    {
    	return parent::page(null, '*', '`comments` DESC', $page, $pagesize);
    }

    //查询最新评论话题
    public function ls_last($page = 1, $pagesize = 20)
    {
    	return parent::page(null, '*', '`created` DESC', $page, $pagesize);
    }

    //开启评论
    public function enable($topicid)
    {
    	$topicid = intval($topicid);
    	return $this->update(array('disabled'=>0), $topicid);
    }

    //禁用评论
    public function disable($topicid)
    {
    	$topicid = intval($topicid);
    	return $this->update(array('disabled'=>1), $topicid);
    }

    //更新评论数
    public function set_comments($topicid, $comments, $comments_pend = 0)
    {
    	$topicid = intval($topicid);
    	return $this->update(array('comments'=>$comments, 'comments_pend'=>$comments_pend), $topicid);
    }

    //获取评论数
    public function get_comments($topicid)
    {
    	$topicid = intval($topicid);
    	return parent::get($topicid, '`comments`, `comments_pend`');
    }

	// 首页数据加载
	public function page($where, $order, $page, $size)
	{
		$where && $where = "WHERE $where";
		$order && $order = "ORDER BY $order";

		$sql = "SELECT * FROM #table_comment_topic
				$where $order";
		$data = $this->db->page($sql, $page, $size);
		return $this->_output($data);
	}

	/**
	 * 对get数据进行格式化
	 */
	public function _get($topicid)
	{
		return $this->_output($this->get($topicid));
	}

	/**
	 * 输出格式转换, $data是一条或多条记录
	 */
	private function _output($data)
	{
		if(!$data) return array();
		// 如果 data 是单条数据 如: array(0=>array()); 下面直接转换 $data = $data[0]
		if(!$data[0]) {
			$wei = 1;
			$data = array($data);
		}
		foreach ($data as & $r)
		{
			$r['created'] = date('Y-m-d H:i', $r['created']);
			$r['disabled'] = $r['disabled'] ? '<font color="red">已禁用</font>' : '可用启';
		}
		if($wei) $data = $data[0];
		return $data;
	}

	/**
	 * 批量操作 开启评论
	 * 
	 */
	public function _enable($tids)
	{
		$tidarr = explode(',', $tids);
		foreach($tidarr as $tids) 
		{
			$r = $this->enable($tids);
		}
		return $r;
	}

	/**
	 * 批量操作 禁用评论
	 * 
	 */
	public function _disable($tids)
	{
		$tidarr = explode(',', $tids);
		foreach($tidarr as $tids) 
		{
			$r = $this->disable($tids);
		}
		return $r;
	}	

	/**
	 * 批量操作 删除评论
	 * 
	 */
	public function _delete($tids)
	{
		$tidarr = explode(',', $tids);
		foreach($tidarr as $tids) 
		{
			$r = $this->delete($tids);
		}
		return $r;
	}

	/**
	 * 添加话题API
	 * 
	 * @param string $title 话题标题 必填且小于 80 字节
	 * @param string $url URL地址 必填且小于 255 字节
	 * @param string $description 话题描述
	 * @param string $thumb 话题缩略图
	 * @param string $disabled 话题是否可用 默认可用
	 * @param int 成功返回话题id否则返回错误信息
	 */
	public function add_topic_api($title, $url, $description, $thumb, $disabled)
	{
		$this->_check_title($title);
		$this->_check_url($url);
		$this->_check_desc($description);
		$this->_check_thumb($thumb);
		$this->_check_disabled($disabled);

		// 允许查询的字段
		$fields = 'title, url, description, 
				thumb, disabled, created, 
				createdby, updated, updatedby, comments';

		if(!$this->error)
		{
			return $this->add($title, $url, $description, $thumb, $disabled);
		}
		else
		{
			return $this->error;
		}
	}

	/**
	 * 检测话题标题并返回相关常量值
	 *
	 * 下面几个 以_check开头的作用相同
	 * @todo 可以在做更严格的检测
	 */
	private function _check_title($title)
	{
		$title = addslashes_deep($title);
		if(!$title)
		{
			$this->error = '话题不能为空';
			return false;
		}
		elseif(words_count($title) > 80)
		{
			$this->error = '话题不得超过80个字节';
			return false;
		}
		elseif(stripslashes_deep($title) != $title)
		{
			$this->error = '话题不能含有非法字符';
			return false;
		}

		$r= parent::get("title = $title", 'title');
		if($title == $r['title'])
		{
			$this->error = '该话题已存在';
			return false;
		}
	}

	private function _check_url($url)
	{
		if(!$url)
		{
			$this->error = '评论话题网址不得为空';
			return false;
		}
		elseif(!$this->_strexists($url, 'http://'))
		{
			$this->error = '网址必须为绝对地址（http://）';
			return false;
		}
		elseif(words_count($title) > 255)
		{
			$this->error = '话题网址长度不得超过255字节';
			return false;
		}
	}

	private function _check_desc($description)
	{
		if($description && words_count($description) > 255)
		{
			$this->error = '评论话题描述不得超过255字节';
			return false;
		}
	}

	private function _check_thumb($thumb)
	{
		if($thumb && words_count($thumb) > 100)
		{
			$this->error = '评论话题图片地址不得超过100字节';
			return false;
		}
	}	

	private function _check_disabled($disabled)
	{
		$expect = array(0, 1);
		if($disabled && !in_array($disabled, $expect))
		{
			$this->error = '评论开关必须是0或1';
			return false;
		}
	}

	/**
	 * 获取单条话题信息API
	 *
	 * @param int $topicid 话题ID
	 * @param mixed 存在则返回数据（JSON） 否则返回 false
	 */
	public function get_topic_api($topicid)
	{
		$this->_check_topicid($topicid);
		// 允许查询的字段
		$fields = 'title, url, description,
				thumb, disabled, created,
				createdby, updated, updatedby, comments';
		$r = parent::get($topicid, $fields);
		return $r ? exit($this->json->encode($r)) : FALSE;
	}

	/**
	 * 获取多条话题信息API
	 *
	 * @param string $topicid 话题ID '1,2,3,4,5,6,8'
	 * @param mixed 存在则返回数据（JSON） 否则返回 false
	 */
	public function gets_topic_api($topicid)
	{
		$this->_check_topicid($topicid);

		// 允许查询的字段
		$fields = 'title, url, description,
				thumb, disabled, created,
				createdby, updated, updatedby, comments';

		$r = parent::select($topicid, $fields);
		return $r ? exit($this->json->encode($r)) : FALSE;
	}

	private function _check_topicid($topicid)
	{
		if(!is_numeric($topicid) || empty($topicid))
		{
			$this->error = '话题ID不能为空，且必须为数字';
			return false;
		}
	}
	/**
	 * 查找是否包含指定字符
	 *
	 * @param string $string $haystack 
	 * @param string $find $needle
	 * @notice 定义为public 可供外部使用
	 */
	public function _strexists($string, $find) {
		return !(strpos($string, $find) === FALSE);
	}

	/**
	 * 格式化 field 字段和 value，并组成一个字符串
	 *
	 * @param array $array  array('created' => 'DESC', 'time' => 'ASC');
	 * @param $exp 分割符
	 * @return string	`created` DESC, 'time' ASC
	 * @notice comment模型里面调用了这个方法 所以定义为 public 
	 */
	public function _implode_field_value($array, $exp = ',')
	{
		$str = $comma = '';
		foreach ($array as $k => $v) {
			$str .= $comma."`$k` $v";
			$comma = $exp;
		}
		return $str;
	}

	public function attach(SplObserver $observer)
	{
		$this->observers[] = $observer;
	}

	public function detach(SplObserver $observer)
	{
		if($index = array_search($observer, $this->observers, true)) unset($this->observers[$index]);
	}

	public function notify()
	{
		foreach ($this->observers as $observer)
		{
			$observer->update($this);
		}
	}
}