<?php
class controller_api extends dms_controller_abstract
{
	private $logger, $post;

	function __construct(&$app)
	{
		parent::__construct($app);
		$this->log	= loader::model('dms_log');
		$this->_verify();
		$this->post	= input();
	}
	
	/**
	 * 添加文章
	 *
	 * @param string title
	 * @param string source
	 * @param string author
	 * @param string description
	 * @param string content
	 * @param string expand
	 * @param string tags
	 * @param int force 标题重复时是否强制添加
 	 * @return 
	 */
	public function article_add()
	{
		$article	= loader::model('dms_article');
		$data		= $article->add($this->post);
		if ($data && $data['state'])
		{
			$this->log->set('article_add', 'article', $data['data'], $this->post);
			exit($this->json->encode($data));
		}
		else
		{
			$this->log->set('article_add', 'article', 0, $this->post);
			exit($this->json->encode($data));
		}
	}

	/**
	 * 编辑文章
	 *
	 * @param int id
	 * @param string title
	 * @param string source
	 * @param string author
	 * @param string description
	 * @param string content
	 * @param string expand
	 * @param string tags
	 * @return 
	 */
	public function article_edit()
	{
		$article	= loader::model('dms_article');
		$a		= $article->get($this->post);
		$post	= $this->post;
		if (!$a['state'])
		{
			exit($this->json->encode(array('state'=>false, 'error'=>'文章不存在')));
		}
		$this->_permission($a['data']['appid'], 2);
		$post['old_tags']	= $a['old_tags'];
		$data		= $article->edit($post);
		$this->log->set('article_edit', 'article', $post['id'], $this->post);
		exit($this->json->encode($data));
	}

	/**
	 * 获取单篇文字内容
	 *
	 * @param int id
	 * @return 
	 */
	public function article_get()
	{
		$article	= loader::model('dms_article');
		$data		= $article->get($this->post);
		$this->log->set('article_get', 'article', $this->post['id'], $this->post);
		if ($data && $data['state'])
		{
			$this->_permission($data['data']['appid'], 1);
			exit($this->json->encode($data));
		}
		else
		{
			exit($this->json->encode($data));
		}
	}

	/**
	 * 删除文章
	 *
	 * @param id
	 * @return 
	 */
	 public function article_delete()
	 {
		$article	= loader::model('dms_article');
		$post	= $this->post;
		$a		= $article->get($post);
		if (!$a['state'])
		{
			exit($this->json->encode(array('state'=>false, 'error'=>'文章不存在')));
		}
		$this->_permission($a['data']['appid'], 4);
		$post['tags']	= $a['tags'];
		if ($article->delete($post))
		{
			$this->log->set('article_delete', 'article', $post['id'], $this->post);
			exit($this->json->encode(array('state'=>true, 'data'=>'删除成功')));
		}
		else
		{
			$this->log->set('article_delete', 'article', $post['id'], $this->post);
			exit($this->json->encode(array('state'=>false, 'error'=>'删除失败')));
		}
	 }

	/**
	 * 根据条件分页获取多篇文章
	 *
	 * @param string title
	 * @param string source
	 * @param string author
	 * @param string description
	 * @param string content
	 * @param int createtime_start
	 * @param int createtime_end
	 * @param int updatetime_start
	 * @param int updatetime_end
	 * @param string tags
	 * @param int page
	 * @param int pagesize
	 * @return 
	 */
	public function article_query()
	{
		$search	= loader::model('dms_search_article');
		$data = $search->page($this->post, 'article');
		$this->log->set('article_query', 'article', 0, $this->post);
		exit($this->json->encode($data));
	}

	/**
	 * 添加图片
	 *
	 * @param string title
	 * @param string source
	 * @param string author
	 * @param string description
	 * @param string content
	 * @param string expand
	 * @param string tags
	 * @param string url
	 * @return 
	 */
	public function picture_add()
	{
		$picture	= loader::model('dms_picture');
		$data		= $picture->add($this->post);
		if ($data['state'])
		{
			$this->log->set('picture_add', 'picture', $data['data'], $this->post);
			exit($this->json->encode(array('state' => 1, 'data' => $data)));
		}
		else
		{
			$this->log->set('picture_add', 'picture', 0, $this->post);
			exit($this->json->encode(array('state' => 0, 'error' => $data['data'])));
		}
	}

	/**
	 * 修改图片
	 *
	 * @param it id
	 * @param string title
	 * @param string source
	 * @param string author
	 * @param string description
	 * @param string content
	 * @param string expand
	 * @param string tags
	 * @param string url
	 * @return 
	 */
	public function picture_edit()
	{
		$picture	= loader::model('dms_picture');
		$post		= $this->post;
		$p			= $picture->get($post);
		if (!$p['state'])
		{
			exit($this->json->encode(array('state'=>false, 'error'=>'文章不存在')));
		}
		$this->_permission($p['data']['appid'], 2);
		$post['old_tags']	= $p['old_tags'];
		$data	= $picture->edit($post);
		$this->log->set('picture_edit', 'picture', $post['id'], $this->post);
		exit($this->json->encode($data));
	}

	/**
	 * 根据ID获取单张图片
	 *
	 * @param int id
	 * @return 
	 */
	public function picture_get()
	{
		$picture	= loader::model('dms_picture');
		if ($data = $picture->get($this->post))
		{
			$this->log->set('picture_get', 'picture', $this->post['id'], $this->post);
			$this->_permission($data['appid'], 1);
			exit($this->json->encode(array('state' => 1, 'data' => $data)));
		}
		else
		{
			$this->log->set('picture_get', 'picture', $this->post['id'], $this->post);
			exit($this->json->encode(array('state' => 0, 'error' => '读取图片失败')));
		}
	}

	/**
	 * 删除图片
	 *
	 * @param int id
	 * @return 
	 */
	 public function picture_delete()
	 {
		$picture	= loader::model('dms_picture');
		$post		= $this->post;
		$p			= $picture->get($post);
		if (!$p)
		{
			exit($this->json->encode(array('state'=>false, 'error'=>'图片不存在')));
		}
		$this->_permission($p['data']['appid'], 4);
		if ($picture->delete($post))
		{
			$this->log->set('picture_delete', 'picture', $post['id'], $this->post);
			exit($this->json->encode(array('state'=>true, 'data'=>'删除成功')));
		}
		else
		{
			$this->log->set('picture_delete', 'picture', $post['id'], $this->post);
			exit($this->json->encode(array('state'=>false, 'error'=>'删除失败')));
		}
	 }

	/**
	 * 根据条件分页获取多张图片
	 *
	 * @param string title
	 * @param string source
	 * @param string author
	 * @param string description
	 * @param string content
	 * @param int createtime_start
	 * @param int createtime_end
	 * @param int updatetime_start
	 * @param int updatetime_end
	 * @param string tags
	 * @param int page
	 * @param int pagesize
	 * @return 
	 */
	public function picture_query()
	{
		$search	= loader::model('dms_search_picture');
		$data = $search->page($this->post, 'picture');
		$this->log->set('picture_query', 'picture', 0, $this->post);
		exit($this->json->encode($data));
	}

	/**
	 * 添加组图
	 *
	 * @param string title
	 * @param string source
	 * @param string author
	 * @param string description
	 * @param string content
	 * @param string expand
	 * @param string tags
	 * @param array pictures
	 * @return 
	 */
	public function picture_group_add()
	{
		$picture_group	= loader::model('dms_picture_group');
		if ($data = $picture_group->add($this->post))
		{
			$this->log->set('picture_group_add', 'picture_group', $data, $this->post);
			exit($this->json->encode(array('state' => 1, 'data' => $data)));
		}
		else
		{
			$this->log->set('picture_group_add', 'picture_group', 0, $this->post);
			exit($this->json->encode(array('state' => 0, 'error' => '插入图组失败')));
		}
	}

	/**
	 * 根据ID获取单个组图
	 *
	 * @param
	 * @return 
	 */
	public function picture_group_get()
	{
		$picture_group	= loader::model('dms_picture_group');
		if ($data = $picture_group->get($this->post))
		{
			$this->log->set('picture_group_get', 'picture_group', $this->post['id'], $this->post);
			exit($this->json->encode(array('state' => 1, 'data' => $data)));
		}
		else
		{
			$this->log->set('picture_group_get', 'picture_group', $this->post['id'], $this->post);
			exit($this->json->encode(array('state' => 0, 'error' => '读取图片失败')));
		}
	}

	/**
	 * 根据条件分页获取多个组图
	 *
	 * @param
	 * @return 
	 */
	public function picture_group_query()
	{
		$search	= loader::model('dms_search_picture_group');
		$data = $search->page($this->post, 'picture_group');
		$this->log->set('picture_group_query', 'picture_group', 0, $this->post);
		exit($this->json->encode($data));
	}

	/**
	 * 添加附件
	 *
	 * @param string title
	 * @param string source
	 * @param string author
	 * @param string description
	 * @param string expand
	 * @param string tags
	 * @param string url
	 * @return 
	 */
	public function attachment_add()
	{
		$attachment	= loader::model('dms_attachment');
		if ($data = $attachment->add($this->post))
		{
			$this->log->set('attachment_add', 'attachment', $data, $this->post);
			exit($this->json->encode(array('state' => 1, 'data' => $data)));
		}
		else
		{
			$this->log->set('attachment_add', 'attachment', 0, $this->post);
			exit($this->json->encode(array('state' => 0, 'error' => '插入附件失败')));
		}
		
	}

	/**
	 * 根据ID获得单个附件(似乎没什么用)
	 *
	 * @param id 附件id
	 * @return 
	 */
	public function attachment_get()
	{
		$attachment	= loader::model('dms_attachment');
		$this->log->set('attachment_get', 'attachment', value($this->post, 'id'), $this->post);
		if ($data = $attachment->get(value($this->post,'id',0)))
		{
			$this->_permission($data['appid'], 1);
			exit($this->json->encode(array('state'=> 1, 'data'=>$data)));
		}
		else
		{
			exit($this->json->encode(array('state'=> 0, 'error'=>$attachment->error())));
		}
	}

	/**
	 * 根据条件查询附件
	 *
	 * @param string title
	 * @param string source
	 * @param string author
	 * @param string description
	 * @param string content
	 * @param int createtime_start
	 * @param int createtime_end
	 * @param int updatetime_start
	 * @param int updatetime_end
	 * @param string tags
	 * @param int page
	 * @param int pagesize
	 * @param int minsize
	 * @param int maxsize
	 * @param string ext
	 * @return 
	 */
	public function attachment_query()
	{
		$search	= loader::model('dms_search_attachment');
		$post = $this->post;
		$post['status'] = 1;
		$data = $search->page($this->post, 'attachment');
		$this->log->set('attachment_query', 'attachment', 0, $this->post);
		exit($this->json->encode($data));
	}

	/**
	 * 删除一条附件记录
	 *
	 * @param int id
	 * @return
	 */
	public function attachment_delete()
	{
		$attachment	= loader::model('dms_attachment');
		
		if (!$a = $attachment->get(value($this->post, 'id')))
		{
			$this->log->set('attachment_delete', 'attachment', 0, $this->post);
			exit($this->json->encode(array('state'=> 0, 'error'=>'附件不存在')));
		}
		$this->_permission($a['appid'], 4);
		$post = $this->post;
		$post['tags'] = $a['tags'];
		if ($data = $attachment->remove($post))
		{
			$this->log->set('attachment_delete', 'attachment', $post['id'], $this->post);
			exit($this->json->encode(array('state'=> 1, 'data'=>$data)));
		}
		else
		{
			$this->log->set('attachment_delete', 'attachment', 0, $this->post);
			exit($this->json->encode(array('state'=> 0, 'error'=>$attachment->error())));
		}
	}

	/**
	 * 将日志返回给客户端
	 *
	 * @param string app
	 * @param int appid app与appid只可以出现一个,app会覆盖appid的值
	 * @param string operator
	 * @param int modelid
	 * @param string model
	 * @param int target
	 * @param string action
	 * @param int time_start
	 * @param int time_end
	 * @param int page
	 * @param int pagesize
	 * @param string/int ip
	 * @return 
	 */
	public function log_query()
	{
		$log	= loader::model('dms_log');
		$data	= $log->search($this->post);
		exit($this->json->encode($data));
	}

	/**
	 * 查询单条日志
	 *
	 * @param int id
	 * @return 
	 */
	public function log_get()
	{
		$log	= loader::model('dms_log');
		if ($data = $log->get(value($this->post, 'id')))
		{
			exit($this->json->encode(array('state'=>true, 'data'=>$data)));
		}
		else
		{
			exit($this->json->encode(array('state'=>false, 'error'=>'日志不存在')));
		}
	}

	/**
	 * 设置引用标识
	 *
	 * @param int target
	 * @param int model
	 * @param int modelid model与modelid只可以出现一个,model会覆盖modelid的值
	 * @param int time
	 * @param string operator
	 * @param string status
	 * @return 
	 */
	public function quote_set()
	{
		$quote	= loader::model('dms_quote');
		$post	= $this->post;
		$post['operator']	= $_GET['operator'];
		if ($quote->set($post))
		{
			$result	= array('state'=>true, 'data' => '设置引用成功');
		}
		else
		{
			$result	= array('state'=>false, 'error' => '设置引用失败');
		}
		$this->log->set('quote_set', $post['model'], $post['target'], $this->post);
		exit($this->json->encode($result));
	}

	/**
	 * 获得引用标识
	 *
	 * @param array quotes 多条查询数据的情况
	 * @param int target target字段
	 * @param string model
	 * @param int time_start
	 * @param int time_end
	 * @param int page
	 * @param int pagesize
	 * @param int modelid model与modelid只可以出现一个,model会覆盖modelid的值
	 * @return 
	 */
	public function quote_get()
	{
		$quote	= loader::model('dms_quote');
		$post	= input();
		$data	= $quote->get($post);
		if ($data)
		{
			$data['state']	= true;
			exit($this->json->encode($data));
		}
		else
		{
			exit($this->json->encode(array('state'=>false, 'data'=>'获取标识失败', 'error'=>$quote->error())));
		}
	}

	/**
	 * 更改引用标识
	 *
	 * @param string model
	 * @param int modelid
	 * @param int target
	 * @param string status
	 * @param int disable
	 * @param int time
	 * @return 
	 */
	public function quote_update()
	{
		$quote	= loader::model('dms_quote');
		if ($data = $quote->update($this->post))
		{
			$result	= array('state'=>true, 'data'=>'修改成功');
		}
		else
		{
			$result	= array('state'=>false, 'error'=>'修改失败');
		}
		$this->log->set('quote_update', $this->post['model'], $this->post['target'], $this->post);
		exit($this->json->encode($result));
	}

	/**
	 * 用于测试是否可以正常通讯
	 */
	public function test()
	{
		exit($this->json->encode(array('state'=>true)));
	}

	/**
	 * 身份验证
	 */
	private function _verify()
	{
		if (!get_setting('status'))
		{
			exit($this->json->encode(array('state'=>false, 'error'=>'数据中心已关闭')));
		}
		$_GET['token'] || exit($this->json->encode(array('state' => 0, 'error' => '缺少参数')));
		$apps	= loader::model('dms_app');
		$result	= $apps->verify($_GET['token'], $_SERVER['REQUEST_URI'], IP);
		if ($result['state'])
		{
			$_GET['appid']	= $result['data']['appid'];
			if ($this->app->action != 'test' && !$apps->priv_check($_GET['appid'], $this->app->action))
			{
				exit($this->json->encode(array('state' => 0, 'error'=> '没有'.$this->app->action.'动作的权限')));
			}
		}
		else
		{
			exit($this->json->encode(array('state' => 0, 'error' => $result['data'])));
		}
	}

	/**
	 * 权限验证
	 *
	 * @param int source
	 * @param int target
	 * @param int priv
	 * @return 
	 */
	private function _permission($target, $priv)
	{
		$priv	= loader::model('dms_priv');
		if (!$priv->verify($_GET['appid'], $target, $priv))
		{
			exit($this->json->encode(array('state' => 0, 'error' => '无权限获取该资源')));
		}
	}
}